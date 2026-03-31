<?php

namespace App\Services;

use App\Models\Incident;
use App\Models\Student;
use App\Models\AttendanceRecord;
use App\Models\ViolationCategory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class AnalyticsService
{
    /**
     * Cache duration in minutes
     */
    protected int $cacheDuration = 15;

    /**
     * Get comprehensive dashboard statistics
     */
    public function getDashboardStats(): array
    {
        return Cache::remember('dashboard_stats', $this->cacheDuration * 60, function () {
            return [
                'overview' => $this->getOverviewStats(),
                'trends' => $this->getIncidentTrends(),
                'breakdown' => $this->getCategoryBreakdown(),
                'performance' => $this->getProcessingPerformance(),
            ];
        });
    }

    /**
     * Get overview statistics (totals, counts)
     */
    public function getOverviewStats(): array
    {
        $now = now();
        $thisMonth = $now->copy()->startOfMonth();
        $lastMonth = $now->copy()->subMonth()->startOfMonth();
        $thisYear = $now->copy()->startOfYear();

        // Current month incidents
        $thisMonthIncidents = Incident::whereBetween('incident_date', [$thisMonth, $now])->count();
        
        // Last month incidents (for comparison)
        $lastMonthIncidents = Incident::whereBetween('incident_date', [$lastMonth, $thisMonth])->count();
        
        // Calculate percentage change
        $monthlyChange = $lastMonthIncidents > 0 
            ? round((($thisMonthIncidents - $lastMonthIncidents) / $lastMonthIncidents) * 100, 1)
            : 0;

        return [
            'total_incidents_this_month' => $thisMonthIncidents,
            'total_incidents_last_month' => $lastMonthIncidents,
            'monthly_change_percent' => $monthlyChange,
            'total_incidents_this_year' => Incident::whereBetween('incident_date', [$thisYear, $now])->count(),
            'pending_approval' => Incident::where('status', 'pending_approval')->count(),
            'under_review' => Incident::where('status', 'under_review')->count(),
            'approved_this_month' => Incident::where('status', 'approved')
                ->whereBetween('updated_at', [$thisMonth, $now])->count(),
            'archived_total' => Incident::where('status', 'closed')->count(),
            'active_students' => Student::where('status', 'active')->count(),
            'students_with_incidents' => Student::whereHas('incidents', function ($q) use ($thisYear, $now) {
                $q->whereBetween('incident_date', [$thisYear, $now]);
            })->count(),
            'at_risk_students' => $this->getAtRiskStudentsCount(),
            'repeat_offenders' => $this->getRepeatOffendersCount(),
        ];
    }

    /**
     * Get incident trends over time
     */
    public function getIncidentTrends(int $months = 6): array
    {
        $trends = [];
        $now = now();

        for ($i = $months - 1; $i >= 0; $i--) {
            $date = $now->copy()->subMonths($i);
            $startOfMonth = $date->copy()->startOfMonth();
            $endOfMonth = $date->copy()->endOfMonth();

            $count = Incident::whereBetween('incident_date', [$startOfMonth, $endOfMonth])->count();
            
            $trends[] = [
                'month' => $date->format('M'),
                'year' => $date->format('Y'),
                'label' => $date->format('M Y'),
                'count' => $count,
            ];
        }

        return $trends;
    }

    /**
     * Get weekly trends for current month
     */
    public function getWeeklyTrends(): array
    {
        $trends = [];
        $now = now();
        $startOfMonth = $now->copy()->startOfMonth();

        for ($week = 1; $week <= 4; $week++) {
            $weekStart = $startOfMonth->copy()->addWeeks($week - 1);
            $weekEnd = $weekStart->copy()->addDays(6)->endOfDay();

            if ($weekStart->gt($now)) break;

            $count = Incident::whereBetween('incident_date', [$weekStart, min($weekEnd, $now)])->count();

            $trends[] = [
                'week' => "Week {$week}",
                'count' => $count,
                'start' => $weekStart->format('M d'),
                'end' => $weekEnd->format('M d'),
            ];
        }

        return $trends;
    }

    /**
     * Get violation category breakdown
     */
    public function getCategoryBreakdown(): array
    {
        $thisYear = now()->startOfYear();

        return ViolationCategory::withCount(['incidents' => function ($query) use ($thisYear) {
                $query->where('incident_date', '>=', $thisYear);
            }])
            ->orderByDesc('incidents_count')
            ->get()
            ->map(function ($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'severity' => $category->severity,
                    'count' => $category->incidents_count,
                ];
            })
            ->toArray();
    }

    /**
     * Get grade level breakdown
     */
    public function getGradeLevelBreakdown(): array
    {
        $thisYear = now()->startOfYear();

        return DB::table('incident_students')
            ->join('students', 'incident_students.student_id', '=', 'students.id')
            ->join('incidents', 'incident_students.incident_id', '=', 'incidents.id')
            ->where('incidents.incident_date', '>=', $thisYear)
            ->select('students.grade_level', DB::raw('COUNT(DISTINCT incidents.id) as count'))
            ->groupBy('students.grade_level')
            ->orderBy('students.grade_level')
            ->get()
            ->map(function ($item) {
                return [
                    'grade_level' => $item->grade_level,
                    'label' => "Grade {$item->grade_level}",
                    'count' => $item->count,
                ];
            })
            ->toArray();
    }

    /**
     * Get section breakdown
     */
    public function getSectionBreakdown(?int $gradeLevel = null): array
    {
        $thisYear = now()->startOfYear();

        $query = DB::table('incident_students')
            ->join('students', 'incident_students.student_id', '=', 'students.id')
            ->join('incidents', 'incident_students.incident_id', '=', 'incidents.id')
            ->where('incidents.incident_date', '>=', $thisYear);

        if ($gradeLevel) {
            $query->where('students.grade_level', $gradeLevel);
        }

        return $query->select(
                'students.grade_level',
                'students.section',
                DB::raw('COUNT(DISTINCT incidents.id) as count')
            )
            ->groupBy('students.grade_level', 'students.section')
            ->orderByDesc('count')
            ->limit(10)
            ->get()
            ->map(function ($item) {
                return [
                    'grade_level' => $item->grade_level,
                    'section' => $item->section,
                    'label' => "G{$item->grade_level} - {$item->section}",
                    'count' => $item->count,
                ];
            })
            ->toArray();
    }

    /**
     * Get processing performance metrics
     */
    public function getProcessingPerformance(): array
    {
        $thisMonth = now()->startOfMonth();

        // Calculate average processing time (from reported to approved)
        $approvedIncidents = Incident::where('status', 'approved')
            ->where('updated_at', '>=', $thisMonth)
            ->get();

        $totalProcessingDays = 0;
        $processedCount = 0;

        foreach ($approvedIncidents as $incident) {
            $days = $incident->created_at->diffInDays($incident->updated_at);
            $totalProcessingDays += $days;
            $processedCount++;
        }

        $avgProcessingDays = $processedCount > 0 
            ? round($totalProcessingDays / $processedCount, 1) 
            : 0;

        // Status distribution
        $statusDistribution = Incident::where('incident_date', '>=', $thisMonth)
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        return [
            'avg_processing_days' => $avgProcessingDays,
            'processed_this_month' => $processedCount,
            'status_distribution' => $statusDistribution,
            'completion_rate' => $this->calculateCompletionRate(),
        ];
    }

    /**
     * Calculate incident completion rate
     */
    protected function calculateCompletionRate(): float
    {
        $thisMonth = now()->startOfMonth();
        
        $total = Incident::where('incident_date', '>=', $thisMonth)->count();
        $completed = Incident::whereIn('status', ['approved', 'closed'])
            ->where('incident_date', '>=', $thisMonth)
            ->count();

        return $total > 0 ? round(($completed / $total) * 100, 1) : 0;
    }

    /**
     * Get at-risk students count
     */
    public function getAtRiskStudentsCount(): int
    {
        $year = now()->year;

        // Get students with 10+ absences this year
        $highAbsences = DB::table('attendance_records')
            ->select('student_id')
            ->where('status', 'absent')
            ->whereYear('date', $year)
            ->groupBy('student_id')
            ->havingRaw('COUNT(*) >= 10')
            ->pluck('student_id');

        // Get students with 15+ tardies this year
        $highTardies = DB::table('attendance_records')
            ->select('student_id')
            ->where('status', 'tardy')
            ->whereYear('date', $year)
            ->groupBy('student_id')
            ->havingRaw('COUNT(*) >= 15')
            ->pluck('student_id');

        // Get students with 3+ incidents this year
        $multipleIncidents = DB::table('incident_students')
            ->join('incidents', 'incident_students.incident_id', '=', 'incidents.id')
            ->select('incident_students.student_id')
            ->whereYear('incidents.incident_date', $year)
            ->groupBy('incident_students.student_id')
            ->havingRaw('COUNT(*) >= 3')
            ->pluck('student_id');

        // Combine all at-risk student IDs
        $atRiskIds = $highAbsences
            ->merge($highTardies)
            ->merge($multipleIncidents)
            ->unique();

        return Student::where('status', 'active')
            ->whereIn('id', $atRiskIds)
            ->count();
    }

    /**
     * Get repeat offenders count (3+ incidents this year)
     */
    public function getRepeatOffendersCount(): int
    {
        $thisYear = now()->startOfYear();

        return DB::table('incident_students')
            ->join('incidents', 'incident_students.incident_id', '=', 'incidents.id')
            ->where('incidents.incident_date', '>=', $thisYear)
            ->select('incident_students.student_id', DB::raw('COUNT(*) as incident_count'))
            ->groupBy('incident_students.student_id')
            ->having('incident_count', '>=', 3)
            ->count();
    }

    /**
     * Get top offenders list
     */
    public function getTopOffenders(int $limit = 10): array
    {
        $thisYear = now()->startOfYear();

        return DB::table('incident_students')
            ->join('students', 'incident_students.student_id', '=', 'students.id')
            ->join('incidents', 'incident_students.incident_id', '=', 'incidents.id')
            ->where('incidents.incident_date', '>=', $thisYear)
            ->select(
                'students.id',
                'students.student_id',
                'students.first_name',
                'students.last_name',
                'students.grade_level',
                'students.section',
                DB::raw('COUNT(*) as incident_count')
            )
            ->groupBy(
                'students.id',
                'students.student_id',
                'students.first_name',
                'students.last_name',
                'students.grade_level',
                'students.section'
            )
            ->orderByDesc('incident_count')
            ->limit($limit)
            ->get()
            ->map(function ($student) {
                return [
                    'id' => $student->id,
                    'student_id' => $student->student_id,
                    'name' => "{$student->last_name}, {$student->first_name}",
                    'grade_section' => "Grade {$student->grade_level} - {$student->section}",
                    'incident_count' => $student->incident_count,
                ];
            })
            ->toArray();
    }

    /**
     * Get severity distribution
     */
    public function getSeverityDistribution(): array
    {
        $thisYear = now()->startOfYear();

        return Incident::join('violation_categories', 'incidents.violation_category_id', '=', 'violation_categories.id')
            ->where('incidents.incident_date', '>=', $thisYear)
            ->select('violation_categories.severity', DB::raw('COUNT(*) as count'))
            ->groupBy('violation_categories.severity')
            ->get()
            ->map(function ($item) {
                return [
                    'severity' => $item->severity,
                    'label' => ucfirst($item->severity),
                    'count' => $item->count,
                ];
            })
            ->toArray();
    }

    /**
     * Get comparative analytics (this year vs last year)
     */
    public function getComparativeAnalytics(): array
    {
        $thisYear = now()->startOfYear();
        $lastYear = now()->subYear()->startOfYear();
        $lastYearEnd = now()->subYear()->endOfYear();

        $thisYearCount = Incident::where('incident_date', '>=', $thisYear)->count();
        $lastYearCount = Incident::whereBetween('incident_date', [$lastYear, $lastYearEnd])->count();

        $yearOverYearChange = $lastYearCount > 0
            ? round((($thisYearCount - $lastYearCount) / $lastYearCount) * 100, 1)
            : 0;

        return [
            'this_year' => $thisYearCount,
            'last_year' => $lastYearCount,
            'change_percent' => $yearOverYearChange,
            'trend' => $yearOverYearChange > 0 ? 'increasing' : ($yearOverYearChange < 0 ? 'decreasing' : 'stable'),
        ];
    }

    /**
     * Get a tabular dataset of incidents for deeper analytics
     */
    public function getIncidentDataset(array $filters = [], int $limit = 50): array
    {
        $limit = max(10, min($limit, 200));

        $query = Incident::with([
                'category:id,name,severity',
                'students:id,student_id,first_name,middle_name,last_name,grade_level,section',
                'reporter:id,name',
            ])
            ->orderByDesc('incident_date');

        if (!empty($filters['grade_level'])) {
            $query->whereHas('students', function ($studentQuery) use ($filters) {
                $studentQuery->where('grade_level', $filters['grade_level']);
            });
        }

        if (!empty($filters['section'])) {
            $query->whereHas('students', function ($studentQuery) use ($filters) {
                $studentQuery->where('section', $filters['section']);
            });
        }

        if (!empty($filters['severity'])) {
            $query->whereHas('category', function ($categoryQuery) use ($filters) {
                $categoryQuery->where('severity', $filters['severity']);
            });
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['date_from'])) {
            $startDate = Carbon::parse($filters['date_from'])->startOfDay();
            $query->where('incident_date', '>=', $startDate);
        }

        if (!empty($filters['date_to'])) {
            $endDate = Carbon::parse($filters['date_to'])->endOfDay();
            $query->where('incident_date', '<=', $endDate);
        }

        $totalRecords = (clone $query)->count();

        $incidents = $query
            ->take($limit)
            ->get();

        $records = $incidents->map(function (Incident $incident) {
            $primaryStudent = $incident->students->first();
            $category = $incident->category;

            return [
                'id' => $incident->id,
                'reference' => $incident->reference_code ?? sprintf('INC-%05d', $incident->id),
                'student' => $primaryStudent?->full_name,
                'student_id' => $primaryStudent?->student_id,
                'grade_level' => $primaryStudent?->grade_level,
                'section' => $primaryStudent?->section,
                'category' => $category->name ?? 'Uncategorized',
                'severity' => $category->severity ?? 'n/a',
                'status' => $incident->status,
                'reporter' => $incident->reporter->name ?? 'Unknown',
                'narrative' => $incident->description ?? null,
                'date' => optional($incident->incident_date)->toDateString(),
                'date_label' => optional($incident->incident_date)->format('M d, Y'),
                'time_label' => optional($incident->incident_date)->format('h:i A'),
            ];
        })->toArray();

        return [
            'limit' => $limit,
            'total' => $totalRecords,
            'records' => $records,
        ];
    }

    /**
     * Generate intervention insights when no manual suggestions are queued.
     */
    public function generateInterventionInsights(int $limit = 3): array
    {
        $limit = max(1, min($limit, 5));
        $periodStart = now()->subDays(45);
        $periodEnd = now();

        $incidentHotspots = DB::table('incident_students')
            ->join('students', 'incident_students.student_id', '=', 'students.id')
            ->join('incidents', 'incident_students.incident_id', '=', 'incidents.id')
            ->leftJoin('violation_categories', 'incidents.violation_category_id', '=', 'violation_categories.id')
            ->where('incidents.incident_date', '>=', $periodStart)
            ->select(
                'students.grade_level',
                'students.section',
                DB::raw("COALESCE(violation_categories.name, 'General Incident') as category_name"),
                DB::raw("COALESCE(violation_categories.severity, 'medium') as severity"),
                DB::raw('COUNT(*) as incident_count')
            )
            ->groupBy('students.grade_level', 'students.section', 'category_name', 'severity')
            ->orderByDesc('incident_count')
            ->limit($limit * 3)
            ->get();

        $insights = [];

        foreach ($incidentHotspots as $hotspot) {
            if (count($insights) >= $limit) {
                break;
            }

            $gradeLevel = $hotspot->grade_level;
            $section = $hotspot->section;
            $scope = $this->formatScopeLabel($gradeLevel, $section);
            $action = $this->actionForSeverity($hotspot->severity);

            $insights[] = [
                'grade_level' => $gradeLevel,
                'section' => $section,
                'scope_label' => $scope,
                'incident_type' => $hotspot->category_name,
                'incident_count' => $hotspot->incident_count,
                'analysis_period_start' => $periodStart->toDateString(),
                'analysis_period_end' => $periodEnd->toDateString(),
                'suggestion' => sprintf(
                    '%s logged %d %s cases in the last 45 days. %s',
                    $scope,
                    $hotspot->incident_count,
                    $hotspot->category_name,
                    $action
                ),
            ];
        }

        if (count($insights) < $limit) {
            $attendanceHotspots = AttendanceRecord::query()
                ->join('students', 'attendance_records.student_id', '=', 'students.id')
                ->whereIn('attendance_records.status', ['absent', 'tardy'])
                ->where('attendance_records.date', '>=', $periodStart)
                ->select(
                    'students.grade_level',
                    'students.section',
                    'attendance_records.status',
                    DB::raw('COUNT(*) as total')
                )
                ->groupBy('students.grade_level', 'students.section', 'attendance_records.status')
                ->orderByDesc('total')
                ->limit($limit * 2)
                ->get();

            foreach ($attendanceHotspots as $hotspot) {
                if (count($insights) >= $limit) {
                    break;
                }

                $gradeLevel = $hotspot->grade_level;
                $section = $hotspot->section;
                $scope = $this->formatScopeLabel($gradeLevel, $section);
                $statusLabel = $hotspot->status === 'tardy' ? 'tardy arrivals' : 'absences';

                $insights[] = [
                    'grade_level' => $gradeLevel,
                    'section' => $section,
                    'scope_label' => $scope,
                    'incident_type' => ucfirst($hotspot->status) . ' pattern',
                    'incident_count' => $hotspot->total,
                    'analysis_period_start' => $periodStart->toDateString(),
                    'analysis_period_end' => $periodEnd->toDateString(),
                    'suggestion' => sprintf(
                        '%s recorded %d %s in the last 45 days. Coordinate with advisers for targeted follow-ups.',
                        $scope,
                        $hotspot->total,
                        $statusLabel
                    ),
                ];
            }
        }

        return $insights;
    }

    protected function formatScopeLabel($gradeLevel, $section): string
    {
        if ($gradeLevel) {
            $label = 'Grade ' . $gradeLevel;
            if ($section) {
                $label .= ' - ' . $section;
            }
            return $label;
        }

        return 'All Grade Levels';
    }

    protected function actionForSeverity(?string $severity): string
    {
        $severity = strtolower($severity ?? '');

        return match ($severity) {
            'high' => 'Schedule restorative conferences with guardians and prioritize multi-day monitoring.',
            'medium' => 'Coordinate guidance-led coaching blocks with advisers.',
            'low' => 'Reinforce homeroom reminders and document follow-ups.',
            default => 'Review advisory routines with guidance counselors.',
        };
    }

    /**
     * Clear analytics cache
     */
    public function clearCache(): void
    {
        Cache::forget('dashboard_stats');
    }
}
