@extends('layouts.app')

@section('content')
<header class="bg-gradient-to-r from-green-800 to-green-700 border-b border-green-900 px-8 py-6 flex justify-between items-center sticky top-0 z-40 shadow-lg">
    <div>
        <h2 class="text-2xl font-black text-white">Activity Logs</h2>
        <p class="text-sm text-green-100 font-medium mt-1">Audit trail of all system activities</p>
    </div>
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.activity-logs.export', request()->query()) }}" 
           class="bg-white/10 hover:bg-white/20 text-white px-4 py-2 rounded-lg text-sm font-semibold transition-all flex items-center gap-2">
            <i class="fa-solid fa-download text-xs"></i> Export CSV
        </a>
        <a href="{{ route('dashboard') }}" 
           class="bg-white hover:bg-gray-50 text-green-800 px-5 py-2 rounded-lg text-sm font-bold transition-all">
            <i class="fa-solid fa-arrow-left mr-2"></i> Back
        </a>
    </div>
</header>

<div class="p-8">
    <!-- Filters -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm mb-6">
        <form method="GET" action="{{ route('admin.activity-logs') }}" class="p-4">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Action</label>
                    <select name="action" class="w-full rounded-lg border-gray-300 text-sm focus:border-green-500 focus:ring-green-500">
                        <option value="">All Actions</option>
                        @foreach($actions as $action)
                            <option value="{{ $action }}" {{ request('action') === $action ? 'selected' : '' }}>
                                {{ ucfirst($action) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Model</label>
                    <select name="model" class="w-full rounded-lg border-gray-300 text-sm focus:border-green-500 focus:ring-green-500">
                        <option value="">All Models</option>
                        @foreach($modelTypes as $type)
                            <option value="App\Models\{{ $type }}" {{ request('model') === "App\Models\\{$type}" ? 'selected' : '' }}>
                                {{ $type }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Start Date</label>
                    <input type="date" name="start_date" value="{{ request('start_date') }}"
                           class="w-full rounded-lg border-gray-300 text-sm focus:border-green-500 focus:ring-green-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">End Date</label>
                    <input type="date" name="end_date" value="{{ request('end_date') }}"
                           class="w-full rounded-lg border-gray-300 text-sm focus:border-green-500 focus:ring-green-500">
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit" class="flex-1 bg-green-700 hover:bg-green-800 text-white px-4 py-2 rounded-lg text-sm font-semibold transition-all">
                        <i class="fa-solid fa-filter mr-1"></i> Filter
                    </button>
                    <a href="{{ route('admin.activity-logs') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-semibold transition-all">
                        Clear
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Results Count -->
    <div class="mb-4 flex justify-between items-center">
        <p class="text-sm text-gray-600">
            Showing <span class="font-semibold">{{ $logs->firstItem() ?? 0 }}</span> to 
            <span class="font-semibold">{{ $logs->lastItem() ?? 0 }}</span> of 
            <span class="font-semibold">{{ $logs->total() }}</span> activities
        </p>
    </div>

    <!-- Activity Table -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Date/Time</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">User</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Action</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Description</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">IP Address</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Details</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($logs as $log)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ $log->created_at->format('M d, Y') }}</div>
                                <div class="text-xs text-gray-500">{{ $log->created_at->format('h:i:s A') }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="h-8 w-8 rounded-full bg-green-100 flex items-center justify-center mr-3">
                                        <span class="text-green-700 font-semibold text-xs">
                                            {{ $log->user ? strtoupper(substr($log->user->name, 0, 2)) : 'SY' }}
                                        </span>
                                    </div>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">{{ $log->user?->name ?? 'System' }}</div>
                                        <div class="text-xs text-gray-500">{{ $log->user?->role?->name ?? 'N/A' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $log->action_badge }}">
                                    <i class="fa-solid {{ $log->action_icon }} mr-1.5 text-[10px]"></i>
                                    {{ ucfirst($log->action) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900 max-w-md truncate" title="{{ $log->description }}">
                                    {{ $log->description ?? 'No description' }}
                                </div>
                                @if($log->model_type)
                                    <div class="text-xs text-gray-500 mt-1">
                                        {{ class_basename($log->model_type) }} #{{ $log->model_id }}
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-xs text-gray-500 font-mono">{{ $log->ip_address ?? 'N/A' }}</span>
                            </td>
                            <td class="px-6 py-4">
                                @if($log->old_values || $log->new_values)
                                    <button type="button" 
                                            onclick="showDetails({{ $log->id }})"
                                            class="text-green-600 hover:text-green-800 text-sm font-medium">
                                        <i class="fa-solid fa-eye mr-1"></i> View
                                    </button>
                                @else
                                    <span class="text-gray-400 text-sm">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="h-16 w-16 rounded-full bg-gray-100 flex items-center justify-center mb-4">
                                        <i class="fa-solid fa-clock-rotate-left text-gray-400 text-2xl"></i>
                                    </div>
                                    <p class="text-gray-500 font-medium">No activity logs found</p>
                                    <p class="text-gray-400 text-sm mt-1">Activities will appear here when actions are performed</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $logs->links() }}
    </div>
</div>

<!-- Details Modal -->
<div id="detailsModal" class="fixed inset-0 bg-black/50 z-50 hidden flex items-center justify-center p-4" onclick="closeModal(event)">
    <div class="bg-white rounded-2xl shadow-xl max-w-2xl w-full max-h-[80vh] overflow-hidden" onclick="event.stopPropagation()">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <h3 class="text-lg font-bold text-gray-900">Activity Details</h3>
            <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fa-solid fa-times"></i>
            </button>
        </div>
        <div class="p-6 overflow-y-auto max-h-[60vh]">
            <div id="modalContent">
                <div class="animate-pulse">
                    <div class="h-4 bg-gray-200 rounded w-3/4 mb-4"></div>
                    <div class="h-4 bg-gray-200 rounded w-1/2"></div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Store log data for modal display
    const logData = @json($logs->keyBy('id')->map(fn($log) => [
        'old_values' => $log->old_values,
        'new_values' => $log->new_values,
    ]));

    function showDetails(logId) {
        const modal = document.getElementById('detailsModal');
        const content = document.getElementById('modalContent');
        const data = logData[logId];
        
        if (!data) {
            content.innerHTML = '<p class="text-gray-500">No details available</p>';
        } else {
            let html = '';
            
            if (data.old_values && Object.keys(data.old_values).length > 0) {
                html += '<div class="mb-6"><h4 class="text-sm font-bold text-gray-700 mb-2">Previous Values</h4>';
                html += '<div class="bg-red-50 rounded-lg p-4 border border-red-100">';
                html += '<pre class="text-xs text-red-800 whitespace-pre-wrap overflow-x-auto">' + JSON.stringify(data.old_values, null, 2) + '</pre>';
                html += '</div></div>';
            }
            
            if (data.new_values && Object.keys(data.new_values).length > 0) {
                html += '<div><h4 class="text-sm font-bold text-gray-700 mb-2">New Values</h4>';
                html += '<div class="bg-green-50 rounded-lg p-4 border border-green-100">';
                html += '<pre class="text-xs text-green-800 whitespace-pre-wrap overflow-x-auto">' + JSON.stringify(data.new_values, null, 2) + '</pre>';
                html += '</div></div>';
            }
            
            content.innerHTML = html || '<p class="text-gray-500">No changes recorded</p>';
        }
        
        modal.classList.remove('hidden');
    }

    function closeModal(event) {
        if (event && event.target !== event.currentTarget) return;
        document.getElementById('detailsModal').classList.add('hidden');
    }

    // Close modal on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeModal();
    });
</script>
@endpush
@endsection
