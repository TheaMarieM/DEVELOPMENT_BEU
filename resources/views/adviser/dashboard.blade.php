@extends('layouts.adviser')

@section('content')
@php
    $userName = auth()->user()->name ?? 'Adviser';
    $sectionName = auth()->user()->section ?? 'Adviser';
    $adviserTitle = $sectionName ? $sectionName . "'s Adviser" : 'Adviser';
@endphp

<header class="bg-white border-b border-gray-200 px-8 py-4 flex flex-wrap gap-4 justify-between items-center sticky top-0 z-30 shadow-sm">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Adviser's Dashboard</h1>
        <p class="text-sm text-gray-500 mt-1">Welcome back, {{ $userName }}</p>
    </div>
    <div class="flex items-center gap-4">
        <a href="{{ route('adviser.students.create') }}" class="px-6 py-3 text-sm font-semibold rounded-lg bg-green-700 text-white shadow hover:bg-green-800 transition-colors inline-flex items-center gap-2">
            <i class="fa-solid fa-user-plus"></i> Register Student
        </a>
        <div class="h-10 w-px bg-gray-300"></div>
        <div class="flex items-center gap-3">
            <p class="text-sm font-bold text-gray-900">{{ $adviserTitle }}</p>
            <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center text-gray-600">
                <i class="fa-solid fa-user"></i>
            </div>
        </div>
    </div>
</header>

