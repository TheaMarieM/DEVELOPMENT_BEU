<?php

namespace App\Http\Controllers;

use App\Models\InterventionSuggestion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InterventionSuggestionController extends Controller
{
    public function convert(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'grade_level' => ['nullable', 'string', 'max:255'],
            'section' => ['nullable', 'string', 'max:255'],
            'incident_type' => ['required', 'string', 'max:255'],
            'incident_count' => ['required', 'integer', 'min:1'],
            'analysis_period_start' => ['required', 'date'],
            'analysis_period_end' => ['required', 'date', 'after_or_equal:analysis_period_start'],
            'suggestion' => ['required', 'string'],
            'assigned_to' => ['nullable', 'string', 'max:255'],
            'assignment_due_at' => ['nullable', 'date'],
        ]);

        InterventionSuggestion::create([
            'grade_level' => $data['grade_level'] ?? null,
            'section' => $data['section'] ?? null,
            'incident_type' => $data['incident_type'],
            'incident_count' => $data['incident_count'],
            'analysis_period_start' => $data['analysis_period_start'],
            'analysis_period_end' => $data['analysis_period_end'],
            'suggestion' => $data['suggestion'],
            'assigned_to' => $data['assigned_to'] ?? Auth::user()?->name,
            'assignment_due_at' => $data['assignment_due_at'] ?? now()->addDays(7),
            'status' => 'pending',
        ]);

        return back()->with('success', 'Insight converted into a workflow-ready plan.');
    }

    public function decide(Request $request, InterventionSuggestion $suggestion): RedirectResponse
    {
        if ($suggestion->status !== 'pending') {
            return back()->with('info', 'This plan has already been processed.');
        }

        $data = $request->validate([
            'decision' => ['required', 'in:apply,dismiss'],
            'remarks' => ['nullable', 'string', 'max:1000'],
        ]);

        $status = $data['decision'] === 'apply' ? 'implemented' : 'rejected';
        $message = $status === 'implemented'
            ? 'Plan marked as implemented.'
            : 'Plan dismissed.';

        $suggestion->update([
            'status' => $status,
            'decided_by' => Auth::id(),
            'decided_at' => now(),
            'decision_remarks' => $data['remarks'] ?? null,
        ]);

        return back()->with('success', $message);
    }
}
