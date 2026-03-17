<?php

namespace App\Services;

use App\Models\Incident;
use App\Models\Student;
use Illuminate\Support\Facades\View;

/**
 * Report Service for generating HTML-based printable reports
 * Uses browser's native print functionality for PDF generation
 * This approach avoids heavy PDF library dependencies while maintaining simplicity
 */
class ReportService
{
    /**
     * Generate incident summary report data
     */
    public function generateIncidentReport(Incident $incident): array
    {
        $incident->load([
            'students.parents',
            'category',
            'clause',
            'reporter.role',
            'approvals.approver.role',
            'notifications.parent',
        ]);

        return [
            'incident' => $incident,
            'generated_at' => now(),
            'generated_by' => auth()->user(),
            'school_info' => $this->getSchoolInfo(),
        ];
    }

    /**
     * Generate student discipline record data
     */
    public function generateStudentRecord(Student $student): array
    {
        $student->load([
            'incidents' => function ($query) {
                $query->orderBy('incident_date', 'desc')
                    ->with(['category', 'clause', 'reporter']);
            },
            'attendanceRecords' => function ($query) {
                $query->whereIn('status', ['absent', 'tardy'])
                    ->orderBy('date', 'desc')
                    ->limit(50);
            },
            'adviser',
            'parents',
        ]);

        // Calculate statistics
        $thisYear = now()->startOfYear();
        $incidentStats = [
            'total_incidents' => $student->incidents->count(),
            'incidents_this_year' => $student->incidents->where('incident_date', '>=', $thisYear)->count(),
            'by_severity' => $student->incidents
                ->groupBy(fn($i) => $i->category?->severity ?? 'unknown')
                ->map->count(),
        ];

        $attendanceStats = [
            'total_absences' => $student->attendanceRecords->where('status', 'absent')->count(),
            'total_tardies' => $student->attendanceRecords->where('status', 'tardy')->count(),
        ];

        return [
            'student' => $student,
            'incident_stats' => $incidentStats,
            'attendance_stats' => $attendanceStats,
            'generated_at' => now(),
            'generated_by' => auth()->user(),
            'school_info' => $this->getSchoolInfo(),
        ];
    }

    /**
     * Generate monthly summary report data
     */
    public function generateMonthlySummary(int $year, int $month): array
    {
        $startDate = now()->setYear($year)->setMonth($month)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();

        $incidents = Incident::whereBetween('incident_date', [$startDate, $endDate])
            ->with(['students', 'category', 'reporter'])
            ->orderBy('incident_date', 'desc')
            ->get();

        // Group by category
        $byCategory = $incidents->groupBy(fn($i) => $i->category?->name ?? 'Uncategorized')
            ->map->count()
            ->sortDesc();

        // Group by grade level
        $byGradeLevel = [];
        foreach ($incidents as $incident) {
            foreach ($incident->students as $student) {
                $grade = "Grade {$student->grade_level}";
                $byGradeLevel[$grade] = ($byGradeLevel[$grade] ?? 0) + 1;
            }
        }
        arsort($byGradeLevel);

        // Group by status
        $byStatus = $incidents->groupBy('status')->map->count();

        return [
            'period' => [
                'year' => $year,
                'month' => $month,
                'month_name' => $startDate->format('F'),
                'start_date' => $startDate,
                'end_date' => $endDate,
            ],
            'summary' => [
                'total_incidents' => $incidents->count(),
                'by_category' => $byCategory,
                'by_grade_level' => $byGradeLevel,
                'by_status' => $byStatus,
            ],
            'incidents' => $incidents,
            'generated_at' => now(),
            'generated_by' => auth()->user(),
            'school_info' => $this->getSchoolInfo(),
        ];
    }

    /**
     * Generate quarterly analytics report data
     */
    public function generateQuarterlyReport(int $year, int $quarter): array
    {
        $startMonth = (($quarter - 1) * 3) + 1;
        $startDate = now()->setYear($year)->setMonth($startMonth)->startOfMonth();
        $endDate = $startDate->copy()->addMonths(2)->endOfMonth();

        $incidents = Incident::whereBetween('incident_date', [$startDate, $endDate])
            ->with(['students', 'category', 'clause', 'reporter'])
            ->get();

        // Monthly breakdown
        $monthlyBreakdown = [];
        for ($m = 0; $m < 3; $m++) {
            $monthStart = $startDate->copy()->addMonths($m)->startOfMonth();
            $monthEnd = $monthStart->copy()->endOfMonth();
            $monthIncidents = $incidents->filter(function ($i) use ($monthStart, $monthEnd) {
                return $i->incident_date >= $monthStart && $i->incident_date <= $monthEnd;
            });
            $monthlyBreakdown[$monthStart->format('F')] = $monthIncidents->count();
        }

        // Top violations
        $topViolations = $incidents->groupBy(fn($i) => $i->category?->name ?? 'Unknown')
            ->map->count()
            ->sortDesc()
            ->take(5);

        // Grade level analysis
        $gradeAnalysis = [];
        foreach ($incidents as $incident) {
            foreach ($incident->students as $student) {
                $key = "Grade {$student->grade_level}";
                if (!isset($gradeAnalysis[$key])) {
                    $gradeAnalysis[$key] = ['count' => 0, 'students' => []];
                }
                $gradeAnalysis[$key]['count']++;
                $gradeAnalysis[$key]['students'][] = $student->id;
            }
        }
        foreach ($gradeAnalysis as &$data) {
            $data['unique_students'] = count(array_unique($data['students']));
            unset($data['students']);
        }

        return [
            'period' => [
                'year' => $year,
                'quarter' => $quarter,
                'quarter_label' => "Q{$quarter}",
                'start_date' => $startDate,
                'end_date' => $endDate,
            ],
            'summary' => [
                'total_incidents' => $incidents->count(),
                'monthly_breakdown' => $monthlyBreakdown,
                'top_violations' => $topViolations,
                'grade_analysis' => $gradeAnalysis,
                'resolved_count' => $incidents->whereIn('status', ['approved', 'closed'])->count(),
                'pending_count' => $incidents->whereIn('status', ['reported', 'under_review', 'pending_approval'])->count(),
            ],
            'generated_at' => now(),
            'generated_by' => auth()->user(),
            'school_info' => $this->getSchoolInfo(),
        ];
    }

    /**
     * Get school information for report headers
     */
    protected function getSchoolInfo(): array
    {
        return [
            'name' => 'St. Paul University Philippines',
            'unit' => 'Basic Education Unit (BEU)',
            'address' => 'Mabini Street, Tuguegarao City, Cagayan 3500',
            'contact' => 'Tel: (078) 844-1839',
            'logo' => public_path('images/spup-logo.png'),
        ];
    }

    /**
     * Render report to HTML (for browser print/PDF)
     */
    public function renderToHtml(string $view, array $data): string
    {
        return View::make($view, $data)->render();
    }
}
