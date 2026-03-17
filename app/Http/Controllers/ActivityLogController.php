<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    /**
     * Display activity logs
     */
    public function index(Request $request)
    {
        $query = ActivityLog::with('user.role')
            ->orderByDesc('created_at');

        // Filter by action
        if ($request->filled('action')) {
            $query->byAction($request->action);
        }

        // Filter by model type
        if ($request->filled('model')) {
            $query->byModel($request->model);
        }

        // Filter by user
        if ($request->filled('user_id')) {
            $query->byUser($request->user_id);
        }

        // Filter by date range
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->inDateRange($request->start_date, $request->end_date);
        }

        // Search in description
        if ($request->filled('search')) {
            $query->where('description', 'like', '%' . $request->search . '%');
        }

        $logs = $query->paginate(50)->withQueryString();

        // Get unique actions for filter dropdown
        $actions = ActivityLog::distinct()->pluck('action');
        
        // Get unique model types for filter dropdown
        $modelTypes = ActivityLog::distinct()
            ->whereNotNull('model_type')
            ->pluck('model_type')
            ->map(fn($type) => class_basename($type));

        return view('admin.activity-logs', compact('logs', 'actions', 'modelTypes'));
    }

    /**
     * Get activity logs for a specific model
     */
    public function forModel(string $modelType, int $modelId)
    {
        $modelClass = "App\\Models\\{$modelType}";
        
        if (!class_exists($modelClass)) {
            abort(404, 'Model type not found');
        }

        $logs = ActivityLog::where('model_type', $modelClass)
            ->where('model_id', $modelId)
            ->with('user.role')
            ->orderByDesc('created_at')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $logs,
        ]);
    }

    /**
     * Get recent activity for dashboard widget
     */
    public function recent(int $limit = 10)
    {
        $logs = ActivityLog::with('user.role')
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $logs,
        ]);
    }

    /**
     * Export activity logs
     */
    public function export(Request $request)
    {
        $query = ActivityLog::with('user')
            ->orderByDesc('created_at');

        // Apply same filters as index
        if ($request->filled('action')) {
            $query->byAction($request->action);
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->inDateRange($request->start_date, $request->end_date);
        }

        $logs = $query->get();

        // Generate CSV
        $filename = 'activity_logs_' . now()->format('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($logs) {
            $file = fopen('php://output', 'w');
            
            // CSV headers
            fputcsv($file, [
                'Date/Time',
                'User',
                'Action',
                'Model',
                'Model ID',
                'Description',
                'IP Address',
            ]);

            foreach ($logs as $log) {
                fputcsv($file, [
                    $log->created_at->format('Y-m-d H:i:s'),
                    $log->user?->name ?? 'System',
                    $log->action,
                    class_basename($log->model_type ?? 'N/A'),
                    $log->model_id ?? 'N/A',
                    $log->description,
                    $log->ip_address,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