<div class="p-8 space-y-8">
    <section class="grid grid-cols-1 md:grid-cols-4 gap-5">
        <div class="bg-white border border-gray-200 rounded-3xl p-6 card-shadow">
            <div class="flex items-center justify-between">
                <p class="text-xs uppercase tracking-[0.2em] text-gray-400 font-semibold">Total Advisees</p>
                <span class="w-10 h-10 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center">
                    <i class="fa-solid fa-users"></i>
                </span>
            </div>
            <h3 class="text-4xl font-black text-gray-900 mt-3">{{ str_pad($totalAdvisees, 2, '0', STR_PAD_LEFT) }}</h3>
            <p class="text-xs text-gray-500 mt-2">Students under your advisory</p>
        </div>
        <div class="bg-white border border-gray-200 rounded-3xl p-6 card-shadow">
            <div class="flex items-center justify-between">
                <p class="text-xs uppercase tracking-[0.2em] text-gray-400 font-semibold">Total Incidents</p>
                <span class="w-10 h-10 rounded-full bg-rose-50 text-rose-600 flex items-center justify-center">
                    <i class="fa-solid fa-clipboard-list"></i>
                </span>
            </div>
            <h3 class="text-4xl font-black text-rose-600 mt-3">{{ str_pad($totalIncidents, 2, '0', STR_PAD_LEFT) }}</h3>
            <p class="text-xs text-gray-500 mt-2">Class total incidents</p>
        </div>
        <div class="bg-white border border-gray-200 rounded-3xl p-6 card-shadow">
            <div class="flex items-center justify-between">
                <p class="text-xs uppercase tracking-[0.2em] text-gray-400 font-semibold">Total Tardy</p>
                <span class="w-10 h-10 rounded-full bg-amber-50 text-amber-600 flex items-center justify-center">
                    <i class="fa-solid fa-clock"></i>
                </span>
            </div>
            <h3 class="text-4xl font-black text-amber-600 mt-3">{{ str_pad($totalTardy, 2, '0', STR_PAD_LEFT) }}</h3>
            <p class="text-xs text-gray-500 mt-2">Class total tardy records</p>
        </div>
        <div class="bg-white border border-gray-200 rounded-3xl p-6 card-shadow">
            <div class="flex items-center justify-between">
                <p class="text-xs uppercase tracking-[0.2em] text-gray-400 font-semibold">Total Absences</p>
                <span class="w-10 h-10 rounded-full bg-purple-50 text-purple-600 flex items-center justify-center">
                    <i class="fa-solid fa-calendar-xmark"></i>
                </span>
            </div>
            <h3 class="text-4xl font-black text-purple-600 mt-3">{{ str_pad($totalAbsent, 2, '0', STR_PAD_LEFT) }}</h3>
            <p class="text-xs text-gray-500 mt-2">Class total absences</p>
        </div>
    </section>

    <section class="bg-white border border-gray-200 rounded-3xl p-6 card-shadow space-y-5">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <p class="text-xs uppercase tracking-[0.35em] text-emerald-500 font-semibold">Shared Intervention Feed</p>
                <h2 class="text-xl font-black text-gray-900 mt-1">Focus areas for your class</h2>
                <p class="text-xs text-gray-500 mt-1">Plans applied here sync with principals and the discipline office.</p>
            </div>
            <span class="inline-flex items-center gap-2 text-[11px] font-bold px-3 py-1.5 rounded-full {{ $suggestionsSource === 'analytics' ? 'bg-indigo-50 text-indigo-700 border border-indigo-100' : ($suggestionsSource === 'manual' ? 'bg-emerald-50 text-emerald-700 border border-emerald-100' : 'bg-gray-100 text-gray-500 border border-gray-200') }}">
                <i class="fa-solid fa-wave-square"></i>
                {{ ucfirst($suggestionsSource) }} stream
            </span>
        </div>

        <div class="grid gap-5 lg:grid-cols-[minmax(0,1.5fr)_minmax(0,1fr)]">
            <div class="space-y-4">
                @if($suggestionsSource === 'none')
                    <div class="border border-dashed border-emerald-200 rounded-2xl p-5 text-center bg-emerald-50/40">
                        <p class="text-sm font-semibold text-gray-700">No flagged patterns for your section.</p>
                        <p class="text-xs text-gray-500 mt-1">When the discipline office shares a plan, you can act on it here.</p>
                    </div>
                @else
                    @foreach($suggestions as $suggestion)
                        @php
                            $persisted = isset($suggestion->id);
                            $gradeLevel = data_get($suggestion, 'grade_level');
                            $sectionValue = data_get($suggestion, 'section');
                            $scopeLabel = data_get($suggestion, 'scope_label');
                            $contextLabel = $scopeLabel ?: ($gradeLevel ? 'Grade ' . $gradeLevel : 'All Grades');
                            if (!$scopeLabel && $sectionValue) {
                                $contextLabel .= ' · Section ' . $sectionValue;
                            }
                            $incidentLabel = data_get($suggestion, 'incident_type', 'Behavioral Trend');
                            $eventCount = data_get($suggestion, 'incident_count', 0);
                            $assignmentOwner = data_get($suggestion, 'assigned_to');
                            $assignmentDue = data_get($suggestion, 'assignment_due_at');
                            $assignmentDueLabel = $assignmentDue ? \Illuminate\Support\Carbon::parse($assignmentDue)->format('M d, Y') : null;
                        @endphp
                        <article class="border border-emerald-100 rounded-2xl p-4 bg-gradient-to-br from-white to-emerald-50/20">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="text-[11px] uppercase tracking-[0.3em] text-emerald-600 font-semibold">{{ $contextLabel }}</p>
                                    <h3 class="text-lg font-black text-gray-900 mt-1">{{ $incidentLabel }}</h3>
                                    <p class="text-xs text-gray-500 font-semibold">{{ $eventCount }} incidents in the last cycle</p>
                                </div>
                                <span class="inline-flex items-center gap-1 text-[11px] font-bold px-2.5 py-1 rounded-full {{ $persisted ? 'bg-emerald-100 text-emerald-700 border border-emerald-200' : 'bg-slate-900 text-white' }}">
                                    <i class="fa-solid {{ $persisted ? 'fa-clipboard-check' : 'fa-wand-magic-sparkles' }}"></i>
                                    {{ $persisted ? 'Shared Plan' : 'Auto Insight' }}
                                </span>
                            </div>
                            <p class="text-sm text-gray-700 leading-relaxed mt-3">{{ $suggestion->suggestion }}</p>

                            @if($persisted)
                                <div class="mt-3 flex flex-wrap gap-4 text-[11px] font-semibold text-gray-500">
                                    <span class="inline-flex items-center gap-1"><i class="fa-solid fa-user-tag text-emerald-500 text-[10px]"></i> Owner: {{ $assignmentOwner ?? 'Discipline Office' }}</span>
                                    <span class="inline-flex items-center gap-1"><i class="fa-solid fa-calendar-day text-emerald-500 text-[10px]"></i> Due: {{ $assignmentDueLabel ?? 'Not set' }}</span>
                                </div>
                                <form method="POST" action="{{ route('interventions.decide', $suggestion) }}" class="mt-4 space-y-3">
                                    @csrf
                                    <input type="text" name="remarks" placeholder="Optional remark for principals" class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-2 text-xs font-semibold text-gray-600 focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                                    <div class="flex flex-wrap gap-2">
                                        <button type="submit" name="decision" value="apply" class="px-4 py-2 rounded-xl bg-emerald-600 text-white text-xs font-black inline-flex items-center gap-2 hover:bg-emerald-700 transition">
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
                                <p class="mt-4 text-xs text-gray-500 italic font-semibold">Analytics-only insight – contact the discipline chair to formalize the plan.</p>
                            @endif
                        </article>
                    @endforeach
                @endif
            </div>

            <div class="border border-emerald-100 rounded-2xl p-5 bg-white">
                <p class="text-xs uppercase tracking-[0.3em] text-gray-400 font-semibold">Plan Timeline</p>
                <h3 class="text-lg font-black text-gray-900 mt-1">Recent decisions</h3>
                <div class="mt-4 space-y-4">
                    @forelse($recentPlans as $plan)
                        @php
                            $palette = [
                                'implemented' => 'bg-emerald-50 text-emerald-700 border border-emerald-200',
                                'approved' => 'bg-blue-50 text-blue-700 border border-blue-200',
                                'rejected' => 'bg-rose-50 text-rose-700 border border-rose-200',
                            ];
                            $badgeClass = $palette[$plan->status] ?? 'bg-gray-100 text-gray-600 border border-gray-200';
                        @endphp
                        <div class="border-l-2 border-emerald-100 pl-4">
                            <div class="rounded-2xl border border-gray-100 bg-gray-50 p-4">
                                <div class="flex items-center justify-between gap-3">
                                    <div>
                                        <p class="text-sm font-black text-gray-900">{{ $plan->incident_type }}</p>
                                        <p class="text-xs text-gray-500">{{ $plan->grade_level ? 'Grade ' . $plan->grade_level : 'All Grades' }}@if($plan->section) · Section {{ $plan->section }} @endif</p>
                                    </div>
                                    <span class="text-[11px] font-bold px-2.5 py-1 rounded-full {{ $badgeClass }}">{{ ucfirst($plan->status) }}</span>
                                </div>
                                <p class="text-xs text-gray-500 mt-2">{{ optional($plan->decisionMaker)->name ?? 'Leadership' }} &middot; {{ optional($plan->decided_at)->diffForHumans() ?? 'N/A' }}</p>
                                @if($plan->assigned_to || $plan->assignment_due_at)
                                    <p class="text-[11px] text-gray-500 mt-1">Owner: {{ $plan->assigned_to ?? 'Unassigned' }} @if($plan->assignment_due_at) • Due {{ $plan->assignment_due_at->format('M d, Y') }} @endif</p>
                                @endif
                                @if($plan->decision_remarks)
                                    <p class="text-xs text-gray-600 italic mt-2">“{{ $plan->decision_remarks }}”</p>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="p-4 border border-dashed border-gray-200 rounded-2xl text-center">
                            <p class="text-sm font-semibold text-gray-700">No plan decisions yet.</p>
                            <p class="text-xs text-gray-500 mt-1">Once a plan is marked as implemented, it will appear here.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </section>

    <section class="bg-white border border-gray-200 rounded-3xl overflow-hidden card-shadow">
        <div class="px-7 py-6 border-b border-gray-100 flex flex-wrap gap-3 justify-between items-center">
            <div>
                <p class="text-xs uppercase tracking-[0.2em] text-gray-400 font-semibold">Advisees List</p>
                <h2 class="text-2xl font-black text-gray-900 mt-1">Student Records Summary</h2>
                <p class="text-xs text-gray-500 mt-1">Click any student to view detailed incident records.</p>
            </div>
            <div class="flex gap-2">
                <button class="px-4 py-2 text-xs font-semibold border border-gray-200 rounded-full text-gray-600 hover:bg-gray-50">
                    <i class="fa-solid fa-magnifying-glass mr-2"></i> Search Student
                </button>
                <button class="px-4 py-2 text-xs font-semibold border border-gray-200 rounded-full text-gray-600 hover:bg-gray-50">
                    <i class="fa-solid fa-filter mr-2"></i> Filters
                </button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-[10px] uppercase tracking-[0.2em] text-gray-500">
                    <tr>
                        <th class="px-6 py-4 text-left">Student ID</th>
                        <th class="px-6 py-4 text-left">Full Name</th>
                        <th class="px-6 py-4 text-left">Incidents</th>
                        <th class="px-6 py-4 text-left">Tardy</th>
                        <th class="px-6 py-4 text-left">Absent</th>
                        <th class="px-6 py-4 text-left">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($advisees as $student)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-gray-600 font-medium">{{ $student->student_id }}</td>
                        <td class="px-6 py-4 text-gray-900 font-semibold">{{ $student->first_name }} {{ $student->middle_name }} {{ $student->last_name }}</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex px-3 py-1 rounded-full text-xs font-bold {{ $student->incidents_count > 0 ? 'bg-rose-50 text-rose-700 border border-rose-200' : 'bg-gray-100 text-gray-600' }}">
                                {{ $student->incidents_count }} {{ $student->incidents_count === 1 ? 'incident' : 'incidents' }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex px-3 py-1 rounded-full text-xs font-bold {{ $student->tardy_count > 0 ? 'bg-amber-50 text-amber-700 border border-amber-200' : 'bg-gray-100 text-gray-600' }}">
                                {{ $student->tardy_count }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex px-3 py-1 rounded-full text-xs font-bold {{ $student->absent_count > 0 ? 'bg-purple-50 text-purple-700 border border-purple-200' : 'bg-gray-100 text-gray-600' }}">
                                {{ $student->absent_count }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <a href="{{ route('adviser.students.show', $student) }}" class="inline-flex items-center gap-2 px-4 py-2 text-xs font-semibold rounded-lg bg-green-700 text-white hover:bg-green-800 transition-colors">
                                <i class="fa-solid fa-eye"></i> View Details
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                            <i class="fa-solid fa-inbox text-4xl mb-3 opacity-30"></i>
                            <p class="font-semibold">No advisees found</p>
                            <p class="text-xs mt-1">Register students to see them listed here.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>

@endsection
