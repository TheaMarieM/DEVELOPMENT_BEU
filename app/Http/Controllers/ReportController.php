<?php

namespace App\Http\Controllers;

use App\Models\Incident;
use App\Models\Sanction;
use App\Models\Student;
use App\Models\ViolationCategory;
use App\Services\ReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    protected ReportService $reportService;

    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    /**
     * Get school information
     */
    protected function getSchoolInfo(): array
    {
        return [
            'name' => 'St. Paul University Philippines - BEU',
            'address' => 'Mabini Street, Tuguegarao City, Cagayan 3500',
            'contact' => 'Tel: (078) 844-1839 | Email: beu@spup.edu.ph',
            'principal' => 'School Principal',
        ];
    }

    /**
     * Generate incident report (printable view)
     */
    public function incident(Incident $incident)
    {
        $incident->load(['students', 'category', 'clause', 'reporter', 'approvals.approver']);

        return view('reports.incident', [
            'incident' => $incident,
            'school' => $this->getSchoolInfo(),
            'generated_at' => now()->format('F d, Y h:i A'),
        ]);
    }

    /**
     * Generate student discipline record (printable view)
     */
    public function studentRecord(Request $request)
    {
        $studentId = $request->input('student');
        
        if (!$studentId) {
            return redirect()->route('reports.index')->with('error', 'Please select a student.');
        }

        $student = Student::with(['incidents' => function($q) {
            $q->orderBy('incident_date', 'desc');
        }])->findOrFail($studentId);

        $sanctions = Sanction::where('student_id', $student->id)
            ->orderBy('start_date', 'desc')
            ->get();

        return view('reports.student-record', [
            'student' => [
                'name' => "{$student->first_name} {$student->middle_name} {$student->last_name}",
                'student_id' => $student->student_id,
                'grade_level' => "Grade {$student->grade_level}",
                'section' => $student->section ?? 'N/A',
                'status' => $student->status ?? 'active',
            ],
            'summary' => [
                'total_incidents' => $student->incidents->count(),
                'pending_incidents' => $student->incidents->where('status', 'pending')->count(),
                'resolved_incidents' => $student->incidents->whereIn('status', ['approved', 'resolved', 'closed'])->count(),
                'active_sanctions' => $sanctions->where('status', 'active')->count(),
            ],
            'incidents' => $student->incidents,
            'sanctions' => $sanctions,
            'school' => $this->getSchoolInfo(),
            'generated_at' => now()->format('F d, Y h:i A'),
        ]);
    }

    /**
     * Generate monthly summary report
     */
    public function monthlySummary(Request $request)
    {
        $year = (int) $request->input('year', now()->year);
        $month = (int) $request->input('month', now()->month);

        $startDate = now()->setYear($year)->setMonth($month)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();

        // Get previous month for comparison
        $prevStart = $startDate->copy()->subMonth()->startOfMonth();
        $prevEnd = $prevStart->copy()->endOfMonth();
        $prevMonthCount = Incident::whereBetween('incident_date', [$prevStart, $prevEnd])->count();

        // Current month incidents
        $incidents = Incident::whereBetween('incident_date', [$startDate, $endDate])->get();

        // Severity breakdown
        $severity = [
            'high' => $incidents->where('severity', 'high')->count(),
            'medium' => $incidents->where('severity', 'medium')->count(),
            'low' => $incidents->where('severity', 'low')->count(),
        ];

        // By grade level
        $byGrade = [];
        $gradeIncidents = Incident::whereBetween('incident_date', [$startDate, $endDate])
            ->with('students')
            ->get();
        foreach ($gradeIncidents as $incident) {
            foreach ($incident->students as $student) {
                $grade = $student->grade_level;
                $byGrade[$grade] = ($byGrade[$grade] ?? 0) + 1;
            }
        }
        ksort($byGrade);

        // By category
        $byCategory = Incident::whereBetween('incident_date', [$startDate, $endDate])
            ->select('violation_category_id', DB::raw('COUNT(*) as count'))
            ->with('category')
            ->groupBy('violation_category_id')
            ->orderByDesc('count')
            ->limit(10)
            ->get()
            ->map(function($item) {
                return (object)[
                    'name' => $item->category->name ?? 'Uncategorized',
                    'count' => $item->count,
                ];
            });

        // High priority incidents
        $highPriority = Incident::whereBetween('incident_date', [$startDate, $endDate])
            ->where('severity', 'high')
            ->whereIn('status', ['pending', 'under_review'])
            ->limit(10)
            ->get();

        return view('reports.monthly-summary', [
            'year' => $year,
            'month' => $month,
            'month_name' => $startDate->format('F'),
            'stats' => [
                'total_incidents' => $incidents->count(),
                'previous_month' => $prevMonthCount,
                'pending' => $incidents->where('status', 'pending')->count(),
                'approved' => $incidents->where('status', 'approved')->count(),
                'resolved' => $incidents->whereIn('status', ['resolved', 'closed'])->count(),
            ],
            'severity' => $severity,
            'by_grade' => $byGrade,
            'by_category' => $byCategory,
            'high_priority' => $highPriority,
            'school' => $this->getSchoolInfo(),
            'generated_at' => now()->format('F d, Y h:i A'),
        ]);
    }

    /**
     * Generate quarterly report
     */
    public function quarterlyReport(Request $request)
    {
        $year = (int) $request->input('year', now()->year);
        $quarter = (int) $request->input('quarter', ceil(now()->month / 3));

        $startMonth = (($quarter - 1) * 3) + 1;
        $startDate = now()->setYear($year)->setMonth($startMonth)->startOfMonth();
        $endDate = $startDate->copy()->addMonths(2)->endOfMonth();

        // Get previous quarter for comparison
        $prevStartDate = $startDate->copy()->subMonths(3);
        $prevEndDate = $prevStartDate->copy()->addMonths(2)->endOfMonth();
        $prevQuarterIncidents = Incident::whereBetween('incident_date', [$prevStartDate, $prevEndDate])->get();

        // Current quarter incidents
        $incidents = Incident::whereBetween('incident_date', [$startDate, $endDate])
            ->with(['students', 'category'])
            ->get();

        // Severity breakdown
        $severity = [
            'high' => $incidents->where('severity', 'high')->count(),
            'medium' => $incidents->where('severity', 'medium')->count(),
            'low' => $incidents->where('severity', 'low')->count(),
        ];

        $prevQuarter = [
            'high' => $prevQuarterIncidents->where('severity', 'high')->count(),
            'medium' => $prevQuarterIncidents->where('severity', 'medium')->count(),
            'low' => $prevQuarterIncidents->where('severity', 'low')->count(),
        ];

        // Monthly breakdown
        $monthlyData = [];
        $monthNames = ['January', 'February', 'March', 'April', 'May', 'June', 
                       'July', 'August', 'September', 'October', 'November', 'December'];
        $shortNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

        for ($m = 0; $m < 3; $m++) {
            $monthStart = $startDate->copy()->addMonths($m)->startOfMonth();
            $monthEnd = $monthStart->copy()->endOfMonth();
            $monthIncidents = $incidents->filter(function($i) use ($monthStart, $monthEnd) {
                return $i->incident_date >= $monthStart && $i->incident_date <= $monthEnd;
            });
            
            $monthIndex = $startMonth + $m - 1;
            $monthlyData[] = [
                'name' => $monthNames[$monthIndex],
                'short_name' => $shortNames[$monthIndex],
                'count' => $monthIncidents->count(),
                'high' => $monthIncidents->where('severity', 'high')->count(),
                'medium' => $monthIncidents->where('severity', 'medium')->count(),
                'low' => $monthIncidents->where('severity', 'low')->count(),
            ];
        }

        // By category with severity breakdown
        $categoryData = [];
        foreach ($incidents->groupBy(fn($i) => $i->category->name ?? 'Uncategorized') as $name => $categoryIncidents) {
            $categoryData[] = [
                'name' => $name,
                'total' => $categoryIncidents->count(),
                'high' => $categoryIncidents->where('severity', 'high')->count(),
                'medium' => $categoryIncidents->where('severity', 'medium')->count(),
                'low' => $categoryIncidents->where('severity', 'low')->count(),
            ];
        }
        usort($categoryData, fn($a, $b) => $b['total'] - $a['total']);
        $categoryData = array_slice($categoryData, 0, 10);

        // By grade level
        $gradeData = [];
        $studentsByGrade = [];
        foreach ($incidents as $incident) {
            foreach ($incident->students as $student) {
                $grade = $student->grade_level;
                if (!isset($gradeData[$grade])) {
                    $gradeData[$grade] = 0;
                    $studentsByGrade[$grade] = [];
                }
                $gradeData[$grade]++;
                $studentsByGrade[$grade][] = $student->id;
            }
        }

        $byGrade = [];
        foreach ($gradeData as $level => $count) {
            $uniqueStudents = count(array_unique($studentsByGrade[$level]));
            $byGrade[] = [
                'level' => $level,
                'incidents' => $count,
                'students' => $uniqueStudents,
                'per_student' => $uniqueStudents > 0 ? $count / $uniqueStudents : 0,
            ];
        }
        usort($byGrade, fn($a, $b) => $a['level'] - $b['level']);

        // Calculate stats
        $uniqueStudents = [];
        foreach ($incidents as $incident) {
            foreach ($incident->students as $student) {
                $uniqueStudents[$student->id] = true;
            }
        }

        $resolved = $incidents->whereIn('status', ['approved', 'resolved', 'closed'])->count();
        $resolutionRate = $incidents->count() > 0 ? round(($resolved / $incidents->count()) * 100) : 0;

        // Repeat offenders (more than 2 incidents)
        $studentIncidentCounts = [];
        foreach ($incidents as $incident) {
            foreach ($incident->students as $student) {
                $studentIncidentCounts[$student->id] = ($studentIncidentCounts[$student->id] ?? 0) + 1;
            }
        }
        $repeatOffenders = count(array_filter($studentIncidentCounts, fn($c) => $c > 2));

        // Auto-generated recommendations
        $recommendations = [];
        if ($severity['high'] > $prevQuarter['high']) {
            $recommendations[] = 'High-severity incidents have increased. Consider reviewing intervention strategies.';
        }
        if ($repeatOffenders > 5) {
            $recommendations[] = "There are {$repeatOffenders} repeat offenders. Individual counseling may be needed.";
        }
        if (count($byGrade) > 0) {
            $highestGrade = collect($byGrade)->sortByDesc('incidents')->first();
            if ($highestGrade['incidents'] > ($incidents->count() * 0.3)) {
                $recommendations[] = "Grade {$highestGrade['level']} has the highest incident rate. Consider targeted programs.";
            }
        }
        if (count($categoryData) > 0 && $categoryData[0]['total'] > ($incidents->count() * 0.25)) {
            $recommendations[] = "'{$categoryData[0]['name']}' is the most common violation type. Consider preventive education.";
        }

        return view('reports.quarterly', [
            'year' => $year,
            'quarter' => $quarter,
            'period' => [
                'start' => $startDate->format('F d, Y'),
                'end' => $endDate->format('F d, Y'),
            ],
            'stats' => [
                'total_incidents' => $incidents->count(),
                'students_involved' => count($uniqueStudents),
                'resolution_rate' => $resolutionRate,
                'avg_resolution_days' => 3, // Would need actual date tracking
                'repeat_offenders' => $repeatOffenders,
            ],
            'severity' => $severity,
            'prev_quarter' => $prevQuarter,
            'monthly_data' => $monthlyData,
            'by_category' => $categoryData,
            'by_grade' => $byGrade,
            'recommendations' => $recommendations,
            'school' => $this->getSchoolInfo(),
            'generated_at' => now()->format('F d, Y h:i A'),
        ]);
    }

    /**
     * Reports index page
     */
    public function index()
    {
        return view('reports.index', [
            'currentYear' => now()->year,
            'currentMonth' => now()->month,
            'currentQuarter' => ceil(now()->month / 3),
        ]);
    }
}
