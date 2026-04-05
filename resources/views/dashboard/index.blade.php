@extends('layouts.app')

@section('content')
<!-- Header -->
<header class="bg-white border-b border-gray-200 shadow-sm sticky top-0 z-30">
    <div class="px-8 lg:px-12 py-4 flex flex-wrap items-center justify-between gap-6">
        <div>
            <h1 class="text-2xl font-black text-gray-900 mt-1">Discipline Chairperson Dashboard</h1>
        </div>
        <div class="flex items-center gap-4">
            <div class="flex items-center gap-3 px-3 py-2 rounded-2xl border border-gray-200 bg-gray-50">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-r from-emerald-50 to-emerald-100 border border-emerald-200 flex items-center justify-center text-emerald-600">
                    <i class="fa-solid fa-user-shield"></i>
                </div>
                <div class="flex flex-col text-sm font-semibold text-gray-700 min-h-[40px] justify-center">
                    <span class="uppercase text-[10px] tracking-[0.3em] text-gray-400">Signed In</span>
                    <span>{{ Auth::user()->name ?? 'Discipline Lead' }}</span>
                </div>
            </div>
        </div>
    </div>
</header>

@php
    $quickAccessMeta = [
        'analytics' => (($quickStats['monthly_change'] ?? 0) >= 0 ? '+' : '') . ($quickStats['monthly_change'] ?? 0) . '% vs last month',
        'incidents' => number_format($quickStats['total_this_month'] ?? 0) . ' this month',
        'students' => number_format($quickStats['repeat_offenders'] ?? 0) . ' repeat cases',
        'reports' => ($pendingApprovalsCount ?? 0) . ' pending',
        'activity' => number_format($quickStats['under_review'] ?? 0) . ' under review',
        'attendance' => number_format($atRiskStudentsCount ?? 0) . ' at-risk',
    ];
@endphp
<section class="bg-green-900 border-b border-green-800 shadow-md text-white">
    <div class="px-6 lg:px-10 py-2.5 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
        <div>
            <p class="text-[10px] uppercase tracking-[0.35em] text-emerald-50">Quick Access</p>
        </div>
        <div class="w-full lg:w-auto overflow-x-auto">
            <div class="flex min-w-max items-center gap-2 text-sm font-semibold">
                @php
                    $quickAccessLinks = [
                        ['label' => 'Analytics', 'route' => 'admin.analytics', 'icon' => 'fa-wave-square', 'accent' => true, 'subtitle' => $quickAccessMeta['analytics']],
                        ['label' => 'Incidents', 'route' => 'incidents.index', 'icon' => 'fa-clipboard-list', 'subtitle' => $quickAccessMeta['incidents']],
                        ['label' => 'Students', 'route' => 'students.index', 'icon' => 'fa-users', 'subtitle' => $quickAccessMeta['students']],
                        ['label' => 'Reports', 'route' => 'reports.index', 'icon' => 'fa-file-lines', 'subtitle' => $quickAccessMeta['reports']],
                        ['label' => 'Activity Logs', 'route' => 'admin.activity-logs', 'icon' => 'fa-clock-rotate-left', 'subtitle' => $quickAccessMeta['activity']],
                        ['label' => 'Attendance', 'route' => 'attendance.index', 'icon' => 'fa-calendar-check', 'subtitle' => $quickAccessMeta['attendance']],
                    ];
                @endphp
                @foreach($quickAccessLinks as $link)
                    @php
                        $isActive = request()->routeIs($link['route'] . '*');
                        $baseClasses = 'inline-flex items-center gap-3 px-4 py-2 rounded-2xl border transition text-left';
                        if (($link['accent'] ?? false)) {
                            $classes = $baseClasses . ' bg-yellow-400 text-green-950 shadow shadow-yellow-500/40 hover:bg-yellow-300';
                        } else {
                            $classes = $baseClasses . ' bg-white/10 border-white/10 text-emerald-50 hover:bg-white/20';
                        }
                        if ($isActive && !($link['accent'] ?? false)) {
                            $classes .= ' ring-2 ring-white/40 bg-white/20';
                        }
                    @endphp
                    <a href="{{ route($link['route']) }}" class="{{ $classes }}" aria-label="{{ $link['label'] }} quick link">
                        <i class="fa-solid {{ $link['icon'] }} text-base"></i>
                        <span class="flex flex-col leading-tight">
                            <span>{{ $link['label'] }}</span>
                            @if(!empty($link['subtitle']))
                                <span class="text-[11px] font-normal text-emerald-50/80">{{ $link['subtitle'] }}</span>
                            @endif
                        </span>
                    </a>
                @endforeach
            </div>
        </div>
    </div>
