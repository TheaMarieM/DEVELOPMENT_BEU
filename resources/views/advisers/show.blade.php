@extends('layouts.app')

@section('content')
@php
    $assignedStudents = $adviser->advisedStudents;
    $primarySection = $assignedStudents->first();
    $incidentCount = \DB::table('incident_students')
        ->whereIn('student_id', $assignedStudents->pluck('id'))
        ->count();
    $contactCompleted = $adviser->phone ? 'Complete' : 'Missing';
@endphp

<div class="p-8 max-w-7xl mx-auto space-y-8">
    <div class="flex items-center gap-2 text-sm text-gray-500">
        <a href="{{ route('advisers.index') }}" class="hover:text-emerald-600 transition-colors">Advisers</a>
        <i class="fa-solid fa-chevron-right text-xs"></i>
        <span class="text-gray-800 font-medium">Faculty Profile</span>
    </div>

    <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm flex flex-col lg:flex-row lg:items-center justify-between gap-6">
        <div>
            <p class="text-[11px] font-bold tracking-[0.35em] uppercase text-emerald-600">Advisory Lead</p>
            <div class="flex items-center gap-4 mt-4">
                <div class="w-16 h-16 rounded-2xl bg-emerald-50 border border-emerald-100 text-2xl font-bold text-emerald-700 flex items-center justify-center">
                    {{ substr($adviser->name, 0, 1) }}
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ $adviser->name }}</h1>
                    <div class="text-sm text-gray-600 mt-1 space-y-1">
                        <p class="flex items-center gap-2">
                            <i class="fa-regular fa-envelope text-gray-400"></i>
                            {{ $adviser->email }}
                        </p>
                        @if($adviser->phone)
                            <p class="flex items-center gap-2">
                                <i class="fa-solid fa-phone text-gray-400"></i>
                                {{ $adviser->phone }}
                            </p>
                        @endif
                    </div>
                </div>
            </div>
            <div class="flex flex-wrap gap-3 mt-5 text-[11px] font-semibold uppercase tracking-wide text-gray-600">
                <span class="px-3 py-1 rounded-full border border-gray-200 bg-gray-50">{{ strtoupper($adviser->status) }}</span>
                @if($primarySection)
                    <span class="px-3 py-1 rounded-full border border-gray-200 bg-gray-50">
                        Grade {{ $primarySection->grade_level }} – {{ $primarySection->section }}
                    </span>
                @endif
                <span class="px-3 py-1 rounded-full border border-gray-200 bg-gray-50">{{ $assignedStudents->count() }} Students</span>
            </div>
        </div>
        <div class="flex flex-wrap gap-3">
            <a href="mailto:{{ $adviser->email }}" class="px-4 py-2 rounded-lg border border-gray-200 text-sm font-semibold text-gray-700 flex items-center gap-2 hover:bg-gray-50 transition">
                <i class="fa-solid fa-paper-plane text-emerald-500"></i> Quick Email
            </a>
            <a href="{{ route('advisers.edit', $adviser) }}" class="px-5 py-2.5 rounded-lg bg-emerald-600 text-white text-sm font-semibold flex items-center gap-2 shadow-sm hover:bg-emerald-700 transition">
                <i class="fa-solid fa-pen-to-square text-white/90"></i> Edit Profile
            </a>
        </div>
    </div>

    <div class="grid gap-6 md:grid-cols-3">
        <div class="bg-white border border-emerald-50 rounded-2xl shadow-sm p-6">
            <p class="text-[11px] font-bold text-gray-400 uppercase tracking-[0.25em]">Students Assigned</p>
            <div class="flex items-baseline gap-2 mt-3">
                <span class="text-4xl font-bold text-gray-900">{{ $assignedStudents->count() }}</span>
                <span class="text-xs text-emerald-600 font-semibold">active roster</span>
            </div>
            <p class="text-xs text-gray-500 mt-2">Includes all students mapped to this advisory class.</p>
        </div>
        <div class="bg-white border border-emerald-50 rounded-2xl shadow-sm p-6">
            <p class="text-[11px] font-bold text-gray-400 uppercase tracking-[0.25em]">Class Incidents</p>
            <div class="flex items-baseline gap-2 mt-3">
                <span class="text-4xl font-bold text-amber-600">{{ $incidentCount }}</span>
                <span class="text-xs text-amber-600 font-semibold">records this year</span>
            </div>
            <p class="text-xs text-gray-500 mt-2">Drawn from incident logs tied to the current roster.</p>
        </div>
        <div class="bg-white border border-emerald-50 rounded-2xl shadow-sm p-6">
            <p class="text-[11px] font-bold text-gray-400 uppercase tracking-[0.25em]">Contact Completeness</p>
            <div class="flex items-center gap-3 mt-3">
                <span class="text-2xl font-bold {{ $adviser->phone ? 'text-emerald-600' : 'text-gray-400' }}">{{ $adviser->phone ? '100%' : '68%' }}</span>
                <span class="text-xs font-semibold uppercase tracking-wider {{ $adviser->phone ? 'text-emerald-700' : 'text-gray-500' }}">{{ $contactCompleted }}</span>
            </div>
            <div class="mt-3 h-2 rounded-full bg-gray-100 overflow-hidden">
                <div class="h-full {{ $adviser->phone ? 'bg-emerald-500 w-full' : 'bg-amber-400 w-2/3' }}"></div>
            </div>
            <p class="text-xs text-gray-500 mt-2">Phone details are required for SMS alerts.</p>
        </div>
    </div>

    <div class="grid gap-8 lg:grid-cols-3">
        <div class="space-y-6">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <h2 class="text-sm font-bold text-gray-700 uppercase tracking-wide flex items-center gap-2">
                    <i class="fa-solid fa-id-card-clip text-emerald-500"></i>
                    Faculty dossier
                </h2>
                <dl class="mt-5 space-y-4">
                    <div>
                        <dt class="text-xs font-bold text-gray-400 uppercase tracking-wide">Employee ID</dt>
                        <dd class="font-mono text-sm text-gray-900 mt-1">{{ $adviser->employee_id }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-bold text-gray-400 uppercase tracking-wide">Email</dt>
                        <dd class="text-sm text-gray-800 mt-1">
                            <a href="mailto:{{ $adviser->email }}" class="hover:text-emerald-600 hover:underline flex items-center gap-1">
                                <i class="fa-regular fa-envelope text-gray-400"></i>{{ $adviser->email }}
                            </a>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs font-bold text-gray-400 uppercase tracking-wide">Phone</dt>
                        <dd class="text-sm text-gray-800 mt-1">
                            @if($adviser->phone)
                                <span class="flex items-center gap-1"><i class="fa-solid fa-phone text-gray-400"></i>{{ $adviser->phone }}</span>
                            @else
                                <span class="text-gray-400 italic">Not provided</span>
                            @endif
                        </dd>
                    </div>
                </dl>
            </div>

            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <h2 class="text-sm font-bold text-gray-700 uppercase tracking-wide flex items-center gap-2">
                    <i class="fa-solid fa-chalkboard-user text-indigo-500"></i>
                    Advisory coverage
                </h2>
                @if($primarySection)
                    <div class="mt-5 space-y-2">
                        <p class="text-lg font-bold text-gray-900">Grade {{ $primarySection->grade_level }} &middot; {{ $primarySection->section }}</p>
                        <p class="text-xs text-gray-500">{{ $assignedStudents->count() }} students currently assigned</p>
                        <div class="mt-4 bg-emerald-50 border border-emerald-100 rounded-xl p-4">
                            <p class="text-xs font-semibold text-emerald-700 uppercase tracking-wide">Snapshot</p>
                            <p class="text-sm text-emerald-900 mt-2">Roster synced {{ now()->format('M d, Y') }}.</p>
                        </div>
                    </div>
                @else
                    <p class="mt-5 text-sm text-gray-500 italic">No class assignment has been linked to this adviser.</p>
                @endif
            </div>
        </div>

        <div class="lg:col-span-2 bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                <div>
                    <h2 class="font-bold text-gray-800 text-sm uppercase tracking-wide flex items-center gap-2">
                        <i class="fa-solid fa-list-check text-emerald-500"></i>
                        Class roster
                    </h2>
                    <p class="text-xs text-gray-500 mt-1">Detailed list of students under this advisory section.</p>
                </div>
                <button class="px-4 py-2 rounded-xl border border-gray-200 text-xs font-bold text-gray-600 hover:bg-gray-50 flex items-center gap-2">
                    <i class="fa-solid fa-download"></i>
                    Export roster
                </button>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="px-6 py-3 text-[10px] font-bold uppercase text-gray-400 tracking-wider">Student ID</th>
                            <th class="px-6 py-3 text-[10px] font-bold uppercase text-gray-400 tracking-wider">Student Name</th>
                            <th class="px-6 py-3 text-[10px] font-bold uppercase text-gray-400 tracking-wider">Gender</th>
                            <th class="px-6 py-3 text-[10px] font-bold uppercase text-gray-400 tracking-wider text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-sm">
                        @forelse($assignedStudents as $advisedStudent)
                        <tr class="hover:bg-emerald-50/30 transition-colors">
                            <td class="px-6 py-4 font-mono text-xs text-gray-600">{{ $advisedStudent->student_id }}</td>
                            <td class="px-6 py-4 font-semibold text-gray-900">{{ $advisedStudent->last_name }}, {{ $advisedStudent->first_name }}</td>
                            <td class="px-6 py-4 text-gray-500 text-xs capitalize">{{ $advisedStudent->gender }}</td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('students.show', $advisedStudent) }}" class="inline-flex items-center gap-1 text-xs font-bold text-emerald-700 hover:text-emerald-900">
                                    View Profile
                                    <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-10 text-center text-gray-400 text-sm italic">
                                No students currently assigned to this adviser.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
