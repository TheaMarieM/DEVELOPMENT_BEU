@extends('layouts.principal')

@section('content')
@php
    $userName = auth()->user()->name ?? 'Sr. Principal';
    $userRole = auth()->user()->role->name ?? 'principal';
    $dashboardTitle = $userRole === 'assistant_principal' ? "Assistant Principal's Dashboard" : "Principal's Dashboard";
    $roleDisplayName = $userRole === 'assistant_principal' ? 'Asst. Principal' : 'Principal';
    $atRiskDisplay = str_pad($atRiskStudentsCount, 2, '0', STR_PAD_LEFT);
    $pendingDisplay = str_pad($pendingApprovalsCount, 2, '0', STR_PAD_LEFT);
    $commonIncidentCopy = $commonIncidentTotal > 0
        ? $commonIncidentName . ' · ' . $commonIncidentTotal . ' cases'
        : 'No active violation trend';
    $statusMeta = [
        'pending_approval' => ['label' => 'Pending Approval', 'class' => 'bg-blue-50 text-blue-700 border border-blue-100'],
        'under_review' => ['label' => 'Returned for Revision', 'class' => 'bg-yellow-100 text-yellow-800 border border-yellow-200'],
        'reported' => ['label' => 'Reported', 'class' => 'bg-blue-50 text-blue-600 border border-blue-200'],
        'approved' => ['label' => 'Closed', 'class' => 'bg-green-50 text-green-700 border border-green-100'],
        'closed' => ['label' => 'Archived', 'class' => 'bg-gray-600 text-white border border-gray-700'],
    ];
@endphp

<header class="bg-gradient-to-r from-green-800 to-green-700 border-b border-green-900 px-8 py-6 flex flex-wrap gap-4 justify-between items-center sticky top-0 z-30 shadow-lg">
    <div>
        <h1 class="text-2xl font-black text-white">{{ $dashboardTitle }}</h1>
        <p class="text-sm text-green-100 mt-1">Welcome back, {{ $userName }}</p>
    </div>
    <div class="flex items-center gap-4">
        <a href="#incidents-overview" class="px-6 py-3 text-sm font-bold rounded-xl bg-white text-green-800 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all inline-flex items-center gap-2">
            <i class="fa-solid fa-clipboard-check"></i> Review Incidents
        </a>
        <div class="h-10 w-px bg-green-600"></div>
        <div class="flex items-center gap-3">
            <p class="text-sm font-bold text-white">{{ $roleDisplayName }}</p>
            <div class="w-11 h-11 rounded-xl bg-white/20 backdrop-blur-sm border-2 border-white/30 flex items-center justify-center text-white">
                <i class="fa-solid fa-user-tie"></i>
            </div>
        </div>
    </div>
</header>

