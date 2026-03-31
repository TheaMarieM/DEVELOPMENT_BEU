@extends('layouts.app')

@section('content')
<!-- Header -->
<header class="bg-white border-b border-gray-200 shadow-sm sticky top-0 z-40">
    <div class="px-8 py-4 flex flex-wrap items-center justify-between gap-4">
        <div>
            <p class="text-[11px] uppercase tracking-[0.35em] text-gray-400">Incident {{ $incident->incident_number }}</p>
            <h2 class="text-2xl font-black text-gray-900">Incident Details &amp; Management</h2>
            <p class="text-sm text-gray-500 mt-1 flex items-center gap-2">
                <i class="fa-regular fa-clock text-gray-400"></i> {{ $incident->incident_date->format('M d, Y h:i A') }}
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            @php
                $statusBadge = match($incident->status) {
                    'reported' => ['Reported', 'bg-blue-50 text-blue-700 border-blue-100', 'fa-clipboard'],
                    'under_review' => ['For Revision', 'bg-yellow-50 text-yellow-700 border-yellow-100', 'fa-rotate-left'],
                    'pending_approval' => ['Pending Approval', 'bg-amber-50 text-amber-700 border-amber-100', 'fa-hourglass-end'],
                    'approved' => ['Approved', 'bg-emerald-50 text-emerald-700 border-emerald-100', 'fa-circle-check'],
                    'closed' => ['Closed', 'bg-gray-50 text-gray-700 border-gray-100', 'fa-folder-closed'],
                    default => ['Filed', 'bg-gray-50 text-gray-700 border-gray-100', 'fa-file'],
                };
            @endphp
            <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-xs font-bold border {{ $statusBadge[1] }} uppercase tracking-wide">
                <i class="fa-solid {{ $statusBadge[2] }} text-[11px]"></i> {{ $statusBadge[0] }}
            </span>
            <a href="{{ route('incidents.index') }}" class="px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm font-semibold text-gray-800 hover:bg-gray-50 transition flex items-center gap-2">
                <i class="fa-solid fa-arrow-left"></i> Back to Logs
            </a>
        </div>
    </div>
</header>

@php
    $summaryCards = [
        ['label' => 'Location', 'value' => $incident->location ?? 'Not set', 'icon' => 'fa-location-dot'],
        ['label' => 'Reported By', 'value' => $incident->reporter->name ?? 'N/A', 'icon' => 'fa-user-shield'],
        ['label' => 'Students Involved', 'value' => $incident->students->count(), 'icon' => 'fa-users'],
        ['label' => 'Last Updated', 'value' => $incident->updated_at->diffForHumans(), 'icon' => 'fa-clock-rotate-left'],
    ];
@endphp
<div class="bg-gray-50 border-b border-gray-200 px-8 py-3">
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 text-sm">
        @foreach($summaryCards as $card)
            <div class="p-3 rounded-2xl bg-white border border-gray-100 shadow-sm flex items-center gap-3">
                <span class="w-10 h-10 rounded-xl bg-gray-100 text-gray-600 flex items-center justify-center">
                    <i class="fa-solid {{ $card['icon'] }} text-base"></i>
                </span>
                <div>
                    <p class="text-[10px] uppercase tracking-[0.3em] text-gray-400">{{ $card['label'] }}</p>
                    <p class="text-base font-semibold text-gray-900 mt-1">{{ $card['value'] }}</p>
                </div>
            </div>
        @endforeach
    </div>
</div>

