@extends('layouts.app')

@section('content')
<!-- Header -->
<header class="bg-gradient-to-r from-green-800 to-green-700 border-b border-green-900 px-8 py-6 flex justify-between items-center sticky top-0 z-40 shadow-lg">
    <div>
        <h2 class="text-2xl font-black text-white">Discipline Chairperson Dashboard</h2>
        <p class="text-sm text-green-100 font-medium mt-1">Welcome back, {{ Auth::user()->name ?? 'Administrator' }}</p>
    </div>
    <div class="flex items-center gap-6">
        <a href="{{ route('incidents.index') }}" class="bg-white hover:bg-gray-50 text-green-800 px-6 py-3 rounded-xl text-sm font-bold transition-all shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 flex items-center gap-2">
            <i class="fa-solid fa-plus text-xs"></i> Log New Incident
        </a>
        <div class="h-10 w-px bg-green-600"></div>
        <div class="flex items-center gap-3">
            <div class="text-right">
                <p class="text-sm font-bold text-white">{{ Auth::user()->name ?? 'D. Chairperson' }}</p>
                <p class="text-xs text-green-200 font-semibold">Main Administrator</p>
            </div>
            <div class="w-11 h-11 rounded-xl bg-white/20 backdrop-blur-sm border-2 border-white/30 flex items-center justify-center text-white">
                <i class="fa-solid fa-user-shield"></i>
            </div>
        </div>
    </div>
</header>

