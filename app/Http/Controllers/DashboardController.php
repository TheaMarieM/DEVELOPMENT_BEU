<?php

namespace App\Http\Controllers;

use App\Models\Incident;
use App\Models\Student;
use App\Models\InterventionSuggestion;
use App\Services\AnalyticsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class DashboardController extends Controller
{
    protected AnalyticsService $analyticsService;

    public function __construct(AnalyticsService $analyticsService)
    {
        $this->analyticsService = $analyticsService;
    }

    public function index(Request $request)
    {
        $user = Auth::user();

        // Use cached stats from analytics service
        $overviewStats = Cache::remember('dashboard_overview', 300, function () {
            return $this->analyticsService->getOverviewStats();
        });

        // Get at-risk students count from cached stats
        $atRiskStudentsCount = $overviewStats['at_risk_students'] ?? 0;

        // Get common incidents this quarter (with caching)
        $quarterStart = now()->startOfQuarter();
        $quarterEnd = now()->endOfQuarter();

        $commonIncident = Cache::remember('common_incident_quarter', 600, function () use ($quarterStart, $quarterEnd) {
            return Incident::select('violation_category_id', DB::raw('count(*) as total'))
                ->whereBetween('incident_date', [$quarterStart, $quarterEnd])
                ->groupBy('violation_category_id')
                ->with('category')
                ->orderBy('total', 'desc')
                ->first();
        });

        // Get pending approvals count from cached stats
        $pendingApprovalsCount = $overviewStats['pending_approval'] ?? 0;

        // Get recent incidents with filters
        $query = Incident::with(['students', 'category', 'reporter']);

        // Apply filters
        if ($request->filled('grade_level')) {
            $query->whereHas('students', function($q) use ($request) {
                $q->where('grade_level', $request->grade_level);
            });
        }

        if ($request->filled('section')) {
            $query->whereHas('students', function($q) use ($request) {
                $q->where('section', 'like', '%' . $request->section . '%');
            });
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhereHas('students', function($subQ) use ($search) {
                      $subQ->where('first_name', 'like', "%{$search}%")
                           ->orWhere('last_name', 'like', "%{$search}%")
                           ->orWhere('students.student_id', 'like', "%{$search}%");
                  })
                  ->orWhereHas('category', function($subQ) use ($search) {
                      $subQ->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $recentIncidents = $query->orderBy('incident_date', 'desc')
            ->take(20)
            ->get();

        // Get AI-driven intervention suggestions
        $interventionSuggestions = InterventionSuggestion::where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get();

        // Prepare data for view
        $mostCommonIncident = $commonIncident?->category;
        $suggestions = $interventionSuggestions;

        // Additional stats for enhanced dashboard
        $quickStats = [
            'total_this_month' => $overviewStats['total_incidents_this_month'] ?? 0,
            'monthly_change' => $overviewStats['monthly_change_percent'] ?? 0,
            'under_review' => $overviewStats['under_review'] ?? 0,
            'repeat_offenders' => $overviewStats['repeat_offenders'] ?? 0,
        ];

        return view('dashboard.index', compact(
            'atRiskStudentsCount',
            'mostCommonIncident',
            'pendingApprovalsCount',
            'recentIncidents',
            'suggestions',
            'quickStats'
        ));
    }
}