<div class="p-8 max-w-7xl mx-auto">
    <!-- Status Alert -->
    @if ($errors->any())
        <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
            <p class="text-red-800 font-semibold mb-2">
                <i class="fas fa-exclamation-circle"></i> Error:
            </p>
            <ul class="text-red-700 text-sm space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session('success'))
        <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
            <p class="text-green-800 font-semibold">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
            </p>
        </div>
    @endif

    <!-- Spacer for header summary -->
    <div class="mb-4"></div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-8">
            
            <!-- Incident Overview -->
            <div class="bg-white rounded-xl border border-gray-200 card-shadow p-8">
                <h3 class="text-lg font-bold text-gray-800 mb-6 flex items-center">
                    <i class="fa-solid fa-info-circle text-green-600 mr-3"></i> Incident Overview
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="text-xs font-bold text-gray-500 uppercase tracking-wider">Date & Time</label>
                        <p class="text-sm font-semibold text-gray-800 mt-1">
                            {{ $incident->incident_date->format('F d, Y') }}<br>
                            <span class="text-xs text-gray-600">{{ $incident->incident_date->format('h:i A') }}</span>
                        </p>
                    </div>
                    
                    <div>
                        <label class="text-xs font-bold text-gray-500 uppercase tracking-wider">Location</label>
                        <p class="text-sm font-semibold text-gray-800 mt-1">{{ $incident->location }}</p>
                    </div>
                    
                    <div>
                        <label class="text-xs font-bold text-gray-500 uppercase tracking-wider">Reported By</label>
                        <p class="text-sm font-semibold text-gray-800 mt-1">
                            {{ $incident->reporter->name ?? 'N/A' }}<br>
                            <span class="text-xs text-gray-600 uppercase">{{ $incident->reporter->role->name ?? 'N/A' }}</span>
                        </p>
                    </div>
                    
                    <div>
                        <label class="text-xs font-bold text-gray-500 uppercase tracking-wider">Violation Category</label>
                        <p class="text-sm font-semibold text-gray-800 mt-1">
                            @if($incident->category)
                                {{ $incident->category->name }}
                            @else
                                <span class="text-gray-400 italic">Not set</span>
                            @endif
                        </p>
                    </div>
                </div>
                
                <div class="mt-6 pt-6 border-t border-gray-100">
                    <label class="text-xs font-bold text-gray-500 uppercase tracking-wider">Description</label>
                    <p class="text-sm text-gray-800 mt-2 leading-relaxed">{{ $incident->description }}</p>
                </div>
            </div>

            <!-- Students Involved -->
            <div class="bg-white rounded-xl border border-gray-200 card-shadow p-8">
                <h3 class="text-lg font-bold text-gray-800 mb-6 flex items-center">
                    <i class="fa-solid fa-users text-green-600 mr-3"></i> Students Involved
                </h3>
                
                <div class="space-y-4">
                    @forelse($studentsWithSanctions as $student)
                    <div class="border border-gray-200 rounded-lg p-5 hover:shadow-md transition">
                        <div class="flex justify-between items-start mb-3">
                            <div>
                                <h4 class="font-bold text-gray-900">{{ $student->full_name }}</h4>
                                <p class="text-xs text-gray-500 mt-0.5">
                                    <span class="font-mono font-semibold">{{ $student->student_id }}</span> 
                                    • Grade {{ $student->grade_level }} - {{ $student->section }}
                                </p>
                            </div>
                            <span class="inline-block bg-green-100 text-green-800 px-3 py-1 rounded-full text-xs font-semibold">
                                Involved Student
                            </span>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4 mt-4 pt-4 border-t border-gray-100">
                            <div>
                                <label class="text-[10px] font-bold text-gray-500 uppercase tracking-wider">Offense Count</label>
                                <p class="text-sm font-semibold text-gray-800 mt-1">{{ $student->pivot->offense_count ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <label class="text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1 block">Sanction</label>
                                <div class="flex justify-between items-start">
                                    @if($student->pivot->sanction_id)
                                        <div class="flex-1 mr-2">
                                            <p class="text-sm font-semibold text-gray-800">
                                                {{ \App\Models\Sanction::find($student->pivot->sanction_id)->name ?? 'N/A' }}
                                            </p>
                                        </div>
                                        <form action="{{ route('incidents.toggle-sanction', [$incident, $student]) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="text-[10px] font-bold px-2 py-1 rounded border {{ $student->pivot->sanction_complied ? 'bg-green-50 text-green-700 border-green-200 hover:bg-green-100' : 'bg-gray-50 text-gray-500 border-gray-200 hover:bg-gray-100' }} transition-colors">
                                                <i class="fa-solid {{ $student->pivot->sanction_complied ? 'fa-check' : 'fa-hourglass' }} mr-1"></i>
                                                {{ $student->pivot->sanction_complied ? 'COMPLIED' : 'PENDING' }}
                                            </button>
                                        </form>
                                    @else
                                        <form action="{{ route('incidents.update-student-sanction', [$incident, $student]) }}" method="POST" class="w-full flex items-center gap-2">
                                            @csrf
                                            @method('PUT')
                                            <div class="relative flex-1">
                                                <select name="sanction_id" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none bg-white transition hover:border-gray-400 cursor-pointer" required>
                                                    <option value="" disabled selected>Assign Sanction...</option>
                                                    @forelse($student->available_sanctions as $sanction)
                                                        <option value="{{ $sanction->id }}">{{ $sanction->name }}</option>
                                                    @empty
                                                        <option value="" disabled>No compatible sanctions found (Check Clause/Offense)</option>
                                                    @endforelse
                                                </select>
                                            </div>
                                            <button type="submit" class="shrink-0 w-9 h-9 flex items-center justify-center bg-green-600 hover:bg-green-700 text-white rounded-lg transition shadow-sm" title="Save Sanction">
                                                <i class="fa-solid fa-check"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>

                        @if($student->pivot->narrative_report)
                        <div class="mt-4 pt-4 border-t border-gray-100">
                            <label class="text-[10px] font-bold text-gray-500 uppercase tracking-wider">Narrative Report</label>
                            <p class="text-sm text-gray-700 mt-2">{{ $student->pivot->narrative_report }}</p>
                        </div>
                        @endif
                    </div>
                    @empty
                        @if(!$incident->non_student_participant)
                        <p class="text-gray-500 text-sm text-center py-6">No students assigned to this incident.</p>
                        @endif
                    @endforelse

                    @if($incident->non_student_participant)
                    <div class="border border-gray-200 rounded-lg p-5 hover:shadow-md transition bg-gray-50">
                        <div class="flex justify-between items-start">
                            <div>
                                <h4 class="font-bold text-gray-900">{{ $incident->non_student_participant }}</h4>
                                <p class="text-xs text-gray-500 mt-0.5 italic">External Participant / Non-Student</p>
                            </div>
                            <span class="inline-block bg-amber-100 text-amber-800 px-3 py-1 rounded-full text-xs font-semibold">
                                External
                            </span>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Approvals & Notifications -->
            <div class="bg-white rounded-xl border border-gray-200 card-shadow p-8">
                <h3 class="text-lg font-bold text-gray-800 mb-6 flex items-center justify-between">
                    <div class="flex items-center">
                        <i class="fa-solid fa-check-double text-green-600 mr-3"></i> Approvals & Notifications
                    </div>
                    <form action="{{ route('incidents.toggle-notification', $incident) }}" method="POST">
                        @csrf
                        <button type="submit" class="text-xs font-bold px-3 py-1.5 rounded-lg transition-colors {{ $incident->is_parent_notified ? 'bg-green-100 text-green-800 hover:bg-green-200' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                            <i class="fa-solid {{ $incident->is_parent_notified ? 'fa-check-circle' : 'fa-bell-slash' }} mr-1"></i>
                            {{ $incident->is_parent_notified ? 'Parent Notified' : 'Mark Parent Notified' }}
                        </button>
                    </form>
                </h3>
                
                <div class="space-y-4">
                    @forelse($incident->approvals as $approval)
                    <div class="border border-gray-200 rounded-lg p-4">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="font-semibold text-gray-900">{{ $approval->approver->name ?? 'N/A' }}</p>
                                <p class="text-xs text-gray-500 mt-0.5">{{ $approval->approver->role->name ?? 'N/A' }}</p>
                            </div>
                            @if($approval->status === 'approved')
                                <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs font-semibold">Approved</span>
                            @elseif($approval->status === 'rejected')
                                <span class="bg-red-100 text-red-800 px-2 py-1 rounded text-xs font-semibold">Rejected</span>
                            @else
                                <span class="bg-gray-100 text-gray-800 px-2 py-1 rounded text-xs font-semibold">Pending</span>
                            @endif
                        </div>
                        <p class="text-xs text-gray-500 mt-2">{{ $approval->created_at->format('F d, Y h:i A') }}</p>
                    </div>
                    @empty
                    <p class="text-gray-500 text-sm text-center py-6">No approval records yet.</p>
                    @endforelse
                </div>

                @if($incident->notifications->isNotEmpty())
                <div class="mt-6 pt-6 border-t border-gray-100">
                    <h4 class="font-semibold text-gray-800 mb-4">Parent Notifications</h4>
                    <div class="space-y-2">
                        @foreach($incident->notifications as $notification)
                        <div class="text-sm bg-blue-50 border border-blue-200 rounded px-3 py-2">
                            <p class="font-medium text-blue-900">{{ $notification->status }}</p>
                            <p class="text-xs text-blue-700 mt-0.5">{{ $notification->created_at->format('M d, Y h:i A') }}</p>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Sidebar Actions -->
        <div class="space-y-6 lg:sticky lg:top-24 self-start">
            
            <!-- Quick Actions -->
            <div class="bg-white rounded-xl border border-gray-200 card-shadow p-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wider">Actions</h3>
                        <p class="text-xs text-gray-500">Manage this record</p>
                    </div>
                    <span class="text-[11px] font-semibold px-2 py-0.5 rounded-full bg-gray-100 text-gray-500">Workflow</span>
                </div>
                
                <div class="space-y-2">
                    @if($incident->status === 'under_review')
                    <button onclick="document.getElementById('edit-incident-form').style.display = 'flex'" 
                            class="w-full px-4 py-2.5 bg-sky-600 hover:bg-sky-700 text-white rounded-lg font-semibold text-sm transition flex items-center justify-center gap-2">
                        <i class="fa-solid fa-pen-to-square"></i> Edit Incident
                    </button>
                    
                    <a href="{{ route('incidents.summary-report', $incident) }}" 
                            class="w-full px-4 py-2.5 bg-amber-500 hover:bg-amber-600 text-white rounded-lg font-semibold text-sm transition flex items-center justify-center gap-2 leading-tight">
                        <i class="fa-solid fa-file-pen"></i> Edit Summary Report
                    </a>
                    @elseif($incident->status !== 'approved' && $incident->status !== 'closed')
                    <button onclick="document.getElementById('edit-incident-form').style.display = 'flex'" 
                            class="w-full px-4 py-2.5 bg-sky-600 hover:bg-sky-700 text-white rounded-lg font-semibold text-sm transition flex items-center justify-center gap-2">
                        <i class="fa-solid fa-pen-to-square"></i> Edit Incident
                    </button>
                    @endif
                    
                    @if($incident->status === 'reported')
                    <a href="{{ route('incidents.summary-report', $incident) }}" 
                            class="w-full px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-semibold text-sm transition flex items-center justify-center gap-2 leading-tight">
                        <i class="fa-solid fa-file-invoice"></i> Create Summary Report
                    </a>
                    @endif

                    @if(in_array($incident->status, ['pending_approval', 'approved', 'closed']))
                    <a href="{{ route('incidents.summary-report', $incident) }}" 
                       class="w-full px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-semibold text-sm transition flex items-center justify-center gap-2 leading-tight">
                        <i class="fa-solid fa-file-contract"></i> View Summary Report
                    </a>
                    @endif

                    @if($incident->status === 'approved')
                    <form action="{{ route('incidents.archive', $incident) }}" method="POST" class="w-full">
                        @csrf
                        <button type="submit" onclick="return confirm('Archive this incident? It will be moved to closed status.')"
                                class="w-full px-4 py-2.5 bg-gray-800 hover:bg-gray-900 text-white rounded-lg font-semibold text-sm transition flex items-center justify-center gap-2">
                            <i class="fa-solid fa-folder-closed"></i> Archive Record
                        </button>
                    </form>
                    @endif
                </div>
            </div>

            <!-- Incident Meta -->
            <div class="bg-white rounded-xl border border-gray-200 card-shadow p-6">
                <h3 class="text-sm font-bold text-gray-800 mb-4 uppercase tracking-wider">Details</h3>
                
                <div class="space-y-4 text-sm">
                    <div>
                        <label class="text-xs font-bold text-gray-500 uppercase tracking-wider">Incident ID</label>
                        <p class="font-mono text-gray-800 mt-1">{{ $incident->incident_number }}</p>
                    </div>
                    
                    <div>
                        <label class="text-xs font-bold text-gray-500 uppercase tracking-wider">Created</label>
                        <p class="text-gray-800 mt-1">{{ $incident->created_at->format('M d, Y h:i A') }}</p>
                    </div>
                    
                    <div>
                        <label class="text-xs font-bold text-gray-500 uppercase tracking-wider">Last Updated</label>
                        <p class="text-gray-800 mt-1">{{ $incident->updated_at->format('M d, Y h:i A') }}</p>
                    </div>

                    <div>
                        <label class="text-xs font-bold text-gray-500 uppercase tracking-wider">Students Involved</label>
                        <p class="text-gray-800 mt-1 font-bold">{{ $incident->students->count() }}</p>
                    </div>
                </div>
            </div>

            <!-- Related Links -->
            <div class="bg-white rounded-xl border border-gray-200 card-shadow p-6">
                <h3 class="text-sm font-bold text-gray-800 mb-4 uppercase tracking-wider">Navigate</h3>
                
                <div class="space-y-2">
                    <a href="{{ route('incidents.index') }}" class="block text-sm text-green-600 hover:text-green-700 font-medium">
                        <i class="fa-solid fa-list mr-2"></i> All Incidents
                    </a>
                    <a href="{{ route('dashboard') }}" class="block text-sm text-green-600 hover:text-green-700 font-medium">
                        <i class="fa-solid fa-chart-line mr-2"></i> Dashboard
                    </a>
                    <a href="{{ route('students.index') }}" class="block text-sm text-green-600 hover:text-green-700 font-medium">
                        <i class="fa-solid fa-graduation-cap mr-2"></i> Student Registry
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div id="edit-incident-form" style="display: none;" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl max-w-md w-full p-8 max-h-96 overflow-y-auto">
        <h3 class="text-lg font-bold text-gray-800 mb-4">Quick Edit</h3>
        
        <form action="{{ route('incidents.update', $incident) }}" method="POST">
            @csrf
            @method('PATCH')
            
            <div class="mb-4">
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Status</label>
                <select name="status" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-green-500 focus:border-transparent outline-none">
                    <option value="reported" {{ $incident->status === 'reported' ? 'selected' : '' }}>Reported</option>
                    <option value="under_review" {{ $incident->status === 'under_review' ? 'selected' : '' }}>Under Review</option>
                    <option value="pending_approval" {{ $incident->status === 'pending_approval' ? 'selected' : '' }}>Pending Approval</option>
                    <option value="approved" {{ $incident->status === 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="closed" {{ $incident->status === 'closed' ? 'selected' : '' }}>Closed</option>
                </select>
            </div>
            
            <div class="mb-6">
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Location</label>
                <input type="text" name="location" value="{{ $incident->location }}" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-green-500 focus:border-transparent outline-none">
            </div>
            
            <div class="flex gap-3">
                <button type="submit" class="flex-1 px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium text-sm transition">
                    Save Changes
                </button>
                <button type="button" onclick="document.getElementById('edit-incident-form').style.display = 'none'" class="flex-1 px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg font-medium text-sm transition">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>

@endsection