</section>

<div class="px-10 py-8 space-y-8">

    <!-- Student Quick Search -->
    @php
        $studentSearchPresets = [
            ['label' => 'Grade 7', 'value' => 'Grade 7'],
            ['label' => 'Grade 10', 'value' => 'Grade 10'],
            ['label' => 'Top Violations', 'value' => 'Flagged'],
            ['label' => 'Attendance Risk', 'value' => 'Attendance Risk'],
        ];
    @endphp
    <div class="bg-white rounded-2xl border border-emerald-100 shadow-sm p-6" x-data="studentQuickSearch('{{ route('students.search') }}')" x-on:keydown.escape.window="closePanel()">
        <div class="relative w-full">
            <i class="fa-solid fa-magnifying-glass absolute left-4 top-3.5 text-gray-400"></i>
            <input type="text" x-model="query" x-on:input.debounce.300ms="handleInput" placeholder="Search students" class="w-full pl-11 pr-12 py-3 border-2 border-emerald-100 rounded-xl text-base focus:ring-2 focus:ring-emerald-500 focus:border-transparent outline-none shadow-sm">
            <div class="absolute right-4 top-3.5" x-show="loading" x-cloak>
                <svg class="animate-spin h-4 w-4 text-emerald-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                </svg>
            </div>
        </div>

        <div class="flex flex-wrap gap-2 mt-4 text-xs font-semibold text-emerald-700">
            @foreach($studentSearchPresets as $preset)
                <button type="button" class="px-3 py-1.5 rounded-full border border-emerald-100 bg-emerald-50 hover:bg-emerald-100 transition" x-on:click="applyPreset('{{ $preset['value'] }}')">
                    {{ $preset['label'] }}
                </button>
            @endforeach
        </div>

        <div class="mt-3" x-show="recentTerms.length" x-cloak>
            <p class="text-[11px] font-semibold uppercase tracking-[0.35em] text-gray-400 mb-2">Recent searches</p>
            <div class="flex flex-wrap gap-2">
                <template x-for="term in recentTerms" :key="term">
                    <button type="button" class="px-3 py-1.5 rounded-full border border-gray-200 bg-white text-xs font-semibold text-gray-600 hover:bg-gray-50" x-text="term" @click="applyPreset(term)"></button>
                </template>
            </div>
        </div>

        <div class="relative mt-4" x-show="open" x-cloak>
            <div class="absolute z-30 w-full bg-white border border-emerald-100 rounded-2xl shadow-2xl overflow-hidden" x-on:click.away="closePanel()" x-transition>
                <template x-if="results.length === 0 && !loading">
                    <div class="p-6 text-center text-gray-500 text-sm">
                        <i class="fa-solid fa-search mb-2 text-lg"></i>
                        <p>No students found for "<span x-text="query"></span>". Try another keyword.</p>
                    </div>
                </template>

                <template x-for="student in results" :key="student.id">
                    <div class="p-4 hover:bg-emerald-50 border-b border-emerald-50 last:border-b-0">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                            <div>
                                <p class="text-sm font-black text-gray-900" x-text="student.name"></p>
                                <p class="text-xs text-gray-500">
                                    ID: <span x-text="student.student_id"></span> • Grade <span x-text="student.grade_level"></span> - <span x-text="student.section || 'N/A'"></span>
                                </p>
                                <div class="flex flex-wrap gap-3 mt-2 text-xs text-gray-600 font-semibold">
                                    <span class="inline-flex items-center gap-1"><i class="fa-solid fa-exclamation-circle text-rose-500 text-[10px]"></i> Violations: <span x-text="student.incidents"></span></span>
                                    <span class="inline-flex items-center gap-1"><i class="fa-solid fa-user-clock text-amber-500 text-[10px]"></i> Absences: <span x-text="student.absent_days"></span></span>
                                    <span class="inline-flex items-center gap-1"><i class="fa-solid fa-hourglass-half text-sky-500 text-[10px]"></i> Tardies: <span x-text="student.tardy_days"></span></span>
                                </div>
                            </div>
                            <div class="flex flex-wrap gap-2">
                                <a :href="student.routes.view" class="px-3 py-2 text-xs font-bold rounded-lg border border-gray-200 text-gray-700 hover:bg-gray-100 transition" x-on:click="recordRecent(student.name)">View Details</a>
                                <a :href="student.routes.add_incident" class="px-3 py-2 text-xs font-bold rounded-lg bg-rose-600 text-white hover:bg-rose-700 transition" x-on:click="recordRecent(student.name)">Add Incident</a>
                                <a :href="student.routes.add_attendance" class="px-3 py-2 text-xs font-bold rounded-lg bg-amber-500 text-white hover:bg-amber-600 transition" x-on:click="recordRecent(student.name)">Add Attendance</a>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    @php
        $riskChange = $quickStats['repeat_offenders'] ?? 0;
        $riskBadge = $riskChange > 0
            ? ['text' => $riskChange . ' repeat cases', 'color' => 'text-red-600 bg-red-100']
            : ['text' => 'No repeat cases', 'color' => 'text-emerald-700 bg-emerald-50'];

        $incidentChange = $quickStats['monthly_change'] ?? 0;
        $incidentBadge = $mostCommonIncident
            ? ['text' => ($incidentChange >= 0 ? '+' : '') . $incidentChange . '% MoM', 'color' => $incidentChange >= 0 ? 'text-blue-600 bg-blue-100' : 'text-rose-600 bg-rose-50']
            : ['text' => 'Awaiting data', 'color' => 'text-gray-600 bg-gray-100'];

        $approvalQueue = $quickStats['under_review'] ?? 0;
        $approvalBadge = $approvalQueue > 0
            ? ['text' => $approvalQueue . ' under review', 'color' => 'text-amber-600 bg-amber-100']
            : ['text' => 'All caught up', 'color' => 'text-emerald-700 bg-emerald-50'];
    @endphp
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- At-Risk Students Card -->
        <div class="bg-white p-6 rounded-2xl border border-red-100 shadow-sm">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <p class="text-xs font-bold text-red-400 uppercase tracking-wider">At-Risk Students</p>
                    <h4 class="text-5xl font-black text-red-600 mt-2 leading-none">{{ str_pad($atRiskStudentsCount, 2, '0', STR_PAD_LEFT) }}</h4>
                    <span class="inline-flex items-center gap-1 text-[11px] font-semibold mt-2 px-2 py-1 rounded-full {{ $riskBadge['color'] }}">
                        <i class="fa-solid fa-arrow-trend-up text-[10px]"></i> {{ $riskBadge['text'] }}
                    </span>
                    <p class="text-xs text-red-500 font-semibold mt-2">{{ number_format($quickStats['total_this_month'] ?? 0) }} incidents logged this month.</p>
                </div>
                <div class="w-12 h-12 bg-red-50 rounded-xl flex items-center justify-center text-red-600">
                    <i class="fa-solid fa-triangle-exclamation text-red-600 text-xl"></i>
                </div>
            </div>
            <div class="bg-red-50 rounded-lg px-3 py-2 mt-3">
                <p class="text-xs text-red-700 font-bold">Action Required</p>
                <p class="text-xs text-red-600 mt-1">Early intervention recommended</p>
            </div>
        </div>

        <!-- Common Incident Card -->
        <div class="bg-white p-6 rounded-2xl border border-blue-100 shadow-sm">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <p class="text-xs font-bold text-blue-400 uppercase tracking-wider">Common Incident</p>
                    <h4 class="text-lg font-black text-blue-900 mt-2 leading-tight">
                        @if($mostCommonIncident)
                            {{ $mostCommonIncident->name ?? 'No data' }}
                        @else
                            No data
                        @endif
                    </h4>
                    <span class="inline-flex items-center gap-1 text-[11px] font-semibold mt-2 px-2 py-1 rounded-full {{ $incidentBadge['color'] }}">
                        <i class="fa-solid fa-chart-line text-[10px]"></i> {{ $incidentBadge['text'] }}
                    </span>
                    <p class="text-xs text-blue-500 font-semibold mt-2">{{ number_format($quickStats['total_this_month'] ?? 0) }} incident signals captured this month.</p>
                </div>
                <div class="w-12 h-12 bg-blue-50 rounded-xl flex items-center justify-center text-blue-600">
                    <i class="fa-solid fa-chart-line text-xl"></i>
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
        <div class="bg-white p-6 rounded-2xl border border-amber-100 shadow-sm">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <p class="text-xs font-bold text-amber-400 uppercase tracking-wider">Pending Approvals</p>
                    <h4 class="text-5xl font-black text-amber-600 mt-2 leading-none">{{ str_pad($pendingApprovalsCount, 2, '0', STR_PAD_LEFT) }}</h4>
                    <span class="inline-flex items-center gap-1 text-[11px] font-semibold mt-2 px-2 py-1 rounded-full {{ $approvalBadge['color'] }}">
                        <i class="fa-solid fa-hourglass-half text-[10px]"></i> {{ $approvalBadge['text'] }}
                    </span>
                    <p class="text-xs text-amber-600 font-semibold mt-2">{{ $pendingApprovalsCount }} records pending principal review.</p>
                </div>
                <div class="w-12 h-12 bg-amber-50 rounded-xl flex items-center justify-center text-amber-600">
                    <i class="fa-solid fa-clipboard-check text-xl"></i>
                </div>
            </div>
            <p class="text-xs text-amber-700 mt-3 font-bold">Awaiting Principal Review</p>
            <p class="text-xs text-amber-600 mt-1">Requires final case closure</p>
        </div>
    </div>

    <!-- Intervention Insights -->
    <section id="intervention-insights" class="bg-white rounded-3xl border border-emerald-100 shadow-sm p-8">
        <div class="flex flex-wrap items-center justify-between gap-4 mb-8">
            <div>
                <p class="text-xs font-bold text-emerald-600 uppercase tracking-[0.3em]">Targeted Support</p>
                <h3 class="text-2xl font-black text-gray-900 mt-1">Intervention Insights</h3>
                <p class="text-sm text-gray-500 mt-1">Surface risks, decide on a plan, and let leadership see the same timeline.</p>
            </div>
            <a href="{{ route('admin.analytics') }}" class="px-4 py-2 text-sm font-semibold text-emerald-700 bg-white border border-emerald-100 rounded-xl hover:bg-emerald-50 transition-all">View Full Analytics</a>
        </div>

        <div class="grid gap-6 lg:grid-cols-[minmax(0,2fr)_minmax(0,1fr)]">
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <p class="text-xs font-semibold tracking-[0.3em] text-gray-500 uppercase">Actionable Plans</p>
                    <span class="inline-flex items-center gap-2 text-[11px] font-bold px-3 py-1 rounded-full {{ $suggestionsSource === 'analytics' ? 'bg-indigo-50 text-indigo-700 border border-indigo-100' : ($suggestionsSource === 'manual' ? 'bg-emerald-50 text-emerald-700 border border-emerald-100' : 'bg-gray-100 text-gray-500 border border-gray-200') }}">
                        <i class="fa-solid fa-wave-square"></i>
                        {{ ucfirst($suggestionsSource) }} source
                    </span>
                </div>

                @if($suggestionsSource === 'none')
                    <div class="p-6 border border-dashed border-emerald-200 bg-white/80 rounded-2xl text-center">
                        <p class="text-sm font-semibold text-gray-700">No insights available yet.</p>
                        <p class="text-xs text-gray-500 mt-1">Keep logging incidents and attendance – the engine refreshes every hour.</p>
                    </div>
                @else
                    @php
                        $defaultOwner = auth()->user()->name ?? 'Discipline Chair';
                        $defaultDueDate = now()->addDays(7)->toDateString();
                        $analysisFallbackStart = now()->subDays(45)->toDateString();
                        $analysisFallbackEnd = now()->toDateString();
                    @endphp
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
                            $analysisStart = data_get($suggestion, 'analysis_period_start', $analysisFallbackStart);
                            $analysisEnd = data_get($suggestion, 'analysis_period_end', $analysisFallbackEnd);
                            $rowTone = $loop->odd ? 'bg-white' : 'bg-emerald-50/40';
                        @endphp
                        <article class="p-5 rounded-2xl border border-emerald-100 shadow-sm {{ $rowTone }}">
                            <div class="grid gap-4 lg:grid-cols-[minmax(0,1.8fr)_minmax(0,1fr)]">
                                <div>
                                    <div class="flex flex-wrap items-start justify-between gap-3">
                                        <div>
                                            <p class="text-[11px] font-bold uppercase tracking-[0.3em] text-emerald-600">{{ $contextLabel }}</p>
                                            <p class="text-sm font-black text-gray-900 mt-1">{{ $incidentLabel }}</p>
                                            <p class="text-xs text-gray-500 font-semibold">{{ $eventCount }} flagged events in the last cycle</p>
                                        </div>
                                        <span class="inline-flex items-center gap-1 text-xs font-semibold {{ $persisted ? 'text-emerald-700 bg-emerald-50 border border-emerald-100' : 'text-indigo-700 bg-indigo-50 border border-indigo-100' }} px-2.5 py-1 rounded-full">
                                            <i class="fa-solid {{ $persisted ? 'fa-clipboard-check' : 'fa-wand-magic-sparkles' }} text-[10px]"></i>
                                            {{ $persisted ? 'Manual Insight' : 'Analytics Insight' }}
                                        </span>
                                    </div>
                                    <p class="text-sm text-gray-700 leading-relaxed mt-3">{{ $suggestion->suggestion }}</p>
                                </div>
                                <div class="rounded-2xl bg-white/70 border border-emerald-100 p-4 flex flex-col gap-3">
                                    <div class="text-[11px] uppercase tracking-[0.2em] text-gray-400 font-semibold">
                                        Window: {{ \Illuminate\Support\Carbon::parse($analysisStart)->format('M d') }} – {{ \Illuminate\Support\Carbon::parse($analysisEnd)->format('M d, Y') }}
                                    </div>

                                    @if($persisted)
                                        <div class="flex flex-wrap gap-4 text-[12px] font-semibold text-gray-500">
                                            <span class="inline-flex items-center gap-1"><i class="fa-solid fa-user-tag text-emerald-500 text-[10px]"></i> Owner: {{ $assignmentOwner ?? 'Not assigned' }}</span>
                                            <span class="inline-flex items-center gap-1"><i class="fa-solid fa-calendar-day text-emerald-500 text-[10px]"></i> Due: {{ $assignmentDueLabel ?? 'Not set' }}</span>
                                        </div>
                                        <form method="POST" action="{{ route('interventions.decide', $suggestion) }}" class="space-y-3">
                                            @csrf
                                            <label class="text-[11px] font-semibold text-gray-500 uppercase tracking-[0.35em]" for="remarks-{{ $suggestion->id }}">Add Context</label>
                                            <input id="remarks-{{ $suggestion->id }}" type="text" name="remarks" placeholder="Optional note (max 1000 characters)" class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-2 text-xs font-semibold text-gray-600 focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                                            <div class="flex flex-wrap gap-2">
                                                <button type="submit" name="decision" value="apply" class="px-4 py-2 text-xs font-black rounded-lg bg-emerald-600 text-white hover:bg-emerald-500 transition flex items-center gap-2">
                                                    <i class="fa-solid fa-circle-check text-[10px]"></i> Apply Plan
                                                </button>
                                                <button type="submit" name="decision" value="dismiss" class="px-4 py-2 text-xs font-black rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-50 transition flex items-center gap-2">
                                                    <i class="fa-solid fa-ban text-[10px]"></i> Dismiss
                                                </button>
                                            </div>
                                        </form>
                                    @else
                                        <form method="POST" action="{{ route('interventions.convert') }}" class="space-y-3">
                                            @csrf
                                            <input type="hidden" name="grade_level" value="{{ $gradeLevel }}">
                                            <input type="hidden" name="section" value="{{ $sectionValue }}">
                                            <input type="hidden" name="incident_type" value="{{ $incidentLabel }}">
                                            <input type="hidden" name="incident_count" value="{{ $eventCount }}">
                                            <input type="hidden" name="analysis_period_start" value="{{ $analysisStart }}">
                                            <input type="hidden" name="analysis_period_end" value="{{ $analysisEnd }}">
                                            <input type="hidden" name="suggestion" value="{{ $suggestion->suggestion }}">
                                            <div class="grid gap-3 sm:grid-cols-2">
                                                <label class="text-xs font-semibold text-gray-600">Assign To
                                                    <input type="text" name="assigned_to" value="{{ $defaultOwner }}" class="mt-1 w-full rounded-xl border border-gray-200 bg-white px-3 py-2 text-xs font-semibold text-gray-700 focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                                                </label>
                                                <label class="text-xs font-semibold text-gray-600">Follow-Up Due
                                                    <input type="date" name="assignment_due_at" value="{{ $defaultDueDate }}" class="mt-1 w-full rounded-xl border border-gray-200 bg-white px-3 py-2 text-xs font-semibold text-gray-700 focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                                                </label>
                                            </div>
                                            <button type="submit" class="w-full px-4 py-2 text-xs font-black rounded-lg bg-indigo-600 text-white hover:bg-indigo-500 transition flex items-center justify-center gap-2">
                                                <i class="fa-solid fa-repeat text-[10px]"></i> Convert Insight to Plan
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </article>
                    @endforeach
                @endif
            </div>

            <div class="space-y-4">
                <div class="bg-gradient-to-br from-green-900 to-emerald-700 text-white rounded-2xl shadow-sm p-6">
                    <p class="text-xs font-bold uppercase tracking-[0.3em] text-emerald-100">System Snapshot</p>
                    <h4 class="text-lg font-black leading-tight mt-1">Leadership Pulse</h4>
                    <div class="mt-5 grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <p class="text-[11px] uppercase tracking-[0.3em] text-emerald-200">Incidents This Month</p>
                            <p class="text-2xl font-black">{{ number_format($quickStats['total_this_month'] ?? 0) }}</p>
                            <p class="text-xs text-emerald-100">{{ ($quickStats['monthly_change'] ?? 0) >= 0 ? '+' : '' }}{{ $quickStats['monthly_change'] ?? 0 }}% vs last month</p>
                        </div>
                        <div>
                            <p class="text-[11px] uppercase tracking-[0.3em] text-emerald-200">Under Review</p>
                            <p class="text-2xl font-black">{{ number_format($quickStats['under_review'] ?? 0) }}</p>
                            <p class="text-xs text-emerald-100">Workflow items awaiting QA</p>
                        </div>
                        <div>
                            <p class="text-[11px] uppercase tracking-[0.3em] text-emerald-200">Repeat Cases</p>
                            <p class="text-2xl font-black">{{ number_format($quickStats['repeat_offenders'] ?? 0) }}</p>
                            <p class="text-xs text-emerald-100">Tracked over past 30 days</p>
                        </div>
                        <div>
                            <p class="text-[11px] uppercase tracking-[0.3em] text-emerald-200">At-Risk Roster</p>
                            <p class="text-2xl font-black">{{ str_pad($atRiskStudentsCount, 2, '0', STR_PAD_LEFT) }}</p>
                            <p class="text-xs text-emerald-100">Students needing follow-up</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white border border-emerald-100 rounded-2xl shadow-sm p-6">
                    <p class="text-xs font-bold text-gray-500 uppercase tracking-[0.3em]">Plan Timeline</p>
                    <h4 class="text-lg font-black text-gray-900 mt-1">Recent Adopted Plans</h4>

                    <div class="mt-4 space-y-4">
                        @forelse($recentPlans as $plan)
                            @php
                                $statusPalette = [
                                    'implemented' => 'bg-emerald-50 text-emerald-700 border border-emerald-100',
                                    'approved' => 'bg-blue-50 text-blue-700 border border-blue-100',
                                    'rejected' => 'bg-rose-50 text-rose-700 border border-rose-100',
                                ];
                                $badgeClass = $statusPalette[$plan->status] ?? 'bg-gray-100 text-gray-600 border border-gray-200';
                            @endphp
                            <div class="relative pl-5">
                                <span class="absolute left-0 top-2 w-2 h-2 rounded-full bg-emerald-500"></span>
                                <div class="bg-gray-50 rounded-xl p-4 border border-gray-100">
                                    <div class="flex items-center justify-between gap-3">
                                        <div>
                                            <p class="text-sm font-black text-gray-900">{{ $plan->incident_type }}</p>
                                            <p class="text-xs text-gray-500">{{ $plan->grade_level ? 'Grade ' . $plan->grade_level : 'All Grades' }}@if($plan->section) · Section {{ $plan->section }} @endif</p>
                                        </div>
                                        <span class="text-[11px] font-bold px-2.5 py-1 rounded-full {{ $badgeClass }}">{{ ucfirst($plan->status) }}</span>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-2">{{ optional($plan->decisionMaker)->name ?? 'Leadership' }} &middot; {{ optional($plan->decided_at)->diffForHumans() ?? 'Pending timestamp' }}</p>
                                    @if($plan->assigned_to || $plan->assignment_due_at)
                                        <p class="text-[11px] text-gray-500 mt-1">Owner: {{ $plan->assigned_to ?? 'Unassigned' }} @if($plan->assignment_due_at) • Due {{ $plan->assignment_due_at->format('M d, Y') }} @endif</p>
                                    @endif
                                    @if($plan->decision_remarks)
                                        <p class="text-xs text-gray-600 mt-2 italic">“{{ $plan->decision_remarks }}”</p>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="p-5 border border-dashed border-gray-200 rounded-xl text-center">
                                <p class="text-sm font-semibold text-gray-700">No adopted plans yet.</p>
                                <p class="text-xs text-gray-500 mt-1">Decisions taken here will appear across principal and adviser dashboards.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Recent Incidents Table -->
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm relative z-10">
        <div class="px-7 py-6 bg-gray-50 border-b border-gray-200 flex justify-between items-center rounded-t-xl">
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
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search records..." 
                               class="pl-11 pr-4 py-3 border-2 border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-green-600 focus:border-transparent outline-none w-64 bg-white font-medium shadow-sm">
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
                <thead class="bg-gray-50 border-b border-gray-200">
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

    <!-- Spacer removed for previous insights block -->

