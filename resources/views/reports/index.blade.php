@extends('layouts.app')

@section('content')
<header class="bg-gradient-to-r from-green-800 to-green-700 border-b border-green-900 px-8 py-6 flex justify-between items-center sticky top-0 z-40 shadow-lg">
    <div>
        <h2 class="text-2xl font-black text-white">Reports Center</h2>
        <p class="text-sm text-green-100 font-medium mt-1">Generate printable reports and summaries</p>
    </div>
    <a href="{{ route('dashboard') }}" class="bg-white hover:bg-gray-50 text-green-800 px-5 py-2 rounded-lg text-sm font-bold transition-all">
        <i class="fa-solid fa-arrow-left mr-2"></i> Dashboard
    </a>
</header>
@php
    $students = \App\Models\Student::where('status', 'active')->orderBy('last_name')->get();
    $now = now();
    $quickMonths = [];
    for ($i = 0; $i < 3; $i++) {
        $target = $now->copy()->subMonths($i);
        $quickMonths[] = [
            'label' => $target->format('F Y'),
            'month' => $target->month,
            'year' => $target->year,
        ];
    }

    $quickQuarters = [];
    $refQuarter = ceil($now->month / 3);
    $refYear = $now->year;
    for ($i = 0; $i < 2; $i++) {
        $quarter = $refQuarter - $i;
        $year = $refYear;
        if ($quarter <= 0) {
            $quarter += 4;
            $year -= 1;
        }
        $quickQuarters[] = [
            'label' => 'Q' . $quarter . ' ' . $year,
            'quarter' => $quarter,
            'year' => $year,
        ];
    }
@endphp

