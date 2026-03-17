<?php

namespace App\Http\Controllers;

use App\Models\Incident;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BulkActionsController extends Controller
{
    /**
     * Bulk approve incidents
     */
    public function approveIncidents(Request $request)
    {
        $validated = $request->validate([
            'incident_ids' => 'required|array|min:1|max:50',
            'incident_ids.*' => 'exists:incidents,id',
        ]);

        $successCount = 0;
        $failedIds = [];

        DB::beginTransaction();
        try {
            foreach ($validated['incident_ids'] as $id) {
                $incident = Incident::find($id);
                
                // Only approve incidents that are pending approval
                if ($incident && $incident->status === 'pending_approval') {
                    $incident->update(['status' => 'approved']);
                    $incident->logCustomActivity('approved', "Incident {$incident->incident_number} bulk approved");
                    $successCount++;
                } else {
                    $failedIds[] = $id;
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "{$successCount} incident(s) approved successfully",
                'approved_count' => $successCount,
                'failed_ids' => $failedIds,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to approve incidents: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Bulk archive incidents
     */
    public function archiveIncidents(Request $request)
    {
        $validated = $request->validate([
            'incident_ids' => 'required|array|min:1|max:50',
            'incident_ids.*' => 'exists:incidents,id',
        ]);

        $successCount = 0;
        $failedIds = [];

        DB::beginTransaction();
        try {
            foreach ($validated['incident_ids'] as $id) {
                $incident = Incident::find($id);
                
                // Only archive incidents that are approved
                if ($incident && $incident->status === 'approved') {
                    $incident->update(['status' => 'closed']);
                    $incident->logCustomActivity('archived', "Incident {$incident->incident_number} bulk archived");
                    $successCount++;
                } else {
                    $failedIds[] = $id;
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "{$successCount} incident(s) archived successfully",
                'archived_count' => $successCount,
                'failed_ids' => $failedIds,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to archive incidents: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Bulk export incidents to CSV
     */
    public function exportIncidents(Request $request)
    {
        $validated = $request->validate([
            'incident_ids' => 'nullable|array',
            'incident_ids.*' => 'exists:incidents,id',
            'status' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
        ]);

        $query = Incident::with(['students', 'category', 'clause', 'reporter']);

        // Filter by IDs if provided
        if (!empty($validated['incident_ids'])) {
            $query->whereIn('id', $validated['incident_ids']);
        }

        // Filter by status
        if (!empty($validated['status'])) {
            $query->where('status', $validated['status']);
        }

        // Filter by date range
        if (!empty($validated['start_date']) && !empty($validated['end_date'])) {
            $query->whereBetween('incident_date', [$validated['start_date'], $validated['end_date']]);
        }

        $incidents = $query->orderBy('incident_date', 'desc')->get();

        // Log export activity
        ActivityLog::logActivity(
            action: 'exported',
            description: "Exported {$incidents->count()} incident(s) to CSV"
        );

        // Generate CSV
        $filename = 'incidents_export_' . now()->format('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($incidents) {
            $file = fopen('php://output', 'w');
            
            // CSV headers
            fputcsv($file, [
                'Incident No.',
                'Date',
                'Location',
                'Category',
                'Clause',
                'Severity',
                'Students Involved',
                'Description',
                'Reported By',
                'Status',
                'Parent Notified',
                'Action Taken',
                'Created At',
                'Updated At',
            ]);

            foreach ($incidents as $incident) {
                $studentNames = $incident->students->map(fn($s) => "{$s->last_name}, {$s->first_name}")->join('; ');
                
                fputcsv($file, [
                    $incident->incident_number,
                    $incident->incident_date->format('Y-m-d'),
                    $incident->location,
                    $incident->category?->name ?? 'N/A',
                    $incident->clause?->clause_number ?? 'N/A',
                    $incident->category?->severity ?? 'N/A',
                    $studentNames,
                    str_replace(["\r", "\n"], ' ', $incident->description),
                    $incident->reporter?->name ?? 'N/A',
                    ucfirst(str_replace('_', ' ', $incident->status)),
                    $incident->is_parent_notified ? 'Yes' : 'No',
                    str_replace(["\r", "\n"], ' ', $incident->action_taken ?? ''),
                    $incident->created_at->format('Y-m-d H:i:s'),
                    $incident->updated_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