<div class="p-8 max-w-7xl mx-auto space-y-8">
    
    <!-- Quick Access Bar -->
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-4">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center gap-2">
                <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Quick Access:</span>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('admin.analytics') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-green-50 hover:bg-green-100 text-green-700 rounded-lg text-sm font-semibold transition-all">
                    <i class="fa-solid fa-chart-pie text-xs"></i> Analytics Dashboard
                </a>
                <a href="{{ route('reports.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 rounded-lg text-sm font-semibold transition-all">
                    <i class="fa-solid fa-file-lines text-xs"></i> Reports Center
                </a>
                <a href="{{ route('admin.activity-logs') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-purple-50 hover:bg-purple-100 text-purple-700 rounded-lg text-sm font-semibold transition-all">
                    <i class="fa-solid fa-clock-rotate-left text-xs"></i> Activity Logs
                </a>
                <a href="{{ route('incidents.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-50 hover:bg-blue-100 text-blue-700 rounded-lg text-sm font-semibold transition-all">
                    <i class="fa-solid fa-list text-xs"></i> All Incidents
                </a>
                <a href="{{ route('students.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-amber-50 hover:bg-amber-100 text-amber-700 rounded-lg text-sm font-semibold transition-all">
                    <i class="fa-solid fa-users text-xs"></i> Students
                </a>
                <a href="{{ route('attendance.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-rose-50 hover:bg-rose-100 text-rose-700 rounded-lg text-sm font-semibold transition-all">
                    <i class="fa-solid fa-calendar-check text-xs"></i> Attendance
                </a>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- At-Risk Students Card -->
        <div class="bg-gradient-to-br from-red-50 to-rose-50 p-6 rounded-2xl border-2 border-red-100 shadow-lg hover:shadow-xl transition-shadow">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <p class="text-xs font-bold text-red-400 uppercase tracking-wider">At-Risk Students</p>
                    <h4 class="text-4xl font-black text-red-600 mt-2">{{ str_pad($atRiskStudentsCount, 2, '0', STR_PAD_LEFT) }}</h4>
                </div>
                <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center">
                    <i class="fa-solid fa-triangle-exclamation text-red-600 text-xl"></i>
                </div>
            </div>
            <div class="bg-red-100/50 rounded-lg px-3 py-2 mt-3">
                <p class="text-xs text-red-700 font-bold">⚠️ Action Required</p>
                <p class="text-xs text-red-600 mt-1">Early intervention recommended</p>
            </div>
        </div>

        <!-- Common Incident Card -->
        <div class="bg-gradient-to-br from-blue-50 to-cyan-50 p-6 rounded-2xl border-2 border-blue-100 shadow-lg hover:shadow-xl transition-shadow">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <p class="text-xs font-bold text-blue-400 uppercase tracking-wider">Common Incident (Q4)</p>
                    <h4 class="text-lg font-black text-blue-900 mt-2 leading-tight">
                        @if($mostCommonIncident)
                            {{ $mostCommonIncident->name ?? 'No data' }}
                        @else
                            No data
                        @endif
                    </h4>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                    <i class="fa-solid fa-chart-line text-blue-600 text-xl"></i>
                </div>
            </div>
            <p class="text-xs text-blue-600 mt-3 font-medium">
                @if($mostCommonIncident)
                    Primary trend in Grade 9 & 10
                @else
                    No incidents recorded this quarter
                @endif
            </p>
        </div>

        <!-- Pending Approvals Card -->
        <div class="bg-gradient-to-br from-amber-50 to-yellow-50 p-6 rounded-2xl border-2 border-amber-100 shadow-lg hover:shadow-xl transition-shadow">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <p class="text-xs font-bold text-amber-400 uppercase tracking-wider">Pending Approvals</p>
                    <h4 class="text-4xl font-black text-amber-600 mt-2">{{ str_pad($pendingApprovalsCount, 2, '0', STR_PAD_LEFT) }}</h4>
                </div>
                <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center">
                    <i class="fa-solid fa-clipboard-check text-amber-600 text-xl"></i>
                </div>
            </div>
            <p class="text-xs text-amber-700 mt-3 font-bold">📋 Awaiting Principal Review</p>
            <p class="text-xs text-amber-600 mt-1">Requires final case closure</p>
        </div>
    </div>

    <!-- Recent Incidents Table -->
    <div class="bg-white rounded-2xl border-2 border-gray-200 shadow-xl relative z-20">
        <div class="px-7 py-6 bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200 flex justify-between items-center rounded-t-xl">
            <div>
                <h3 class="text-xl font-black text-gray-900">Recent Behavioral Incidents</h3>
                <p class="text-xs text-gray-600 mt-1">Track and manage student incidents effectively</p>
            </div>
            <div class="relative" x-data="{ showFilters: {{ request()->has('grade_level') || request()->has('section') ? 'true' : 'false' }} }">
                <div class="flex gap-3 items-center">
                    <form action="{{ route('dashboard') }}" method="GET" class="relative">
                        @if(request('grade_level')) <input type="hidden" name="grade_level" value="{{ request('grade_level') }}"> @endif
                        @if(request('section')) <input type="hidden" name="section" value="{{ request('section') }}"> @endif
                        <i class="fa-solid fa-magnifying-glass absolute left-4 top-3.5 text-gray-400"></i>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search records..." class="pl-11 pr-4 py-3 border-2 border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-green-600 focus:border-transparent outline-none w-64 bg-white font-medium shadow-sm">
                    </form>
                    <button @click="showFilters = !showFilters" class="px-5 py-3 border-2 border-gray-200 rounded-xl text-sm font-bold text-gray-700 hover:bg-gray-50 transition-colors bg-white shadow-sm flex items-center gap-2">
                        <i class="fa-solid fa-sliders text-green-700"></i> Filters
                        <span x-show="showFilters" class="hidden sm:inline text-xs bg-green-100 text-green-700 px-1.5 py-0.5 rounded ml-1">On</span>
                    </button>
                </div>
                
                <!-- Filter Dropdown -->
                <div x-show="showFilters" 
                     @click.away="showFilters = false"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 transform scale-95"
                     x-transition:enter-end="opacity-100 transform scale-100"
                     class="absolute right-0 top-full mt-3 w-72 bg-white rounded-xl shadow-2xl border border-gray-100 p-5 z-50 origin-top-right">
                    
                    <div class="flex justify-between items-center mb-4 pb-2 border-b border-gray-100">
                        <h4 class="text-sm font-bold text-gray-800">Filter Incidents</h4>
                        <button @click="showFilters = false" class="text-gray-400 hover:text-gray-600"><i class="fa-solid fa-xmark"></i></button>
                    </div>

                    <form action="{{ route('dashboard') }}" method="GET" class="space-y-4">
                        @if(request('search')) <input type="hidden" name="search" value="{{ request('search') }}"> @endif
                        
                        <div>
                            <label class="text-xs font-bold text-gray-500 uppercase block mb-1.5">Grade Level</label>
                            <select name="grade_level" class="block w-full border-gray-200 rounded-lg text-sm focus:ring-green-500 focus:border-green-500 bg-gray-50/50">
                                <option value="">All Levels</option>
                                <option value="7" {{ request('grade_level') == '7' ? 'selected' : '' }}>Grade 7</option>
                                <option value="8" {{ request('grade_level') == '8' ? 'selected' : '' }}>Grade 8</option>
                                <option value="9" {{ request('grade_level') == '9' ? 'selected' : '' }}>Grade 9</option>
                                <option value="10" {{ request('grade_level') == '10' ? 'selected' : '' }}>Grade 10</option>
                                <option value="11" {{ request('grade_level') == '11' ? 'selected' : '' }}>Grade 11</option>
                                <option value="12" {{ request('grade_level') == '12' ? 'selected' : '' }}>Grade 12</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="text-xs font-bold text-gray-500 uppercase block mb-1.5">Section</label>
                            <input type="text" name="section" value="{{ request('section') }}" placeholder="e.g. St. Paul" class="block w-full border-gray-200 rounded-lg text-sm focus:ring-green-500 focus:border-green-500 bg-gray-50/50">
                        </div>

                        <div class="flex gap-2 pt-2">
                            <button type="submit" class="flex-1 bg-green-700 hover:bg-green-800 text-white py-2.5 rounded-lg text-xs font-bold transition-all shadow-md shadow-green-900/10">Apply Filters</button>
                            <a href="{{ route('dashboard') }}" class="px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-lg text-xs font-bold transition-all text-center">Clear</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-gradient-to-r from-green-50 to-emerald-50 border-b-2 border-green-100">
                    <tr>
                        <th class="px-6 py-4 text-xs font-black uppercase text-green-800 tracking-wider">Date/Time</th>
                        <th class="px-6 py-4 text-xs font-black uppercase text-green-800 tracking-wider">Student Name</th>
                        <th class="px-6 py-4 text-xs font-black uppercase text-green-800 tracking-wider">Violation Type</th>
                        <th class="px-6 py-4 text-xs font-black uppercase text-green-800 tracking-wider">Reported By</th>
                        <th class="px-6 py-4 text-xs font-black uppercase text-green-800 tracking-wider">Status</th>
                        <th class="px-6 py-4 text-xs font-black uppercase text-green-800 tracking-wider">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                    @forelse($recentIncidents as $incident)
                    <tr class="hover:bg-green-50/30 transition-colors">
                        <td class="px-6 py-4 text-gray-600 text-sm font-medium">
                            {{ $incident->incident_date->format('M d, Y') }}<br>
                            <span class="text-xs text-gray-400">{{ $incident->incident_date->format('h:i A') }}</span>
                        </td>
                        <td class="px-6 py-4 font-bold text-gray-900">
                            @if($incident->students->isNotEmpty())
                                {{ $incident->students->first()->full_name }}
                                <br><span class="text-xs text-gray-500 font-normal">{{ $incident->students->first()->student_id }}</span>
                            @else
                                N/A
                            @endif
                        </td>
                        <td class="px-6 py-4 text-gray-700 font-semibold">{{ $incident->category->name ?? 'N/A' }}</td>
                        <td class="px-6 py-4 text-gray-600">{{ $incident->reporter->name ?? 'N/A' }}</td>
                        <td class="px-6 py-4">
                            @if($incident->status === 'pending_approval')
                                <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-black bg-amber-100 text-amber-800 border-2 border-amber-200 uppercase">
                                    <span class="w-1.5 h-1.5 bg-amber-500 rounded-full mr-2"></span>
                                    Pending
                                </span>
                            @elseif($incident->status === 'approved')
                                <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-black bg-green-100 text-green-800 border-2 border-green-200 uppercase">
                                    <i class="fa-solid fa-check mr-1.5 text-[10px]"></i>
                                    Approved
                                </span>
                            @elseif($incident->status === 'closed')
                                <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-black bg-gray-600 text-white border-2 border-gray-700 uppercase">
                                    Closed
                                </span>
                            @elseif($incident->status === 'under_review')
                                <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-black bg-yellow-100 text-yellow-800 border-2 border-yellow-200 uppercase">
                                    <i class="fa-solid fa-rotate-left mr-1.5 text-[10px]"></i>
                                    For Revision
                                </span>
                            @elseif($incident->status === 'rejected')
                                <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-black bg-red-100 text-red-800 border-2 border-red-200 uppercase">
                                    <i class="fa-solid fa-xmark mr-1.5 text-[10px]"></i>
                                    Rejected
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-black bg-gray-100 text-gray-800 border-2 border-gray-200 uppercase">{{ $incident->status }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <a href="{{ route('incidents.show', $incident) }}" class="inline-flex items-center gap-2 text-green-700 font-bold text-sm hover:text-green-900 hover:gap-3 transition-all">
                                View Details
                                <i class="fa-solid fa-arrow-right text-xs"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center">
                                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                    <i class="fa-solid fa-inbox text-gray-400 text-2xl"></i>
                                </div>
                                <p class="text-gray-500 font-semibold">No incidents recorded yet</p>
                                <p class="text-gray-400 text-sm mt-1">Start by logging a new incident</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- AI Suggestions -->
    <div class="bg-gray-900 rounded-xl p-8 text-white relative overflow-hidden card-shadow">
        <div class="relative z-10">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-8 h-8 rounded-lg bg-green-500 flex items-center justify-center text-white text-sm">
                    <i class="fa-solid fa-lightbulb"></i>
                </div>
                <h3 class="font-bold text-lg">AI-Driven Intervention Suggestions</h3>
            </div>
            
            @if($suggestions && $suggestions->isNotEmpty())
                @foreach($suggestions as $suggestion)
                <div class="max-w-3xl mb-6 last:mb-0">
                    <p class="text-green-300 text-[11px] font-bold uppercase tracking-widest mb-1">System Insight: {{ $suggestion->grade_level ?? 'General' }}</p>
                    <p class="text-gray-300 text-sm leading-relaxed mb-6">
                        {{ $suggestion->suggestion }} [cite: {{ $suggestion->incident_count ?? '269' }}].
                    </p>
                    <div class="flex gap-4">
                        <button class="bg-green-600 hover:bg-green-500 text-white px-6 py-2 rounded-lg text-xs font-bold transition-all shadow-lg">Approve Recommendation</button>
                        <button class="bg-transparent border border-gray-700 hover:bg-gray-800 text-gray-400 px-6 py-2 rounded-lg text-xs font-bold transition-all">Dismiss Suggestion</button>
                    </div>
                </div>
                @endforeach
            @else
                <div class="max-w-3xl">
                    <p class="text-green-300 text-[11px] font-bold uppercase tracking-widest mb-1">System Insight: General</p>
                    <p class="text-gray-300 text-sm leading-relaxed mb-6">
                        No AI suggestions available at this time. The system will analyze incident patterns and provide recommendations.
                    </p>
                </div>
            @endif
        </div>
        <div class="absolute right-0 top-0 w-64 h-full bg-gradient-to-l from-green-500/10 to-transparent"></div>
    </div>

</div>
@endsection
