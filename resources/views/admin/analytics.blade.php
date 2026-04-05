@extends('layouts.app')

@section('content')
<header class="bg-white border-b border-gray-200 px-8 py-6 flex flex-wrap items-center justify-between gap-4 sticky top-0 z-30">
    <div>
        <p class="text-xs font-bold text-emerald-600 uppercase tracking-[0.3em]">Analytics</p>
        <h2 class="text-3xl font-black text-gray-900">Discipline Intelligence Center</h2>
        <p class="text-sm text-gray-500 font-medium mt-1">Live signals across incidents, attendance, and approvals.</p>
    </div>
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.analytics') }}" class="px-4 py-2 rounded-lg border border-gray-200 text-sm font-semibold text-gray-700 hover:bg-gray-50 transition flex items-center gap-2">
            <i class="fa-solid fa-rotate-right text-xs"></i> Refresh Data
        </a>
        <a href="{{ route('dashboard') }}" class="px-4 py-2 rounded-lg bg-emerald-600 text-white text-sm font-bold hover:bg-emerald-700 transition flex items-center gap-2">
            <i class="fa-solid fa-arrow-left text-xs"></i> Back to Dashboard
        </a>
    </div>
</header>

<div class="p-8 max-w-7xl mx-auto space-y-10">
    @php $insightCards = $insights ?? []; @endphp
    @if(!empty($insightCards))
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 mb-10">
            @foreach($insightCards as $insight)
                <div class="rounded-2xl p-6 border border-emerald-100 shadow-md bg-gradient-to-br from-white to-emerald-50/70">
                    <p class="text-xs font-semibold text-emerald-600 uppercase tracking-widest">{{ $insight['title'] }}</p>
                    <p class="text-2xl font-black mt-2 text-gray-900">{{ $insight['value'] }}</p>
                    @if(!empty($insight['context']))
                        <p class="text-sm text-gray-600 mt-3">{{ $insight['context'] }}</p>
                    @endif
                </div>
            @endforeach
        </div>
    @endif

    <!-- Overview Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Incidents This Month -->
        <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">This Month</span>
                <span class="h-10 w-10 rounded-xl bg-blue-50 flex items-center justify-center">
                    <i class="fa-solid fa-clipboard-list text-blue-600"></i>
                </span>
            </div>
            <h3 class="text-3xl font-black text-gray-900">{{ $data['overview']['total_incidents_this_month'] ?? 0 }}</h3>
            <div class="flex items-center mt-2">
                @php $change = $data['overview']['monthly_change_percent'] ?? 0; @endphp
                @if($change >= 0)
                    <span class="text-sm font-semibold flex items-center text-red-600">
                        <i class="fa-solid fa-arrow-up mr-1 text-xs"></i> {{ abs($change) }}%
                    </span>
                @else
                    <span class="text-sm font-semibold flex items-center text-green-600">
                        <i class="fa-solid fa-arrow-down mr-1 text-xs"></i> {{ abs($change) }}%
                    </span>
                @endif
                <span class="text-xs text-gray-400 ml-2">vs last month</span>
            </div>
        </div>

        <!-- Pending Approval -->
        <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Pending Approval</span>
                <span class="h-10 w-10 rounded-xl bg-amber-50 flex items-center justify-center">
                    <i class="fa-solid fa-clock text-amber-600"></i>
                </span>
            </div>
            <h3 class="text-3xl font-black text-amber-600">{{ $data['overview']['pending_approval'] ?? 0 }}</h3>
            <p class="text-xs text-gray-400 mt-2">Awaiting principal review</p>
        </div>

        <!-- At-Risk Students -->
        <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">At-Risk Students</span>
                <span class="h-10 w-10 rounded-xl bg-red-50 flex items-center justify-center">
                    <i class="fa-solid fa-exclamation-triangle text-red-600"></i>
                </span>
            </div>
            <h3 class="text-3xl font-black text-red-600">{{ $data['overview']['at_risk_students'] ?? 0 }}</h3>
            <p class="text-xs text-gray-400 mt-2">Requires intervention</p>
        </div>

        <!-- Completion Rate -->
        <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Completion Rate</span>
                <span class="h-10 w-10 rounded-xl bg-green-50 flex items-center justify-center">
                    <i class="fa-solid fa-check-circle text-green-600"></i>
                </span>
            </div>
            <h3 class="text-3xl font-black text-green-600">{{ $data['performance']['completion_rate'] ?? 0 }}%</h3>
            <p class="text-xs text-gray-400 mt-2">Cases resolved this month</p>
        </div>
    </div>

    @if(!empty($interventionInsights))
        <section class="rounded-3xl border border-emerald-100 bg-gradient-to-br from-white via-emerald-50/60 to-white shadow-sm">
            <div class="px-6 py-5 border-b border-emerald-100 flex flex-wrap items-center justify-between gap-3">
                <div>
                    <p class="text-xs font-bold text-emerald-600 uppercase tracking-[0.35em]">Intervention Layer</p>
                    <h3 class="text-2xl font-black text-gray-900">Insights from the last 45 days</h3>
                    <p class="text-sm text-gray-500">Auto-curated hotspots blending incident and attendance spikes.</p>
                </div>
                <div class="flex items-center gap-3">
                    <span class="inline-flex items-center gap-1 text-xs font-semibold text-emerald-700 bg-white/80 border border-emerald-100 px-3 py-1 rounded-full">
                        <i class="fa-solid fa-wave-square text-[10px]"></i> Live Analytics
                    </span>
                    <a href="{{ route('dashboard') }}#intervention" class="text-xs font-bold text-emerald-700 hover:text-emerald-900">Sync with Dashboard →</a>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5 p-6">
                @foreach($interventionInsights as $insight)
                    <article class="p-5 rounded-2xl bg-white border border-emerald-100 shadow-sm hover:border-emerald-200 transition">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <p class="text-[11px] uppercase tracking-[0.3em] text-emerald-600 font-bold">{{ $insight['grade_level'] ?? 'All Levels' }}</p>
                                <p class="text-3xl font-black text-gray-900 mt-2">{{ $insight['incident_count'] ?? 0 }}</p>
                                <p class="text-xs text-gray-500 font-semibold">Flagged events</p>
                            </div>
                            <span class="px-3 py-1 rounded-full text-[11px] font-bold text-emerald-700 bg-emerald-50 border border-emerald-100">Hotspot</span>
                        </div>
                        <p class="text-sm text-gray-700 leading-relaxed">{{ $insight['suggestion'] }}</p>
                        <div class="mt-4 flex flex-wrap gap-2 text-[11px] text-gray-500">
                            <span class="inline-flex items-center gap-1"><i class="fa-solid fa-clock text-[9px]"></i> Window: 45 days</span>
                            <span class="inline-flex items-center gap-1"><i class="fa-solid fa-layer-group text-[9px]"></i> Source: incidents + attendance</span>
                        </div>
                    </article>
                @endforeach
            </div>
        </section>
    @endif

    <!-- Charts Row 1 -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Incident Trends Chart -->
        <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-lg font-bold text-gray-900">Incident Trends</h3>
                <span class="text-sm text-gray-500">Last 6 Months</span>
            </div>
            <div class="h-64">
                <canvas id="trendsChart"></canvas>
            </div>
        </div>

        <!-- Category Breakdown -->
        <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm">
            <h3 class="text-lg font-bold text-gray-900 mb-6">Violations by Category</h3>
            <div class="h-64">
                <canvas id="categoryChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Charts Row 2 -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- Grade Level Distribution -->
        <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm">
            <h3 class="text-lg font-bold text-gray-900 mb-6">By Grade Level</h3>
            <div class="h-48">
                <canvas id="gradeLevelChart"></canvas>
            </div>
        </div>

        <!-- Severity Distribution -->
        <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm">
            <h3 class="text-lg font-bold text-gray-900 mb-6">By Severity</h3>
            <div class="h-48">
                <canvas id="severityChart"></canvas>
            </div>
        </div>

        <!-- Status Distribution -->
        <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm">
            <h3 class="text-lg font-bold text-gray-900 mb-6">Current Status</h3>
            <div class="h-48">
                <canvas id="statusChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Top Offenders & Performance -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Top Offenders -->
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h3 class="text-lg font-bold text-gray-900">Repeat Offenders</h3>
                <p class="text-xs text-gray-500 mt-1">Students with multiple incidents this year</p>
            </div>
            <div class="divide-y divide-gray-100 max-h-80 overflow-y-auto">
                @forelse($data['topOffenders'] ?? [] as $offender)
                    <div class="px-6 py-4 flex items-center justify-between hover:bg-gray-50">
                        <div class="flex items-center">
                            <div class="h-10 w-10 rounded-full bg-red-100 flex items-center justify-center mr-4">
                                <i class="fa-solid fa-user text-red-600"></i>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900">{{ $offender['name'] }}</p>
                                <p class="text-xs text-gray-500">{{ $offender['grade_section'] }}</p>
                            </div>
                        </div>
                        <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-sm font-bold">
                            {{ $offender['incident_count'] }} incidents
                        </span>
                    </div>
                @empty
                    <div class="px-6 py-8 text-center text-gray-500">
                        <i class="fa-solid fa-check-circle text-green-500 text-2xl mb-2"></i>
                        <p>No repeat offenders this year</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Performance Metrics -->
        <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm">
            <h3 class="text-lg font-bold text-gray-900 mb-6">Processing Performance</h3>
            
            <div class="space-y-6">
                <!-- Average Processing Time -->
                @php
                    $avgDays = $data['performance']['avg_processing_days'] ?? 0;
                    $avgWidth = min($avgDays * 10, 100);
                @endphp
                <div>
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-sm font-medium text-gray-700">Avg. Processing Time</span>
                        <span class="text-sm font-bold text-gray-900">{{ $avgDays }} days</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="metric-bar bg-green-600 h-2 rounded-full" data-width="{{ $avgWidth }}"></div>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Target: Under 7 days</p>
                </div>

                <!-- Cases Processed -->
                @php
                    $processed = $data['performance']['processed_this_month'] ?? 0;
                    $processedWidth = min($processed * 5, 100);
                @endphp
                <div>
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-sm font-medium text-gray-700">Processed This Month</span>
                        <span class="text-sm font-bold text-gray-900">{{ $processed }}</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="metric-bar bg-blue-600 h-2 rounded-full" data-width="{{ $processedWidth }}"></div>
                    </div>
                </div>

                <!-- Year-over-Year Comparison -->
                <div class="pt-4 border-t border-gray-100">
                    <h4 class="text-sm font-bold text-gray-700 mb-4">Year-over-Year Comparison</h4>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-gray-50 rounded-xl p-4 text-center">
                            <p class="text-2xl font-black text-gray-900">{{ $data['comparative']['this_year'] ?? 0 }}</p>
                            <p class="text-xs text-gray-500">This Year</p>
                        </div>
                        <div class="bg-gray-50 rounded-xl p-4 text-center">
                            <p class="text-2xl font-black text-gray-900">{{ $data['comparative']['last_year'] ?? 0 }}</p>
                            <p class="text-xs text-gray-500">Last Year</p>
                        </div>
                    </div>
                    @php $compChange = $data['comparative']['change_percent'] ?? 0; @endphp
                    <div class="mt-4 text-center">
                        @if($compChange >= 0)
                            <span class="text-lg font-bold text-red-600">
                                <i class="fa-solid fa-arrow-up mr-1"></i>{{ abs($compChange) }}%
                            </span>
                        @else
                            <span class="text-lg font-bold text-green-600">
                                <i class="fa-solid fa-arrow-down mr-1"></i>{{ abs($compChange) }}%
                            </span>
                        @endif
                        <span class="text-sm text-gray-500 ml-2">{{ $data['comparative']['trend'] ?? 'stable' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Section Heatmap -->
    <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm">
        <h3 class="text-lg font-bold text-gray-900 mb-6">Section Incident Heatmap</h3>
        <div class="grid grid-cols-2 md:grid-cols-5 lg:grid-cols-10 gap-2">
            @forelse($data['sections'] ?? [] as $section)
                @php
                    $count = $section['count'];
                    if ($count >= 10) $colorClass = 'bg-red-100 text-red-800';
                    elseif ($count >= 6) $colorClass = 'bg-orange-100 text-orange-800';
                    elseif ($count >= 3) $colorClass = 'bg-yellow-100 text-yellow-800';
                    else $colorClass = 'bg-green-100 text-green-800';
                @endphp
                <div class="p-3 rounded-xl text-center cursor-pointer transition-all hover:scale-105 {{ $colorClass }}" title="{{ $section['label'] }}: {{ $count }} incidents">
                    <p class="text-xs font-bold truncate">{{ $section['label'] }}</p>
                    <p class="text-lg font-black">{{ $count }}</p>
                </div>
            @empty
                <div class="col-span-full text-center py-8 text-gray-500">
                    <i class="fa-solid fa-info-circle text-xl mb-2"></i>
                    <p>No section data available</p>
                </div>
            @endforelse
        </div>
        <div class="mt-4 flex items-center justify-end gap-4 text-xs">
            <span class="flex items-center gap-2">
                <span class="w-4 h-4 rounded bg-green-100"></span> Low (0-2)
            </span>
            <span class="flex items-center gap-2">
                <span class="w-4 h-4 rounded bg-yellow-100"></span> Medium (3-5)
            </span>
            <span class="flex items-center gap-2">
                <span class="w-4 h-4 rounded bg-orange-100"></span> High (6-9)
            </span>
            <span class="flex items-center gap-2">
                <span class="w-4 h-4 rounded bg-red-100"></span> Critical (10+)
            </span>
        </div>
    </div>

    <!-- Data Explorer with actual records -->
    @php
        $datasetMeta = $dataset ?? ['records' => []];
        $records = $datasetMeta['records'] ?? [];
        $filterValues = $filters ?? [];
        $currentLimit = request()->input('limit', $datasetMeta['limit'] ?? 50);
    @endphp
    <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm mt-8">
        <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
            <div>
                <h3 class="text-lg font-bold text-gray-900">Incident Data Explorer</h3>
                <p class="text-xs text-gray-500 mt-1">
                    Showing {{ count($records) }} of {{ $datasetMeta['total'] ?? count($records) }} matching incidents
                </p>
            </div>
            <a href="{{ route('incidents.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-green-700 bg-green-50 px-4 py-2 rounded-xl border border-green-100 hover:bg-green-100 transition">
                <i class="fa-solid fa-up-right-from-square text-xs"></i> Open Incidents Module
            </a>
        </div>

        <form method="GET" action="{{ route('admin.analytics') }}" class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-4 mb-6">
            <div>
                <label class="text-xs font-bold text-gray-500 uppercase block mb-1">Grade Level</label>
                <select name="grade_level" class="w-full border-gray-200 rounded-xl text-sm focus:ring-green-500 focus:border-green-500 bg-gray-50">
                    <option value="">All Grades</option>
                    @foreach(range(7, 12) as $grade)
                        <option value="{{ $grade }}" @selected(($filterValues['grade_level'] ?? null) == $grade)>Grade {{ $grade }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="text-xs font-bold text-gray-500 uppercase block mb-1">Section</label>
                <input type="text" name="section" value="{{ $filterValues['section'] ?? '' }}" placeholder="e.g. St. Paul" class="w-full border-gray-200 rounded-xl text-sm focus:ring-green-500 focus:border-green-500 bg-gray-50">
            </div>
            <div>
                <label class="text-xs font-bold text-gray-500 uppercase block mb-1">Severity</label>
                <select name="severity" class="w-full border-gray-200 rounded-xl text-sm focus:ring-green-500 focus:border-green-500 bg-gray-50">
                    <option value="">All Severities</option>
                    @foreach(['minor','moderate','major','critical'] as $severityOption)
                        <option value="{{ $severityOption }}" @selected(($filterValues['severity'] ?? null) === $severityOption)>{{ ucfirst($severityOption) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="text-xs font-bold text-gray-500 uppercase block mb-1">Status</label>
                <select name="status" class="w-full border-gray-200 rounded-xl text-sm focus:ring-green-500 focus:border-green-500 bg-gray-50">
                    <option value="">Any Status</option>
                    @foreach(['reported' => 'Reported', 'under_review' => 'Under Review', 'pending_approval' => 'Pending', 'approved' => 'Approved', 'closed' => 'Closed'] as $statusValue => $statusLabel)
                        <option value="{{ $statusValue }}" @selected(($filterValues['status'] ?? null) === $statusValue)>{{ $statusLabel }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="text-xs font-bold text-gray-500 uppercase block mb-1">Date From</label>
                <input type="date" name="date_from" value="{{ $filterValues['date_from'] ?? '' }}" class="w-full border-gray-200 rounded-xl text-sm focus:ring-green-500 focus:border-green-500 bg-gray-50">
            </div>
            <div>
                <label class="text-xs font-bold text-gray-500 uppercase block mb-1">Date To</label>
                <input type="date" name="date_to" value="{{ $filterValues['date_to'] ?? '' }}" class="w-full border-gray-200 rounded-xl text-sm focus:ring-green-500 focus:border-green-500 bg-gray-50">
            </div>
            <div>
                <label class="text-xs font-bold text-gray-500 uppercase block mb-1">Sample Size</label>
                <select name="limit" class="w-full border-gray-200 rounded-xl text-sm focus:ring-green-500 focus:border-green-500 bg-gray-50">
                    @foreach([25, 50, 100, 150, 200] as $option)
                        <option value="{{ $option }}" @selected($currentLimit == $option)>{{ $option }} records</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end gap-3">
                <button type="submit" class="flex-1 bg-green-700 hover:bg-green-800 text-white py-3 rounded-xl text-sm font-bold shadow-lg shadow-green-900/20">Apply Filters</button>
                <a href="{{ route('admin.analytics') }}" class="px-4 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-sm font-bold">Reset</a>
            </div>
        </form>

        <div class="overflow-x-auto border border-gray-100 rounded-2xl">
            <table class="w-full text-left text-sm">
                <thead class="bg-gray-50 text-gray-600 uppercase text-xs font-bold tracking-wider">
                    <tr>
                        <th class="px-4 py-3">Reference</th>
                        <th class="px-4 py-3">Student</th>
                        <th class="px-4 py-3">Grade / Section</th>
                        <th class="px-4 py-3">Category</th>
                        <th class="px-4 py-3">Severity</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Reported By</th>
                        <th class="px-4 py-3">Date</th>
                        <th class="px-4 py-3 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse($records as $record)
                        <tr class="hover:bg-green-50/40">
                            <td class="px-4 py-3 font-semibold text-gray-900">{{ $record['reference'] }}</td>
                            <td class="px-4 py-3">
                                <p class="font-bold text-gray-900">{{ $record['student'] ?? 'N/A' }}</p>
                                <p class="text-xs text-gray-500">{{ $record['student_id'] ?? '—' }}</p>
                            </td>
                            <td class="px-4 py-3 text-gray-700">{{ $record['grade_level'] ? 'Grade ' . $record['grade_level'] : '—' }}<br><span class="text-xs text-gray-500">{{ $record['section'] ?? '—' }}</span></td>
                            <td class="px-4 py-3 text-gray-700">{{ $record['category'] }}</td>
                            <td class="px-4 py-3">
                                @php
                                    $severityClassMap = [
                                        'critical' => 'bg-red-50 text-red-700 border-red-200',
                                        'major' => 'bg-orange-50 text-orange-700 border-orange-200',
                                        'moderate' => 'bg-yellow-50 text-yellow-700 border-yellow-200',
                                        'minor' => 'bg-green-50 text-green-700 border-green-200',
                                    ];
                                    $severityKey = strtolower($record['severity']);
                                    $severityClass = $severityClassMap[$severityKey] ?? 'bg-gray-100 text-gray-700 border-gray-200';
                                @endphp
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold border {{ $severityClass }}">
                                    {{ ucfirst($record['severity']) }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                @php
                                    $statusClassMap = [
                                        'pending_approval' => 'bg-amber-50 text-amber-700 border-amber-200',
                                        'under_review' => 'bg-purple-50 text-purple-700 border-purple-200',
                                        'reported' => 'bg-blue-50 text-blue-700 border-blue-200',
                                        'approved' => 'bg-green-50 text-green-700 border-green-200',
                                        'closed' => 'bg-gray-100 text-gray-700 border-gray-200',
                                    ];
                                    $statusClass = $statusClassMap[$record['status']] ?? 'bg-gray-100 text-gray-700 border-gray-200';
                                @endphp
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold border capitalize {{ $statusClass }}">
                                    {{ str_replace('_', ' ', $record['status']) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-gray-700">{{ $record['reporter'] }}</td>
                            <td class="px-4 py-3 text-gray-700">
                                <p class="font-semibold">{{ $record['date_label'] }}</p>
                                <p class="text-xs text-gray-500">{{ $record['time_label'] }}</p>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('incidents.show', $record['id']) }}" class="inline-flex items-center gap-2 text-sm font-semibold text-green-700 hover:text-green-900">View <i class="fa-solid fa-arrow-up-right-from-square text-xs"></i></a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-4 py-12 text-center text-gray-500">
                                <i class="fa-solid fa-chart-line text-2xl mb-2"></i>
                                <p>No incident data found for the selected filters.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Chart.js loaded via Vite bundle -->

<script type="application/json" id="analytics-trends-data">@json($data['trends'] ?? [])</script>
<script type="application/json" id="analytics-categories-data">@json($data['categories'] ?? [])</script>
<script type="application/json" id="analytics-grade-levels-data">@json($data['gradeLevels'] ?? [])</script>
<script type="application/json" id="analytics-severity-data">@json($data['severity'] ?? [])</script>
<script type="application/json" id="analytics-status-data">@json($data['performance']['status_distribution'] ?? [])</script>

<script>
function parseJsonScript(id) {
    var element = document.getElementById(id);

    if (!element) {
        return [];
    }

    try {
        return JSON.parse(element.textContent || '[]');
    } catch (error) {
        console.error('Failed to parse analytics data for', id, error);
        return [];
    }
}

function applyMetricBarWidths() {
    document.querySelectorAll('.metric-bar').forEach(function(element) {
        var width = Number(element.dataset.width || 0);
        element.style.width = width + '%';
    });
}

const trendsData = parseJsonScript('analytics-trends-data');
const categoriesData = parseJsonScript('analytics-categories-data');
const gradeLevelsData = parseJsonScript('analytics-grade-levels-data');
const severityData = parseJsonScript('analytics-severity-data');
const statusDistribution = parseJsonScript('analytics-status-data');

let chartInitAttempts = 0;
const maxChartInitAttempts = 40;

function loadChartJsFallback() {
    return new Promise(function(resolve, reject) {
        if (typeof window.Chart !== 'undefined') {
            resolve();
            return;
        }

        var existing = document.querySelector('script[data-chartjs-fallback="true"]');
        if (existing) {
            existing.addEventListener('load', function() { resolve(); }, { once: true });
            existing.addEventListener('error', function() { reject(new Error('Chart.js fallback failed to load')); }, { once: true });
            return;
        }

        var script = document.createElement('script');
        script.src = 'https://cdn.jsdelivr.net/npm/chart.js@4.5.1/dist/chart.umd.min.js';
        script.async = true;
        script.setAttribute('data-chartjs-fallback', 'true');
        script.onload = function() { resolve(); };
        script.onerror = function() { reject(new Error('Chart.js fallback failed to load')); };
        document.head.appendChild(script);
    });
}

function initCharts() {
    applyMetricBarWidths();

    // Wait for Chart.js to load from Vite bundle
    if (typeof window.Chart === 'undefined') {
        chartInitAttempts++;

        if (chartInitAttempts === 10) {
            loadChartJsFallback()
                .then(function() {
                    console.log('Chart.js fallback loaded from CDN');
                    initCharts();
                })
                .catch(function(error) {
                    console.error(error.message);
                });
        }

        if (chartInitAttempts >= maxChartInitAttempts) {
            console.error('Chart.js is unavailable. Ensure Vite assets are built or dev server is running.');
            return;
        }

        console.log('Waiting for Chart.js to load... attempt', chartInitAttempts);
        setTimeout(initCharts, 100);
        return;
    }

    console.log('Chart.js loaded! Initializing charts with data:', { trendsData, categoriesData, gradeLevelsData, severityData, statusDistribution });
    
    var ChartJS = window.Chart;

    // Incident Trends Chart
    if (trendsData.length > 0) {
        new ChartJS(document.getElementById('trendsChart'), {
            type: 'line',
            data: {
                labels: trendsData.map(function(t) { return t.label; }),
                datasets: [{
                    label: 'Incidents',
                    data: trendsData.map(function(t) { return t.count; }),
                    borderColor: '#15803d',
                    backgroundColor: 'rgba(21, 128, 61, 0.1)',
                    fill: true,
                    tension: 0.4,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true } }
            }
        });
        console.log('Trends chart created');
    }

    // Category Chart
    if (categoriesData.length > 0) {
        var colors = ['#15803d', '#22c55e', '#86efac', '#dcfce7', '#f0fdf4'];
        var topCategories = categoriesData.slice(0, 5);
        
        new ChartJS(document.getElementById('categoryChart'), {
            type: 'doughnut',
            data: {
                labels: topCategories.map(function(c) { return c.name; }),
                datasets: [{
                    data: topCategories.map(function(c) { return c.count; }),
                    backgroundColor: colors,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                        labels: { boxWidth: 12, padding: 15 }
                    }
                }
            }
        });
        console.log('Category chart created');
    }

    // Grade Level Chart
    if (gradeLevelsData.length > 0) {
        new ChartJS(document.getElementById('gradeLevelChart'), {
            type: 'bar',
            data: {
                labels: gradeLevelsData.map(function(g) { return g.label; }),
                datasets: [{
                    label: 'Incidents',
                    data: gradeLevelsData.map(function(g) { return g.count; }),
                    backgroundColor: '#15803d',
                    borderRadius: 6,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true } }
            }
        });
        console.log('Grade level chart created');
    }

    // Severity Chart
    if (severityData.length > 0) {
        var severityColors = {
            'minor': '#22c55e',
            'moderate': '#f59e0b',
            'major': '#ef4444',
            'critical': '#7f1d1d'
        };
        
        new ChartJS(document.getElementById('severityChart'), {
            type: 'pie',
            data: {
                labels: severityData.map(function(s) { return s.label; }),
                datasets: [{
                    data: severityData.map(function(s) { return s.count; }),
                    backgroundColor: severityData.map(function(s) { return severityColors[s.severity] || '#6b7280'; }),
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { boxWidth: 12, padding: 10 }
                    }
                }
            }
        });
        console.log('Severity chart created');
    }

    // Status Chart
    var statusKeys = Object.keys(statusDistribution);
    if (statusKeys.length > 0) {
        var statusColors = {
            'reported': '#3b82f6',
            'under_review': '#f59e0b',
            'pending_approval': '#8b5cf6',
            'approved': '#22c55e',
            'closed': '#6b7280'
        };
        var statusLabels = {
            'reported': 'Reported',
            'under_review': 'Under Review',
            'pending_approval': 'Pending',
            'approved': 'Approved',
            'closed': 'Archived'
        };
        
        new ChartJS(document.getElementById('statusChart'), {
            type: 'doughnut',
            data: {
                labels: statusKeys.map(function(k) { return statusLabels[k] || k; }),
                datasets: [{
                    data: Object.values(statusDistribution),
                    backgroundColor: statusKeys.map(function(k) { return statusColors[k] || '#6b7280'; }),
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { boxWidth: 12, padding: 10 }
                    }
                }
            }
        });
        console.log('Status chart created');
    }
}

// Start initialization - try immediately and also on DOMContentLoaded
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initCharts);
} else {
    initCharts();
}
</script>
@endsection
