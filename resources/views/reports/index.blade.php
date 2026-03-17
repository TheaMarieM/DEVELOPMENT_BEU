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

<div class="p-8 max-w-6xl mx-auto">
    <!-- Quick Reports -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        <!-- Monthly Summary -->
        <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm hover:shadow-lg transition-shadow">
            <div class="flex items-center gap-4 mb-4">
                <div class="h-12 w-12 rounded-xl bg-blue-100 flex items-center justify-center">
                    <i class="fa-solid fa-calendar-days text-blue-600 text-xl"></i>
                </div>
                <div>
                    <h3 class="font-bold text-gray-900">Monthly Summary</h3>
                    <p class="text-xs text-gray-500">Incident overview by month</p>
                </div>
            </div>
            <form action="{{ route('reports.monthly') }}" method="GET" class="space-y-3">
                <div class="grid grid-cols-2 gap-2">
                    <select name="month" class="rounded-lg border-gray-200 text-sm">
                        @for($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" {{ $currentMonth == $m ? 'selected' : '' }}>
                                {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                            </option>
                        @endfor
                    </select>
                    <select name="year" class="rounded-lg border-gray-200 text-sm">
                        @for($y = $currentYear; $y >= $currentYear - 5; $y--)
                            <option value="{{ $y }}">{{ $y }}</option>
                        @endfor
                    </select>
                </div>
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg text-sm font-semibold transition-colors">
                    <i class="fa-solid fa-file-alt mr-2"></i> Generate Report
                </button>
            </form>
        </div>

        <!-- Quarterly Report -->
        <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm hover:shadow-lg transition-shadow">
            <div class="flex items-center gap-4 mb-4">
                <div class="h-12 w-12 rounded-xl bg-purple-100 flex items-center justify-center">
                    <i class="fa-solid fa-chart-bar text-purple-600 text-xl"></i>
                </div>
                <div>
                    <h3 class="font-bold text-gray-900">Quarterly Report</h3>
                    <p class="text-xs text-gray-500">Analytics by quarter</p>
                </div>
            </div>
            <form action="{{ route('reports.quarterly') }}" method="GET" class="space-y-3">
                <div class="grid grid-cols-2 gap-2">
                    <select name="quarter" class="rounded-lg border-gray-200 text-sm">
                        <option value="1" {{ $currentQuarter == 1 ? 'selected' : '' }}>Q1 (Jan-Mar)</option>
                        <option value="2" {{ $currentQuarter == 2 ? 'selected' : '' }}>Q2 (Apr-Jun)</option>
                        <option value="3" {{ $currentQuarter == 3 ? 'selected' : '' }}>Q3 (Jul-Sep)</option>
                        <option value="4" {{ $currentQuarter == 4 ? 'selected' : '' }}>Q4 (Oct-Dec)</option>
                    </select>
                    <select name="year" class="rounded-lg border-gray-200 text-sm">
                        @for($y = $currentYear; $y >= $currentYear - 5; $y--)
                            <option value="{{ $y }}">{{ $y }}</option>
                        @endfor
                    </select>
                </div>
                <button type="submit" class="w-full bg-purple-600 hover:bg-purple-700 text-white py-2 rounded-lg text-sm font-semibold transition-colors">
                    <i class="fa-solid fa-chart-pie mr-2"></i> Generate Report
                </button>
            </form>
        </div>

        <!-- Student Record -->
        <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm hover:shadow-lg transition-shadow">
            <div class="flex items-center gap-4 mb-4">
                <div class="h-12 w-12 rounded-xl bg-green-100 flex items-center justify-center">
                    <i class="fa-solid fa-user-graduate text-green-600 text-xl"></i>
                </div>
                <div>
                    <h3 class="font-bold text-gray-900">Student Record</h3>
                    <p class="text-xs text-gray-500">Individual discipline history</p>
                </div>
            </div>
            <form action="{{ route('reports.student') }}" method="GET" class="space-y-3" x-data="{ studentId: '' }">
                <select name="student" x-model="studentId" class="w-full rounded-lg border-gray-200 text-sm">
                    <option value="">Select a student...</option>
                    @foreach(\App\Models\Student::where('status', 'active')->orderBy('last_name')->get() as $student)
                        <option value="{{ $student->id }}">{{ $student->last_name }}, {{ $student->first_name }} ({{ $student->student_id }})</option>
                    @endforeach
                </select>
                <button type="submit" :disabled="!studentId" class="w-full bg-green-600 hover:bg-green-700 disabled:bg-gray-300 text-white py-2 rounded-lg text-sm font-semibold transition-colors">
                    <i class="fa-solid fa-file-lines mr-2"></i> Generate Record
                </button>
            </form>
        </div>
    </div>

    <!-- Report Tips -->
    <div class="bg-amber-50 rounded-2xl border border-amber-200 p-6">
        <div class="flex items-start gap-4">
            <div class="h-10 w-10 rounded-full bg-amber-100 flex items-center justify-center flex-shrink-0">
                <i class="fa-solid fa-lightbulb text-amber-600"></i>
            </div>
            <div>
                <h4 class="font-bold text-amber-800 mb-2">Printing Tips</h4>
                <ul class="text-sm text-amber-700 space-y-1">
                    <li>• Click the <strong>"Print / Save as PDF"</strong> button on any report page</li>
                    <li>• Use your browser's print dialog to save as PDF or send to printer</li>
                    <li>• For best results, use "A4" paper size and "Portrait" orientation</li>
                    <li>• Reports include official school letterhead and signature lines</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
