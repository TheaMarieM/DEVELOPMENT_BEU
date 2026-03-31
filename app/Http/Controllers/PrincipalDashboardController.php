<?php

namespace App\Http\Controllers;

use App\Models\Incident;
use App\Models\IncidentApproval;
use App\Models\Sanction;
use App\Models\Student;
use App\Models\ViolationCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Services\InterventionSuggestionService;

class PrincipalDashboardController extends Controller
{
    protected InterventionSuggestionService $interventionSuggestionService;

    public function __construct(InterventionSuggestionService $interventionSuggestionService)
    {
        $this->interventionSuggestionService = $interventionSuggestionService;
    }

    public function dashboard(Request $request)
    {
        $pendingApprovalsQuery = Incident::query()
            ->where('status', 'pending_approval');

        $incidentsOverview = Incident::query()
            ->whereIn('status', ['pending_approval', 'under_review'])
            ->with(['students', 'category', 'reporter']);

        // Apply status filter if provided
        if ($request->filled('status')) {
            $incidentsOverview->where('status', $request->status);
        }

        // Apply search filter by student name
        if ($request->filled('search')) {
            $search = $request->search;
            $incidentsOverview->whereHas('students', function ($query) use ($search) {
                $query->where('first_name', 'like', "%{$search}%")
                      ->orWhere('last_name', 'like', "%{$search}%")
                      ->orWhere(DB::raw('CONCAT(first_name, " ", last_name)'), 'like', "%{$search}%");
            });
        }

        $incidentsOverview = $incidentsOverview
            ->orderByDesc('incident_date')
            ->orderByDesc('created_at')
            ->paginate(10)
            ->withQueryString();

        $pendingApprovalsCount = (clone $pendingApprovalsQuery)->count();

        $atRiskStudentsCount = Student::whereHas('incidents', function ($query) {
            $query->whereIn('status', ['pending_approval', 'under_review'])
                ->whereNotNull('incident_date')
                ->whereDate('incident_date', '>=', now()->subDays(30));
        })->count();

        $commonIncident = Incident::select('violation_category_id', DB::raw('count(*) as total'))
            ->where('status', 'pending_approval')
            ->groupBy('violation_category_id')
            ->with('category')
            ->orderByDesc('total')
            ->first();

        $commonIncidentName = $commonIncident?->category?->name ?? 'No active violation trend';
        $commonIncidentTotal = $commonIncident->total ?? 0;

        $insights = $this->interventionSuggestionService->getDashboardInsights(3);

        return view('principal.dashboard', [
            'incidentsOverview' => $incidentsOverview,
            'pendingApprovalsCount' => $pendingApprovalsCount,
            'atRiskStudentsCount' => $atRiskStudentsCount,
            'commonIncidentName' => $commonIncidentName,
            'commonIncidentTotal' => $commonIncidentTotal,
            'pendingSidebarCount' => $pendingApprovalsCount,
            'suggestions' => $insights['suggestions'],
            'suggestionsSource' => $insights['suggestionsSource'],
            'recentPlans' => $insights['recentPlans'],
        ]);
    }

    public function archives()
    {
        $archivedIncidents = Incident::query()
            ->whereIn('status', ['approved', 'closed'])
            ->with(['students', 'category', 'reporter'])
            ->orderByDesc('updated_at')
            ->paginate(15);

        $totalArchived = Incident::whereIn('status', ['approved', 'closed'])->count();

        $archivedThisMonth = Incident::whereIn('status', ['approved', 'closed'])
            ->whereBetween('updated_at', [now()->startOfMonth(), now()->endOfMonth()])
            ->count();

        $pendingSidebarCount = Incident::where('status', 'pending_approval')->count();

        return view('principal.archives', [
            'archivedIncidents' => $archivedIncidents,
            'totalArchived' => $totalArchived,
            'archivedThisMonth' => $archivedThisMonth,
            'pendingSidebarCount' => $pendingSidebarCount,
        ]);
    }

    public function show(Incident $incident)
    {
        $incident->load([
            'students.parents',
            'category',
            'clause',
            'reporter',
            'approvals.approver.role',
            'notifications.parent',
        ]);

        $sanctionIds = $incident->students
            ->pluck('pivot.sanction_id')
            ->filter()
            ->unique();

        $sanctionMap = $sanctionIds->isNotEmpty()
            ? Sanction::whereIn('id', $sanctionIds)->get()->keyBy('id')
            : collect();

        $incident->students->each(function ($student) use ($sanctionMap) {
            $student->sanction_details = $student->pivot->sanction_id
                ? $sanctionMap->get($student->pivot->sanction_id)
                : null;
        });

        $categoryLegend = ViolationCategory::orderBy('sort_order')
            ->take(4)
            ->get();

        $maxOffenseCount = max($incident->students->pluck('pivot.offense_count')->filter()->all() ?: [1]);

        $pendingSidebarCount = Incident::where('status', 'pending_approval')->count();

        return view('principal.incidents.show', [
            'incident' => $incident,
            'categoryLegend' => $categoryLegend,
            'maxOffenseCount' => $maxOffenseCount,
            'pendingSidebarCount' => $pendingSidebarCount,
        ]);
    }

    public function approve(Request $request, Incident $incident)
    {
        if ($incident->status !== 'pending_approval') {
            return back()->with('error', 'Only pending incidents can be approved.');
        }

        $data = $request->validate([
            'remarks' => ['nullable', 'string', 'max:2000'],
        ]);

        DB::transaction(function () use ($incident, $data) {
            IncidentApproval::create([
                'incident_id' => $incident->id,
                'approved_by' => Auth::id(),
                'status' => 'approved',
                'remarks' => $data['remarks'] ?? null,
                'approved_at' => now(),
            ]);

            $incident->update(['status' => 'approved']);
        });

        return redirect()
            ->route('principal.dashboard')
            ->with('success', 'Incident approved and case closed.');
    }

    public function returnForRevision(Request $request, Incident $incident)
    {
        if ($incident->status !== 'pending_approval') {
            return back()->with('error', 'Only pending incidents can be returned.');
        }

        $data = $request->validate([
            'remarks' => ['required', 'string', 'max:2000'],
        ]);

        DB::transaction(function () use ($incident, $data) {
            IncidentApproval::create([
                'incident_id' => $incident->id,
                'approved_by' => Auth::id(),
                'status' => 'rejected',
                'remarks' => $data['remarks'],
                'approved_at' => now(),
            ]);

            $incident->update(['status' => 'under_review']);
        });

        return back()->with('success', 'Incident returned to the Discipline Office.');
    }

    public function downloadAttachment(Incident $incident, Student $student)
    {
        $pivot = $incident->students()->where('student_id', $student->id)->first()?->pivot;

        if (!$pivot || !$pivot->narrative_file_path) {
            abort(404);
        }

        $path = $pivot->narrative_file_path;

        if (!Storage::disk('private')->exists($path)) {
            abort(404);
        }

        $filename = Str::of(basename($path))->replaceMatches('/^[0-9]+-/', '')->toString();

        return response()->download(
            Storage::disk('private')->path($path),
            $filename ?: basename($path)
        );
    }
}