<div class="p-8 space-y-8">
    <section class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-gradient-to-br from-red-50 to-rose-50 border-2 border-red-100 rounded-2xl p-6 shadow-lg hover:shadow-xl transition-shadow">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <p class="text-xs font-bold text-red-400 uppercase tracking-wider">At-Risk Students</p>
                    <h3 class="text-4xl font-black text-red-600 mt-2">{{ $atRiskDisplay }}</h3>
                </div>
                <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center">
                    <i class="fa-solid fa-triangle-exclamation text-red-600 text-xl"></i>
                </div>
            </div>
            <div class="bg-red-100/50 rounded-lg px-3 py-2">
                <p class="text-xs text-red-700 font-bold">⚠️ Action Required</p>
                <p class="text-xs text-red-600 mt-1">Early intervention recommended</p>
            </div>
        </div>
        
        <div class="bg-gradient-to-br from-blue-50 to-cyan-50 border-2 border-blue-100 rounded-2xl p-6 shadow-lg hover:shadow-xl transition-shadow">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <p class="text-xs font-bold text-blue-400 uppercase tracking-wider">Common Incident (Q4)</p>
                    <h3 class="text-lg font-black text-blue-900 mt-2 leading-tight">{{ $commonIncidentName }}</h3>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                    <i class="fa-solid fa-chart-line text-blue-600 text-xl"></i>
                </div>
            </div>
            <p class="text-xs text-blue-600 font-medium">{{ $commonIncidentCopy }}</p>
        </div>
        
        <div class="bg-gradient-to-br from-amber-50 to-yellow-50 border-2 border-amber-100 rounded-2xl p-6 shadow-lg hover:shadow-xl transition-shadow">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <p class="text-xs font-bold text-amber-400 uppercase tracking-wider">Pending Approvals</p>
                    <h3 class="text-4xl font-black text-amber-600 mt-2">{{ $pendingDisplay }}</h3>
                </div>
                <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center">
                    <i class="fa-solid fa-clipboard-check text-amber-600 text-xl"></i>
                </div>
            </div>
            <p class="text-xs text-amber-700 font-bold">📋 Pending Principal Review</p>
            <p class="text-xs text-amber-600 mt-1">Requires final case closure</p>
        </div>
    </section>

    <section class="bg-white border-2 border-green-100 rounded-3xl shadow-xl p-8 space-y-6">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <p class="text-xs font-bold text-green-600 uppercase tracking-[0.35em]">Coordinated Interventions</p>
                <h2 class="text-2xl font-black text-gray-900 mt-1">Apply or monitor support plans</h2>
                <p class="text-xs text-gray-500 mt-1">Actions here sync with the discipline chair and advisers instantly.</p>
            </div>
            <span class="inline-flex items-center gap-2 text-[11px] font-extrabold px-4 py-1.5 rounded-full {{ $suggestionsSource === 'analytics' ? 'bg-slate-900 text-white' : ($suggestionsSource === 'manual' ? 'bg-green-50 text-green-800 border border-green-200' : 'bg-gray-100 text-gray-600 border border-gray-200') }}">
                <i class="fa-solid fa-wave-square"></i>
                {{ ucfirst($suggestionsSource) }} feed
            </span>
        </div>

        <div class="grid gap-6 lg:grid-cols-[minmax(0,1.7fr)_minmax(0,1fr)]">
            <div class="space-y-4">
                @if($suggestionsSource === 'none')
                    <div class="border border-dashed border-green-200 rounded-2xl p-6 text-center bg-green-50/50">
                        <p class="text-sm font-semibold text-gray-700">No pending insights.</p>
                        <p class="text-xs text-gray-500 mt-1">Once the discipline office submits a plan, it will appear here.</p>
                    </div>
                @else
                    @foreach($suggestions as $suggestion)
                        @php
                            $persisted = isset($suggestion->id);
                            $gradeLevel = data_get($suggestion, 'grade_level');
                            $sectionValue = data_get($suggestion, 'section');
                            $scopeLabel = data_get($suggestion, 'scope_label');
                            $contextLabel = $scopeLabel ?: ($gradeLevel ? 'Grade ' . $gradeLevel : 'All Grade Levels');
                            if (!$scopeLabel && $sectionValue) {
                                $contextLabel .= ' · Section ' . $sectionValue;
                            }
                            $incidentLabel = data_get($suggestion, 'incident_type', 'Behavioral Trend');
                            $eventCount = data_get($suggestion, 'incident_count', 0);
                            $assignmentOwner = data_get($suggestion, 'assigned_to');
                            $assignmentDue = data_get($suggestion, 'assignment_due_at');
                            $assignmentDueLabel = $assignmentDue ? \Illuminate\Support\Carbon::parse($assignmentDue)->format('M d, Y') : null;
                        @endphp
                        <article class="border border-green-100 rounded-2xl p-5 bg-gradient-to-br from-white to-green-50/20">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="text-[11px] font-bold uppercase tracking-[0.35em] text-green-600">{{ $contextLabel }}</p>
                                    <h3 class="text-lg font-black text-gray-900 mt-1">{{ $incidentLabel }}</h3>
                                    <p class="text-xs text-gray-500 font-semibold">{{ $eventCount }} flagged events · updated {{ now()->format('M') }}</p>
                                </div>
                                <span class="inline-flex items-center gap-1.5 text-[11px] font-black px-2.5 py-1 rounded-full {{ $persisted ? 'bg-green-100 text-green-800 border border-green-200' : 'bg-slate-900 text-white' }}">
                                    <i class="fa-solid {{ $persisted ? 'fa-clipboard-check' : 'fa-wand-magic-sparkles' }} text-[10px]"></i>
                                    {{ $persisted ? 'Workflow Ready' : 'Auto Insight' }}
                                </span>
                            </div>
                            <p class="text-sm text-gray-700 leading-relaxed mt-3">{{ $suggestion->suggestion }}</p>

                            @if($persisted)
                                <div class="mt-3 flex flex-wrap gap-4 text-[11px] font-semibold text-gray-500">
                                    <span class="inline-flex items-center gap-1"><i class="fa-solid fa-user-tag text-green-500 text-[10px]"></i> Owner: {{ $assignmentOwner ?? 'Not assigned' }}</span>
                                    <span class="inline-flex items-center gap-1"><i class="fa-solid fa-calendar-day text-green-500 text-[10px]"></i> Due: {{ $assignmentDueLabel ?? 'Not set' }}</span>
                                </div>
                                <form method="POST" action="{{ route('interventions.decide', $suggestion) }}" class="mt-4 space-y-3">
                                    @csrf
                                    <input type="text" name="remarks" placeholder="Optional note for advisers" class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-2 text-xs font-semibold text-gray-600 focus:ring-2 focus:ring-green-500 focus:border-transparent">
                                    <div class="flex flex-wrap gap-2">
                                        <button type="submit" name="decision" value="apply" class="px-4 py-2 rounded-xl bg-green-700 text-white text-xs font-black inline-flex items-center gap-2 hover:bg-green-800 transition">
                                            <i class="fa-solid fa-circle-check text-[10px]"></i>
                                            Apply Plan
                                        </button>
                                        <button type="submit" name="decision" value="dismiss" class="px-4 py-2 rounded-xl border border-gray-300 text-gray-600 text-xs font-black inline-flex items-center gap-2 hover:bg-gray-50 transition">
                                            <i class="fa-solid fa-ban text-[10px]"></i>
                                            Dismiss
                                        </button>
                                    </div>
                                </form>
                            @else
                                <p class="mt-4 text-xs text-gray-500 italic font-semibold">Auto generated – ask the discipline chair to formalize it if action is required.</p>
                            @endif
                        </article>
                    @endforeach
                @endif
            </div>

            <div class="border border-green-100 rounded-2xl p-6 bg-white shadow">
                <p class="text-xs font-bold text-gray-500 uppercase tracking-[0.35em]">Plan History</p>
                <h3 class="text-lg font-black text-gray-900 mt-1">Leadership Timeline</h3>
                <div class="mt-4 space-y-4">
                    @forelse($recentPlans as $plan)
                        @php
                            $palette = [
                                'implemented' => 'bg-green-50 text-green-800 border border-green-200',
                                'approved' => 'bg-blue-50 text-blue-800 border border-blue-200',
                                'rejected' => 'bg-rose-50 text-rose-800 border border-rose-200',
                            ];
                            $badgeClass = $palette[$plan->status] ?? 'bg-gray-100 text-gray-700 border border-gray-200';
                        @endphp
                        <div class="relative pl-5">
                            <span class="absolute left-0 top-2 w-2 h-2 rounded-full bg-green-500"></span>
                            <div class="rounded-2xl border border-gray-100 bg-gray-50 p-4">
                                <div class="flex items-center justify-between gap-3">
                                    <div>
                                        <p class="text-sm font-black text-gray-900">{{ $plan->incident_type }}</p>
                                        <p class="text-xs text-gray-500">{{ $plan->grade_level ? 'Grade ' . $plan->grade_level : 'All Grades' }}@if($plan->section) · Section {{ $plan->section }} @endif</p>
                                    </div>
                                    <span class="text-[11px] font-bold px-2.5 py-1 rounded-full {{ $badgeClass }}">{{ ucfirst($plan->status) }}</span>
                                </div>
                                <p class="text-xs text-gray-500 mt-2">{{ optional($plan->decisionMaker)->name ?? 'Discipline Office' }} &middot; {{ optional($plan->decided_at)->diffForHumans() ?? 'N/A' }}</p>
                                @if($plan->assigned_to || $plan->assignment_due_at)
                                    <p class="text-[11px] text-gray-500 mt-1">Owner: {{ $plan->assigned_to ?? 'Unassigned' }} @if($plan->assignment_due_at) • Due {{ $plan->assignment_due_at->format('M d, Y') }} @endif</p>
                                @endif
                                @if($plan->decision_remarks)
                                    <p class="text-xs text-gray-600 italic mt-2">“{{ $plan->decision_remarks }}”</p>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="p-5 border border-dashed border-gray-200 rounded-2xl text-center">
                            <p class="text-sm font-semibold text-gray-700">No plan decisions logged yet.</p>
                            <p class="text-xs text-gray-500 mt-1">When a plan is applied, details will appear here for quick reference.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </section>

    <section class="bg-white border-2 border-gray-200 rounded-2xl overflow-hidden shadow-xl" id="incidents-overview">
        <div class="px-7 py-6 bg-gradient-to-r from-gray-50 to-gray-100 border-b-2 border-gray-200 flex flex-wrap gap-3 justify-between items-center">
            <div>
                <p class="text-xs uppercase tracking-wider text-gray-500 font-bold">Incidents Overview</p>
                <h2 class="text-2xl font-black text-gray-900 mt-1">Cases Routed for Admin Review</h2>
                <p class="text-xs text-gray-600 mt-1">Track real-time submissions, severity, and routing officer</p>
            </div>
                <div class="flex gap-3">
                    <form method="GET" action="{{ route('principal.dashboard') }}" class="relative" x-data="{ searchOpen: false }">
                        @if(request('status'))
                            <input type="hidden" name="status" value="{{ request('status') }}">
                        @endif
                        <div class="flex items-center gap-2">
                            <input type="text" name="search" value="{{ request('search') }}" 
                                   placeholder="Search by student name..." 
                                   x-show="searchOpen" x-cloak
                                   class="px-4 py-2.5 text-xs border-2 border-gray-200 rounded-xl focus:border-green-500 focus:ring-0 transition-all w-64">
                            <button type="button" @click="searchOpen = !searchOpen" 
                                    class="px-5 py-2.5 text-xs font-bold border-2 border-gray-200 rounded-xl text-gray-700 hover:bg-white transition-colors bg-gray-50 flex items-center gap-2">
                                <i class="fa-solid fa-magnifying-glass"></i> 
                                <span x-show="!searchOpen">Search</span>
                                @if(request('search'))
                                    <span class="w-2 h-2 bg-green-600 rounded-full"></span>
                                @endif
                            </button>
                            <button type="submit" x-show="searchOpen" x-cloak
                                    class="px-5 py-2.5 text-xs font-bold bg-green-600 hover:bg-green-700 text-white rounded-xl transition-colors">
                                Go
                            </button>
                            @if(request('search'))
                                <a href="{{ route('principal.dashboard', request()->except('search')) }}" x-show="searchOpen" x-cloak
                                   class="px-3 py-2.5 text-xs font-bold text-gray-500 hover:text-gray-700 transition-colors">
                                    <i class="fa-solid fa-times"></i>
                                </a>
                            @endif
                        </div>
                    </form>
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="px-5 py-2.5 text-xs font-bold border-2 border-gray-200 rounded-xl text-gray-700 hover:bg-white transition-colors bg-gray-50 flex items-center gap-2">
                            <i class="fa-solid fa-filter"></i> Filters
                            @if(request('status'))
                                <span class="w-2 h-2 bg-green-600 rounded-full"></span>
                            @endif
                        </button>
                        <div x-show="open" @click.away="open = false" x-cloak class="absolute right-0 mt-2 w-64 bg-white rounded-xl shadow-xl border-2 border-gray-200 z-50">
                            <div class="p-4">
                                <h3 class="text-xs font-bold text-gray-700 uppercase tracking-wider mb-3">Filter by Status</h3>
                                <form method="GET" action="{{ route('principal.dashboard') }}" class="space-y-2">
                                    <label class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-gray-50 cursor-pointer transition-colors">
                                        <input type="radio" name="status" value="" {{ !request('status') ? 'checked' : '' }} onchange="this.form.submit()" class="text-green-600 focus:ring-green-500">
                                        <span class="text-sm text-gray-700 font-medium">All Statuses</span>
                                    </label>
                                    <label class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-gray-50 cursor-pointer transition-colors">
                                        <input type="radio" name="status" value="pending_approval" {{ request('status') === 'pending_approval' ? 'checked' : '' }} onchange="this.form.submit()" class="text-green-600 focus:ring-green-500">
                                        <span class="text-sm text-gray-700 font-medium">Pending Approval</span>
                                    </label>
                                    <label class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-gray-50 cursor-pointer transition-colors">
                                        <input type="radio" name="status" value="under_review" {{ request('status') === 'under_review' ? 'checked' : '' }} onchange="this.form.submit()" class="text-green-600 focus:ring-green-500">
                                        <span class="text-sm text-gray-700 font-medium">Returned for Revision</span>
                                    </label>
                                </form>
                            </div>
                        </div>
                    </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gradient-to-r from-green-50 to-emerald-50 text-xs uppercase tracking-wider text-green-800 border-b-2 border-green-100">
                    <tr>
                        <th class="px-6 py-4 text-left font-black">Date / Time</th>
                        <th class="px-6 py-4 text-left font-black">Student Name</th>
                        <th class="px-6 py-4 text-left font-black">Violation Type</th>
                        <th class="px-6 py-4 text-left font-black">Reported By</th>
                        <th class="px-6 py-4 text-left font-black">Status</th>
                        <th class="px-6 py-4 text-left font-black">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($incidentsOverview as $incident)
                        @php
                            $meta = $statusMeta[$incident->status] ?? ['label' => ucfirst(str_replace('_', ' ', $incident->status)), 'class' => 'bg-gray-100 text-gray-600 border-2 border-gray-200'];
                            $primaryStudent = $incident->students->first()?->full_name;
                            $additionalCount = max(0, $incident->students->count() - 1);
                        @endphp
                        <tr class="hover:bg-green-50/30 transition-colors">
                            <td class="px-6 py-4 font-semibold text-gray-700">
                                {{ optional($incident->incident_date)->format('M d, Y') ?? '—' }}<br>
                                <span class="text-xs text-gray-500">{{ optional($incident->incident_date)->format('h:i A') ?? '' }}</span>
                            </td>
                            <td class="px-6 py-4 font-bold text-gray-900">
                                {{ $primaryStudent ?? 'Student record withheld' }}
                                @if($additionalCount > 0)
                                    <br><span class="text-xs text-gray-500 font-normal">+{{ $additionalCount }} more</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-gray-700 font-semibold">{{ $incident->category->name ?? 'Uncategorized' }}</td>
                            <td class="px-6 py-4 text-gray-600">{{ $incident->reporter->name ?? 'Discipline Office' }}</td>
                            <td class="px-6 py-4">
                                <span class="text-xs font-black uppercase tracking-wider px-3 py-1.5 rounded-full {{ $meta['class'] }}">{{ $meta['label'] }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <a href="{{ route('principal.incidents.show', $incident) }}" class="inline-flex items-center gap-2 text-green-700 font-bold text-sm hover:text-green-900 hover:gap-3 transition-all">
                                    View Details <i class="fa-solid fa-arrow-right text-xs"></i>
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
                                    <p class="text-gray-500 font-semibold">No incidents routed for approval yet</p>
                                    <p class="text-gray-400 text-sm mt-1">All clear for now!</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 border-t-2 border-gray-100 bg-gray-50">
            {{ $incidentsOverview->links() }}
        </div>
    </section>
</div>
@endsection
