<?php

namespace App\Http\Controllers;

use App\Services\AnalyticsService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AnalyticsController extends Controller
{
    protected AnalyticsService $analyticsService;

    public function __construct(AnalyticsService $analyticsService)
    {
        $this->analyticsService = $analyticsService;
    }

    /**
     * Show the analytics dashboard page with embedded data
     */
    public function index(Request $request)
    {
        $filters = $request->only([
            'grade_level',
            'section',
            'severity',
            'status',
            'date_from',
            'date_to',
        ]);

        $limit = $request->integer('limit', 50);

        $data = [
            'overview' => $this->analyticsService->getOverviewStats(),
            'trends' => $this->analyticsService->getIncidentTrends(6),
            'categories' => $this->analyticsService->getCategoryBreakdown(),
            'gradeLevels' => $this->analyticsService->getGradeLevelBreakdown(),
            'sections' => $this->analyticsService->getSectionBreakdown(),
            'severity' => $this->analyticsService->getSeverityDistribution(),
            'topOffenders' => $this->analyticsService->getTopOffenders(10),
            'comparative' => $this->analyticsService->getComparativeAnalytics(),
            'performance' => $this->analyticsService->getProcessingPerformance(),
        ];

        $dataset = $this->analyticsService->getIncidentDataset($filters, $limit);
        $insights = $this->buildInsightSummary($data, $dataset);
        $interventionInsights = $this->analyticsService->generateInterventionInsights(4);

        return view('admin.analytics', [
            'data' => $data,
            'filters' => $filters,
            'dataset' => $dataset,
            'insights' => $insights,
            'interventionInsights' => $interventionInsights,
        ]);
    }

    /**
     * Get all dashboard statistics
     */
    public function dashboard(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $this->analyticsService->getDashboardStats(),
        ]);
    }

    /**
     * Get overview stats
     */
    public function overview(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $this->analyticsService->getOverviewStats(),
        ]);
    }

    /**
     * Get incident trends
     */
    public function trends(Request $request): JsonResponse
    {
        $months = $request->input('months', 6);
        
        return response()->json([
            'success' => true,
            'data' => [
                'monthly' => $this->analyticsService->getIncidentTrends($months),
                'weekly' => $this->analyticsService->getWeeklyTrends(),
            ],
        ]);
    }

    /**
     * Get category breakdown
     */
    public function categories(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $this->analyticsService->getCategoryBreakdown(),
        ]);
    }

    /**
     * Get grade level breakdown
     */
    public function gradeLevels(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $this->analyticsService->getGradeLevelBreakdown(),
        ]);
    }

    /**
     * Get section breakdown
     */
    public function sections(Request $request): JsonResponse
    {
        $gradeLevel = $request->input('grade_level');
        
        return response()->json([
            'success' => true,
            'data' => $this->analyticsService->getSectionBreakdown($gradeLevel),
        ]);
    }

    /**
     * Get severity distribution
     */
    public function severity(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $this->analyticsService->getSeverityDistribution(),
        ]);
    }

    /**
     * Get top offenders
     */
    public function topOffenders(Request $request): JsonResponse
    {
        $limit = $request->input('limit', 10);
        
        return response()->json([
            'success' => true,
            'data' => $this->analyticsService->getTopOffenders($limit),
        ]);
    }

    /**
     * Get comparative analytics
     */
    public function comparative(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $this->analyticsService->getComparativeAnalytics(),
        ]);
    }

    /**
     * Get performance metrics
     */
    public function performance(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $this->analyticsService->getProcessingPerformance(),
        ]);
    }

    /**
     * Clear analytics cache
     */
    public function clearCache(): JsonResponse
    {
        $this->analyticsService->clearCache();
        
        return response()->json([
            'success' => true,
            'message' => 'Analytics cache cleared successfully',
        ]);
    }

    /**
     * Build narrative insights so analytics feels actionable
     */
    protected function buildInsightSummary(array $data, array $dataset): array
    {
        $categories = collect($data['categories'] ?? []);
        $gradeLevels = collect($data['gradeLevels'] ?? []);
        $severity = collect($data['severity'] ?? []);
        $overview = $data['overview'] ?? [];
        $records = collect($dataset['records'] ?? []);

        $topCategory = $categories->sortByDesc('count')->first();
        $hotGrade = $gradeLevels->sortByDesc('count')->first();
        $severityLeader = $severity->sortByDesc('count')->first();
        $totalSeverity = max(1, $severity->sum('count'));

        $backlog = ($overview['pending_approval'] ?? 0) + ($overview['under_review'] ?? 0);
        $processed = $overview['approved_this_month'] ?? 0;

        $activeStudents = $overview['active_students'] ?? null;
        $studentsWithIncidents = $overview['students_with_incidents'] ?? null;
        $incidentPenetration = $activeStudents
            ? round((($studentsWithIncidents ?? 0) / max(1, $activeStudents)) * 100, 1)
            : null;

        $insights = [
            [
                'title' => 'Most frequent violation',
                'value' => $topCategory['name'] ?? null,
                'context' => $topCategory ? ($topCategory['count'] . ' recorded cases this year') : null,
            ],
            [
                'title' => 'Grade level hotspot',
                'value' => $hotGrade['label'] ?? null,
                'context' => $hotGrade ? ($hotGrade['count'] . ' incidents YTD') : null,
            ],
            [
                'title' => 'Highest severity share',
                'value' => isset($severityLeader['label']) ? $severityLeader['label'] : null,
                'context' => $severityLeader
                    ? round(($severityLeader['count'] / $totalSeverity) * 100, 1) . '% of all incidents'
                    : null,
            ],
            [
                'title' => 'Case backlog',
                'value' => $backlog,
                'context' => $processed > 0
                    ? $processed . ' approvals completed this month'
                    : 'No approvals completed yet',
            ],
            [
                'title' => 'Incident reach',
                'value' => $incidentPenetration ? ($incidentPenetration . '% of students') : null,
                'context' => $studentsWithIncidents && $activeStudents
                    ? $studentsWithIncidents . ' of ' . $activeStudents . ' active students'
                    : null,
            ],
            [
                'title' => 'Dataset sample',
                'value' => $records->count() ? $records->count() . ' recent cases' : null,
                'context' => isset($dataset['total']) ? ('Out of ' . $dataset['total'] . ' matching incidents') : null,
            ],
        ];

        return collect($insights)
            ->filter(function ($insight) {
                return !empty($insight['value']);
            })
            ->values()
            ->all();
    }
}
