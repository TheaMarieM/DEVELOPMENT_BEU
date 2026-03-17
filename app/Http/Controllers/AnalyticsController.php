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
    public function index()
    {
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

        return view('admin.analytics', compact('data'));
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
}