<div class="p-8 max-w-6xl mx-auto space-y-8">
    <section class="bg-white border border-gray-200 rounded-3xl shadow-sm p-6 flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
        <div>
            <p class="text-xs font-bold text-green-600 uppercase tracking-[0.35em]">Report Hub</p>
            <h3 class="text-3xl font-black text-gray-900 mt-1">One place for every printable</h3>
            <p class="text-sm text-gray-500 mt-2 max-w-2xl">Run board-ready summaries or deep student dossiers without hopping between modules. Pick a template, adjust the period, and generate a PDF in seconds.</p>
        </div>
        <div class="grid grid-cols-2 gap-3 w-full lg:w-auto">
            <a href="{{ route('reports.monthly', ['month' => $currentMonth, 'year' => $currentYear]) }}" class="px-4 py-3 rounded-2xl border border-blue-100 bg-blue-50 text-blue-700 text-xs font-bold uppercase tracking-widest hover:bg-blue-100 transition text-center">This Month</a>
            <a href="{{ route('reports.quarterly', ['quarter' => $currentQuarter, 'year' => $currentYear]) }}" class="px-4 py-3 rounded-2xl border border-purple-100 bg-purple-50 text-purple-700 text-xs font-bold uppercase tracking-widest hover:bg-purple-100 transition text-center">Current Quarter</a>
            <a href="#student-record" class="px-4 py-3 rounded-2xl border border-emerald-100 bg-emerald-50 text-emerald-700 text-xs font-bold uppercase tracking-widest hover:bg-emerald-100 transition text-center">Student Record</a>
            <a href="{{ route('dashboard') }}" class="px-4 py-3 rounded-2xl border border-gray-200 text-gray-600 text-xs font-bold uppercase tracking-widest text-center hover:bg-gray-50 transition">Back to Dashboard</a>
        </div>
    </section>

    <div class="grid gap-8 lg:grid-cols-[2fr_1fr]">
        <div class="space-y-8">
            <section class="bg-white border border-gray-200 rounded-3xl shadow-sm p-6">
                <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
                    <div>
                        <p class="text-xs font-bold text-gray-500 uppercase tracking-[0.3em]">Scheduled Reports</p>
                        <h4 class="text-xl font-black text-gray-900">Monthly & Quarterly generators</h4>
                    </div>
                    <p class="text-xs text-gray-500">Run time &lt; 3 seconds on average</p>
                </div>
                <div class="grid gap-6 md:grid-cols-2">
                    <form action="{{ route('reports.monthly') }}" method="GET" class="rounded-2xl border border-blue-100 p-5 bg-blue-50/30">
                        <div class="flex items-center gap-3 mb-4">
                            <span class="w-11 h-11 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center">
                                <i class="fa-solid fa-calendar-days"></i>
                            </span>
                            <div>
                                <p class="text-xs font-bold text-blue-500 uppercase tracking-[0.3em]">Monthly Summary</p>
                                <p class="text-sm text-gray-600">Incident overview by month</p>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <label class="text-xs font-semibold text-gray-500 uppercase">Month
                                <select name="month" class="mt-1 w-full rounded-xl border-gray-200 text-sm">
                                    @for($m = 1; $m <= 12; $m++)
                                        <option value="{{ $m }}" {{ $currentMonth == $m ? 'selected' : '' }}>
                                            {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                                        </option>
                                    @endfor
                                </select>
                            </label>
                            <label class="text-xs font-semibold text-gray-500 uppercase">Year
                                <select name="year" class="mt-1 w-full rounded-xl border-gray-200 text-sm">
                                    @for($y = $currentYear; $y >= $currentYear - 5; $y--)
                                        <option value="{{ $y }}">{{ $y }}</option>
                                    @endfor
                                </select>
                            </label>
                        </div>
                        <button type="submit" class="w-full mt-5 bg-blue-600 hover:bg-blue-700 text-white py-2.5 rounded-xl text-sm font-black tracking-wide flex items-center justify-center gap-2">
                            <i class="fa-solid fa-file-lines"></i>
                            Generate PDF
                        </button>
                    </form>

                    <form action="{{ route('reports.quarterly') }}" method="GET" class="rounded-2xl border border-purple-100 p-5 bg-purple-50/30">
                        <div class="flex items-center gap-3 mb-4">
                            <span class="w-11 h-11 rounded-xl bg-purple-100 text-purple-700 flex items-center justify-center">
                                <i class="fa-solid fa-chart-pie"></i>
                            </span>
                            <div>
                                <p class="text-xs font-bold text-purple-500 uppercase tracking-[0.3em]">Quarterly Report</p>
                                <p class="text-sm text-gray-600">Analytics by quarter</p>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <label class="text-xs font-semibold text-gray-500 uppercase">Quarter
                                <select name="quarter" class="mt-1 w-full rounded-xl border-gray-200 text-sm">
                                    <option value="1" {{ $currentQuarter == 1 ? 'selected' : '' }}>Q1 (Jan-Mar)</option>
                                    <option value="2" {{ $currentQuarter == 2 ? 'selected' : '' }}>Q2 (Apr-Jun)</option>
                                    <option value="3" {{ $currentQuarter == 3 ? 'selected' : '' }}>Q3 (Jul-Sep)</option>
                                    <option value="4" {{ $currentQuarter == 4 ? 'selected' : '' }}>Q4 (Oct-Dec)</option>
                                </select>
                            </label>
                            <label class="text-xs font-semibold text-gray-500 uppercase">Year
                                <select name="year" class="mt-1 w-full rounded-xl border-gray-200 text-sm">
                                    @for($y = $currentYear; $y >= $currentYear - 5; $y--)
                                        <option value="{{ $y }}">{{ $y }}</option>
                                    @endfor
                                </select>
                            </label>
                        </div>
                        <button type="submit" class="w-full mt-5 bg-purple-600 hover:bg-purple-700 text-white py-2.5 rounded-xl text-sm font-black tracking-wide flex items-center justify-center gap-2">
                            <i class="fa-solid fa-chart-line"></i>
                            Generate Insights Pack
                        </button>
                    </form>
                </div>
            </section>

            <section id="student-record" class="bg-white border border-gray-200 rounded-3xl shadow-sm p-6" x-data="{ studentId: '' }">
                <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
                    <div>
                        <p class="text-xs font-bold text-gray-500 uppercase tracking-[0.3em]">Individual Profiles</p>
                        <h4 class="text-xl font-black text-gray-900">Student record & attendance pulls</h4>
                    </div>
                    <span class="text-xs text-gray-500">{{ $students->count() }} active students</span>
                </div>
                <div class="space-y-4">
                    <label class="text-xs font-semibold text-gray-500 uppercase block">Search student
                        <select name="student" x-model="studentId" class="mt-1 w-full rounded-2xl border-gray-200 text-sm">
                            <option value="">Select a student...</option>
                            @foreach($students as $student)
                                <option value="{{ $student->id }}">{{ $student->last_name }}, {{ $student->first_name }} ({{ $student->student_id }})</option>
                            @endforeach
                        </select>
                    </label>
                    <div class="grid gap-3 md:grid-cols-2">
                        <button type="submit" form="student-record-form" :disabled="!studentId" class="py-3 rounded-2xl text-sm font-black tracking-wide flex items-center justify-center gap-2 bg-emerald-600 text-white disabled:bg-gray-200 disabled:text-gray-500">
                            <i class="fa-solid fa-user-graduate"></i>
                            Discipline Record
                        </button>
                        <button type="submit" form="student-attendance-form" :disabled="!studentId" class="py-3 rounded-2xl text-sm font-black tracking-wide flex items-center justify-center gap-2 border border-emerald-200 text-emerald-700 hover:bg-emerald-50 disabled:border-gray-200 disabled:text-gray-500">
                            <i class="fa-solid fa-calendar-check"></i>
                            Attendance Snapshot
                        </button>
                    </div>
                </div>

                <form id="student-record-form" action="{{ route('reports.student') }}" method="GET" class="hidden">
                    <input type="hidden" name="student" :value="studentId">
                </form>
                <form id="student-attendance-form" action="{{ route('reports.attendance') }}" method="GET" class="hidden">
                    <input type="hidden" name="student" :value="studentId">
                </form>
            </section>
        </div>

        <div class="space-y-6">
            <section class="bg-white border border-gray-200 rounded-3xl shadow-sm p-6">
                <p class="text-xs font-bold text-gray-500 uppercase tracking-[0.3em]">One-click exports</p>
                <h4 class="text-lg font-black text-gray-900 mt-2">Recently requested periods</h4>
                <div class="mt-4 space-y-3">
                    @foreach($quickMonths as $slot)
                        <a href="{{ route('reports.monthly', ['month' => $slot['month'], 'year' => $slot['year']]) }}" class="flex items-center justify-between px-4 py-3 rounded-2xl border border-blue-100 bg-blue-50/60 text-sm font-semibold text-blue-800 hover:bg-blue-100 transition">
                            <span>{{ $slot['label'] }}</span>
                            <i class="fa-solid fa-arrow-up-right-from-square text-xs"></i>
                        </a>
                    @endforeach
                    @foreach($quickQuarters as $slot)
                        <a href="{{ route('reports.quarterly', ['quarter' => $slot['quarter'], 'year' => $slot['year']]) }}" class="flex items-center justify-between px-4 py-3 rounded-2xl border border-purple-100 bg-purple-50/60 text-sm font-semibold text-purple-800 hover:bg-purple-100 transition">
                            <span>{{ $slot['label'] }}</span>
                            <i class="fa-solid fa-arrow-up-right-from-square text-xs"></i>
                        </a>
                    @endforeach
                </div>
            </section>

            <section class="bg-amber-50 rounded-3xl border border-amber-200 p-6">
                <div class="flex items-start gap-4">
                    <div class="h-10 w-10 rounded-full bg-amber-100 flex items-center justify-center flex-shrink-0">
                        <i class="fa-solid fa-lightbulb text-amber-600"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-amber-900 mb-2">Printing tips</h4>
                        <ul class="text-sm text-amber-700 space-y-1">
                            <li><strong>Print / Save as PDF</strong> button is built into every report template.</li>
                            <li>Use your browser's Print dialog &rightarrow; "Save as PDF" for digital copies.</li>
                            <li>Stick to <strong>A4 paper</strong>, portrait orientation for clean spacing.</li>
                            <li>Signatures and letterhead are pre-loaded—just add your notes before printing.</li>
                        </ul>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>
@endsection