</div>
<script>
const registerStudentQuickSearch = () => {
    Alpine.data('studentQuickSearch', (endpoint) => ({
        endpoint,
        query: '',
        results: [],
        open: false,
        loading: false,
        controller: null,
        recentsKey: 'discipline_student_recents',
        recentTerms: [],
        init() {
            this.loadRecents();
        },
        handleInput() {
            const trimmed = this.query.trim();
            if (trimmed.length < 2) {
                this.results = [];
                this.open = false;
                if (this.controller) {
                    this.controller.abort();
                }
                return;
            }
            this.fetchStudents(trimmed);
        },
        fetchStudents(value) {
            if (this.controller) {
                this.controller.abort();
            }
            this.loading = true;
            this.open = true;
            this.controller = new AbortController();

            fetch(`${endpoint}?query=${encodeURIComponent(value)}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                signal: this.controller.signal,
            })
                .then((response) => {
                    if (!response.ok) {
                        throw new Error('Unable to fetch students');
                    }
                    return response.json();
                })
                .then((payload) => {
                    this.results = payload.data || [];
                })
                .catch((error) => {
                    if (error.name === 'AbortError') {
                        return;
                    }
                    console.error(error);
                })
                .finally(() => {
                    this.loading = false;
                });
        },
        closePanel() {
            this.open = false;
        },
        applyPreset(term) {
            this.query = term;
            this.recordRecent(term);
            this.handleInput();
        },
        recordRecent(term) {
            if (!term) {
                return;
            }
            const sanitized = term.trim();
            if (!sanitized) {
                return;
            }
            this.recentTerms = [sanitized, ...this.recentTerms.filter((item) => item !== sanitized)].slice(0, 4);
            localStorage.setItem(this.recentsKey, JSON.stringify(this.recentTerms));
        },
        loadRecents() {
            try {
                const stored = localStorage.getItem(this.recentsKey);
                this.recentTerms = stored ? JSON.parse(stored) : [];
            } catch (error) {
                console.warn('Unable to parse recent student searches', error);
                this.recentTerms = [];
            }
        },
    }));
};

if (window.Alpine) {
    registerStudentQuickSearch();
    document.querySelectorAll('[x-data^="studentQuickSearch"]').forEach((element) => {
        Alpine.initTree(element);
    });
} else {
    document.addEventListener('alpine:init', registerStudentQuickSearch);
}
</script>
@endsection
