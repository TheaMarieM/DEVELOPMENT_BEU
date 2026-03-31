@extends('layouts.app')

@section('content')
<!-- Header -->
<header class="bg-white border-b border-gray-200 shadow-sm sticky top-0 z-40">
    <div class="px-8 py-4 flex flex-wrap items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <span class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-700 border border-emerald-100 flex items-center justify-center">
                <i class="fa-solid fa-calendar-check"></i>
            </span>
            <div>
                <p class="text-[11px] uppercase tracking-[0.35em] text-gray-400">Discipline Suite</p>
                <h2 class="text-2xl font-black text-gray-900">Attendance Management</h2>
                <p class="text-sm text-gray-500">Record and track student absences, tardiness, and excused entries</p>
            </div>
        </div>
        <div class="flex flex-wrap items-center gap-3 text-xs font-semibold text-gray-500">
            <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-gray-100 border border-gray-200">
                <i class="fa-solid fa-clock text-gray-400"></i> Last sync {{ now()->diffForHumans() }}
            </span>
            <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-gray-100 border border-gray-200">
                <i class="fa-solid fa-bell text-gray-400"></i> Auto notify guardians enabled
            </span>
        </div>
    </div>
</header>

<div class="p-8 max-w-7xl mx-auto space-y-6">
    @php
        $recordsCollection = method_exists($attendanceRecords, 'getCollection') ? $attendanceRecords->getCollection() : collect($attendanceRecords);
        $today = now();
        $todayLogged = $recordsCollection->filter(fn ($record) => optional($record->date)->isSameDay($today))->count();
        $tardyLogged = $recordsCollection->where('status', 'tardy')->count();
        $excusedLogged = $recordsCollection->where('status', 'excused')->count();
        $attendanceSummary = [
            [
                'label' => 'Entries logged today',
                'value' => $todayLogged,
                'meta' => $today->format('M d, Y'),
                'icon' => 'fa-sun'
            ],
            [
                'label' => 'Tardy (visible)',
                'value' => $tardyLogged,
                'meta' => 'Current filters',
                'icon' => 'fa-clock-rotate-left'
            ],
            [
                'label' => 'Excused (visible)',
                'value' => $excusedLogged,
                'meta' => 'Awaiting verification',
                'icon' => 'fa-check-double'
            ],
        ];
        $recentRecords = $recordsCollection->sortByDesc('date')->take(3);
    @endphp

    <section class="grid grid-cols-1 md:grid-cols-3 gap-4">
        @foreach($attendanceSummary as $card)
            <div class="border border-gray-200 rounded-2xl p-5 bg-white/80">
                <div class="flex items-center justify-between mb-3">
                    <p class="text-[11px] font-bold uppercase tracking-[0.2em] text-gray-400">{{ $card['label'] }}</p>
                    <span class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                        <i class="fa-solid {{ $card['icon'] }} text-sm"></i>
                    </span>
                </div>
                <div class="text-3xl font-black text-gray-900">{{ $card['value'] }}</div>
                <p class="text-xs text-gray-500 mt-1">{{ $card['meta'] }}</p>
            </div>
        @endforeach
    </section>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-6 py-4 rounded-lg flex items-center gap-3">
            <i class="fa-solid fa-circle-check text-green-600 text-xl"></i>
            <p class="font-medium">{{ session('success') }}</p>
        </div>
    @endif

    @if(!empty($focusedStudent))
        <div class="bg-emerald-50 border border-emerald-200 rounded-2xl px-6 py-4 flex flex-wrap items-center justify-between gap-4">
            <div>
                <p class="text-[11px] font-bold text-emerald-600 uppercase tracking-widest">Logging attendance for</p>
                <h3 class="text-xl font-black text-gray-900">{{ $focusedStudent->full_name }}</h3>
                <p class="text-sm text-gray-600 mt-1">ID: {{ $focusedStudent->student_id }} • Grade {{ $focusedStudent->grade_level }} - {{ $focusedStudent->section }}</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('students.show', $focusedStudent) }}" class="px-3 py-2 text-xs font-bold rounded-lg border border-emerald-200 text-emerald-700 hover:bg-emerald-100 transition">View Profile</a>
                <a href="{{ route('attendance.index') }}" class="px-3 py-2 text-xs font-bold rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-100 transition">Clear Selection</a>
            </div>
        </div>
    @endif

    <!-- Log Attendance Form -->
    <div class="bg-white rounded-xl border border-gray-200 card-shadow p-8">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wider">Log Student Attendance</h3>
                <p class="text-xs text-gray-500 mt-1">Record absences and tardiness for students</p>
            </div>
            <span class="text-[11px] font-semibold text-emerald-600 bg-emerald-50 border border-emerald-100 px-3 py-1 rounded-full">Guided workflow</span>
        </div>

        <div class="grid lg:grid-cols-3 gap-8">
            <form action="{{ route('attendance.store') }}" method="POST" class="lg:col-span-2">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-2">Select Student</label>
                        <select name="student_id" required class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-green-500 focus:border-transparent outline-none @error('student_id') border-red-300 @enderror">
                            <option value="">Choose a student...</option>
                            @foreach($students as $student)
                                <option value="{{ $student->id }}" {{ old('student_id', request('student_id')) == $student->id ? 'selected' : '' }}>
                                    {{ $student->last_name }}, {{ $student->first_name }} (Grade {{ $student->grade_level }} - {{ $student->section }})
                                </option>
                            @endforeach
                        </select>
                        @error('student_id')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-2">Attendance Type</label>
                        <select name="status" required class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-green-500 focus:border-transparent outline-none @error('status') border-red-300 @enderror">
                            <option value="">Select type...</option>
                            <option value="absent" {{ old('status') == 'absent' ? 'selected' : '' }}>Absent</option>
                            <option value="tardy" {{ old('status') == 'tardy' ? 'selected' : '' }}>Tardy</option>
                            <option value="excused" {{ old('status') == 'excused' ? 'selected' : '' }}>Excused</option>
                        </select>
                        @error('status')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-2">Date</label>
                        <input type="date" name="date" required value="{{ old('date', now()->format('Y-m-d')) }}"
                               class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-green-500 focus:border-transparent outline-none @error('date') border-red-300 @enderror">
                        @error('date')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-2">Time In (for Tardy)</label>
                        <input type="time" name="time_in" value="{{ old('time_in', now()->format('H:i')) }}"
                               class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-green-500 focus:border-transparent outline-none @error('time_in') border-red-300 @enderror">
                        @error('time_in')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mb-6">
                    <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-2">Remarks (Optional)</label>
                    <textarea name="remarks" rows="2" placeholder="Additional notes..."
                              class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-green-500 focus:border-transparent outline-none resize-none @error('remarks') border-red-300 @enderror">{{ old('remarks') }}</textarea>
                    @error('remarks')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="bg-green-700 hover:bg-green-800 text-white px-8 py-3 rounded-lg text-sm font-bold transition-all shadow-md flex items-center gap-2">
                        <i class="fa-solid fa-clipboard-check"></i>
                        Log Attendance
                    </button>
                </div>
            </form>

            <aside class="bg-gray-50 border border-gray-200 rounded-2xl p-5 space-y-4">
                <div>
                    <p class="text-[11px] font-bold text-gray-500 uppercase tracking-[0.2em] mb-1">Logging checklist</p>
                    <p class="text-sm text-gray-600">Double-check student identity, status, and supporting remark before submitting to keep the master record auditable.</p>
                </div>
                <ul class="space-y-3 text-sm text-gray-600">
                    <li class="flex items-start gap-2">
                        <i class="fa-solid fa-check text-emerald-500 mt-1"></i>
                        Tag tardy entries with the exact time to unlock punctuality analytics.
                    </li>
                    <li class="flex items-start gap-2">
                        <i class="fa-solid fa-check text-emerald-500 mt-1"></i>
                        Use concise remarks (max 140 chars) so advisers can scan on mobile.
                    </li>
                    <li class="flex items-start gap-2">
                        <i class="fa-solid fa-check text-emerald-500 mt-1"></i>
                        Excused entries automatically notify parents through the messaging hub.
                    </li>
                </ul>
                <div class="rounded-xl bg-white border border-dashed border-gray-300 p-4 text-xs text-gray-500">
                    <p class="font-semibold text-gray-700 mb-1">Need to batch upload?</p>
                    <p>Use the CSV template under Reports > Attendance to import more than 20 entries at once.</p>
                </div>
            </aside>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl border border-gray-200 card-shadow p-6">
        <form method="GET" action="{{ route('attendance.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-2">Student</label>
                <select name="student_id" class="w-full px-3 py-2 border border-gray-200 rounded text-sm">
                    <option value="">All Students</option>
                    @foreach($students as $student)
                        <option value="{{ $student->id }}" {{ request('student_id') == $student->id ? 'selected' : '' }}>
                            {{ $student->last_name }}, {{ $student->first_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-2">Status</label>
                <select name="status" class="w-full px-3 py-2 border border-gray-200 rounded text-sm">
                    <option value="">All Types</option>
                    <option value="absent" {{ request('status') == 'absent' ? 'selected' : '' }}>Absent</option>
                    <option value="tardy" {{ request('status') == 'tardy' ? 'selected' : '' }}>Tardy</option>
                    <option value="excused" {{ request('status') == 'excused' ? 'selected' : '' }}>Excused</option>
                </select>
            </div>
            <div>
                <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-2">From Date</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full px-3 py-2 border border-gray-200 rounded text-sm">
            </div>
            <div>
                <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-2">To Date</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}" class="w-full px-3 py-2 border border-gray-200 rounded text-sm">
            </div>
            <div class="md:col-span-4 flex gap-2">
                <button type="submit" class="px-4 py-2 bg-green-700 text-white rounded text-sm font-bold hover:bg-green-800 transition-colors">
                    <i class="fa-solid fa-filter mr-1"></i> Apply Filters
                </button>
                <a href="{{ route('attendance.index') }}" class="px-4 py-2 bg-gray-100 text-gray-600 rounded text-sm font-bold hover:bg-gray-200 transition-colors">
                    <i class="fa-solid fa-rotate-left mr-1"></i> Clear
                </a>
            </div>
        </form>
    </div>

    @if($recentRecords->isNotEmpty())
        <section class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @foreach($recentRecords as $record)
                <div class="border border-gray-200 rounded-2xl p-5 bg-white/80">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-semibold text-gray-500">{{ $record->date->format('M d, Y') }}</span>
                        <span class="text-[11px] font-bold px-2.5 py-1 rounded-full
                            @class([
                                'bg-red-50 text-red-600 border border-red-100' => $record->status === 'absent',
                                'bg-amber-50 text-amber-600 border border-amber-100' => $record->status === 'tardy',
                                'bg-blue-50 text-blue-600 border border-blue-100' => $record->status === 'excused',
                            ])">
                            {{ ucfirst($record->status) }}
                        </span>
                    </div>
                    <div class="text-base font-semibold text-gray-900">{{ $record->student->full_name }}</div>
                    <p class="text-xs text-gray-500 mb-3">Grade {{ $record->student->grade_level }} • {{ $record->student->section }}</p>
                    <p class="text-sm text-gray-600 line-clamp-2">{{ $record->remarks ?? 'No remarks added' }}</p>
                </div>
            @endforeach
        </section>
    @endif

    <!-- Attendance Records List -->
    <div class="bg-white rounded-xl border border-gray-200 card-shadow overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
            <div>
                <h3 class="text-base font-bold text-gray-800">Attendance Records</h3>
                <p class="text-xs text-gray-500 mt-1">Complete log of all recorded absences and tardiness</p>
            </div>
            <span class="text-xs text-gray-500">{{ $attendanceRecords->total() }} Record(s)</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4 text-[10px] font-bold uppercase text-gray-400 tracking-wider">Date</th>
                        <th class="px-6 py-4 text-[10px] font-bold uppercase text-gray-400 tracking-wider">Student</th>
                        <th class="px-6 py-4 text-[10px] font-bold uppercase text-gray-400 tracking-wider">Grade/Section</th>
                        <th class="px-6 py-4 text-[10px] font-bold uppercase text-gray-400 tracking-wider">Status</th>
                        <th class="px-6 py-4 text-[10px] font-bold uppercase text-gray-400 tracking-wider">Time In</th>
                        <th class="px-6 py-4 text-[10px] font-bold uppercase text-gray-400 tracking-wider">Remarks</th>
                        <th class="px-6 py-4 text-[10px] font-bold uppercase text-gray-400 tracking-wider">Recorded By</th>
                        <th class="px-6 py-4 text-[10px] font-bold uppercase text-gray-400 tracking-wider">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                    @forelse($attendanceRecords as $record)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="font-semibold text-gray-800">{{ $record->date->format('M d, Y') }}</div>
                            <div class="text-xs text-gray-500">{{ $record->date->format('l') }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="font-semibold text-gray-800">{{ $record->student->full_name }}</div>
                            <div class="text-xs text-gray-500">ID: {{ $record->student->student_id }}</div>
                        </td>
                        <td class="px-6 py-4 text-gray-600">
                            Grade {{ $record->student->grade_level }} - {{ $record->student->section }}
                        </td>
                        <td class="px-6 py-4">
                            @if($record->status === 'absent')
                                <span class="bg-red-100 text-red-700 text-xs font-bold px-2.5 py-1 rounded uppercase">Absent</span>
                            @elseif($record->status === 'tardy')
                                <span class="bg-amber-100 text-amber-700 text-xs font-bold px-2.5 py-1 rounded uppercase">Tardy</span>
                            @elseif($record->status === 'excused')
                                <span class="bg-blue-100 text-blue-700 text-xs font-bold px-2.5 py-1 rounded uppercase">Excused</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-gray-600">
                            @if($record->time_in)
                                {{ \Carbon\Carbon::parse($record->time_in)->format('h:i A') }}
                            @else
                                <span class="text-gray-400">—</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-gray-600 text-xs">{{ $record->remarks ?? '—' }}</td>
                        <td class="px-6 py-4 text-gray-500 text-xs">
                            {{ $record->recorder->name ?? 'System' }}
                            <div class="text-[10px] text-gray-400">{{ $record->created_at->format('M d, h:i A') }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <form action="{{ route('attendance.destroy', $record) }}" method="POST" class="inline" onsubmit="return confirm('Delete this attendance record?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800 text-xs font-bold">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center text-gray-400 text-sm">
                            <i class="fa-solid fa-calendar-check text-3xl mb-3 opacity-30"></i>
                            <p>No attendance records found.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($attendanceRecords->hasPages())
        <div class="px-6 py-4 border-t border-gray-100 flex justify-between items-center">
            <div class="text-xs text-gray-500">
                Showing {{ $attendanceRecords->firstItem() }}-{{ $attendanceRecords->lastItem() }} of {{ $attendanceRecords->total() }} records
            </div>
            <div class="flex gap-2">
                @if($attendanceRecords->onFirstPage())
                    <span class="px-3 py-1.5 text-xs font-bold text-gray-400 bg-gray-100 rounded cursor-not-allowed">Prev</span>
                @else
                    <a href="{{ $attendanceRecords->previousPageUrl() }}" class="px-3 py-1.5 text-xs font-bold text-gray-700 bg-white border border-gray-200 rounded hover:bg-gray-50">Prev</a>
                @endif

                <span class="px-3 py-1.5 text-xs font-bold text-white bg-green-700 rounded">{{ $attendanceRecords->currentPage() }}</span>

                @if($attendanceRecords->hasMorePages())
                    <a href="{{ $attendanceRecords->nextPageUrl() }}" class="px-3 py-1.5 text-xs font-bold text-gray-700 bg-white border border-gray-200 rounded hover:bg-gray-50">Next</a>
                @else
                    <span class="px-3 py-1.5 text-xs font-bold text-gray-400 bg-gray-100 rounded cursor-not-allowed">Next</span>
                @endif
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
