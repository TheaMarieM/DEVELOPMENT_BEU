@extends('layouts.adviser')

@section('content')
@php
    $userName = auth()->user()->name ?? 'Adviser';
    $fullName = $student->first_name . ' ' . ($student->middle_name ? $student->middle_name . ' ' : '') . $student->last_name;
@endphp

<header class="bg-white border-b border-gray-200 px-8 py-4 flex flex-wrap gap-4 justify-between items-center sticky top-0 z-30 shadow-sm">
    <div>
        <div class="flex items-center gap-3 mb-2">
            <a href="{{ route('adviser.dashboard') }}" class="text-gray-500 hover:text-gray-700">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <h1 class="text-2xl font-bold text-gray-900">Student Complete Profile</h1>
        </div>
        <p class="text-sm text-gray-500 mt-1">{{ $fullName }} · {{ $student->student_id }}</p>
    </div>
    <div class="flex items-center gap-4">
        <div class="h-10 w-px bg-gray-300"></div>
        <div class="flex items-center gap-3">
            <p class="text-sm font-bold text-gray-900">Adviser</p>
            <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center text-gray-600">
                <i class="fa-solid fa-user"></i>
            </div>
        </div>
    </div>
</header>

<div class="p-8 space-y-8">
    <section class="grid grid-cols-1 md:grid-cols-3 gap-5">
        <div class="bg-white border border-gray-200 rounded-2xl p-4 card-shadow">
            <div class="flex items-center justify-between">
                <p class="text-xs uppercase tracking-wide text-gray-400 font-semibold">Total Incidents</p>
                <span class="w-9 h-9 rounded-full bg-rose-50 text-rose-600 flex items-center justify-center text-sm">
                    <i class="fa-solid fa-clipboard-list"></i>
                </span>
            </div>
            <h3 class="text-3xl font-black text-rose-600 mt-2">{{ str_pad($student->incidents->count(), 2, '0', STR_PAD_LEFT) }}</h3>
            <p class="text-xs text-gray-500 mt-1">Recorded violations</p>
        </div>
        <div class="bg-white border border-gray-200 rounded-2xl p-4 card-shadow">
            <div class="flex items-center justify-between">
                <p class="text-xs uppercase tracking-wide text-gray-400 font-semibold">Tardy Records</p>
                <span class="w-9 h-9 rounded-full bg-amber-50 text-amber-600 flex items-center justify-center text-sm">
                    <i class="fa-solid fa-clock"></i>
                </span>
            </div>
            <h3 class="text-3xl font-black text-amber-600 mt-2">{{ str_pad($tardyCount, 2, '0', STR_PAD_LEFT) }}</h3>
            <p class="text-xs text-gray-500 mt-1">Late arrivals recorded</p>
        </div>
        <div class="bg-white border border-gray-200 rounded-2xl p-4 card-shadow">
            <div class="flex items-center justify-between">
                <p class="text-xs uppercase tracking-wide text-gray-400 font-semibold">Absence Records</p>
                <span class="w-9 h-9 rounded-full bg-purple-50 text-purple-600 flex items-center justify-center text-sm">
                    <i class="fa-solid fa-calendar-xmark"></i>
                </span>
            </div>
            <h3 class="text-3xl font-black text-purple-600 mt-2">{{ str_pad($absentCount, 2, '0', STR_PAD_LEFT) }}</h3>
            <p class="text-xs text-gray-500 mt-1">Days absent recorded</p>
        </div>
    </section>

    <!-- Complete Student Information -->
    <section class="bg-white border border-gray-200 rounded-2xl overflow-hidden card-shadow">
        <div class="px-6 py-4 border-b border-gray-100">
            <p class="text-xs uppercase tracking-wide text-gray-400 font-semibold">Complete Student Information</p>
            <h2 class="text-xl font-black text-gray-900 mt-1">Profile & Contact Details</h2>
        </div>
        <div class="p-6 space-y-6">
            <!-- Basic Details -->
            <div>
                <h3 class="text-sm font-bold text-gray-700 mb-3 pb-2 border-b border-gray-200">Basic Details</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs uppercase tracking-wide text-gray-400 font-semibold mb-1">Student ID</p>
                        <p class="text-sm font-semibold text-gray-900">{{ $student->student_id }}</p>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-wide text-gray-400 font-semibold mb-1">Full Name</p>
                        <p class="text-sm font-semibold text-gray-900">{{ $fullName }}</p>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-wide text-gray-400 font-semibold mb-1">Email Address</p>
                        <p class="text-sm font-semibold text-gray-900">{{ $student->email ?? 'Not provided' }}</p>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-wide text-gray-400 font-semibold mb-1">Status</p>
                        <span class="inline-flex px-3 py-1 rounded-full text-xs font-bold {{ $student->status === 'active' ? 'bg-green-50 text-green-700 border border-green-200' : 'bg-gray-100 text-gray-600' }}">
                            {{ ucfirst($student->status) }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Residential Details -->
            <div>
                <h3 class="text-sm font-bold text-gray-700 mb-3 pb-2 border-b border-gray-200">Residential Details</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs uppercase tracking-wide text-gray-400 font-semibold mb-1">Residential Address</p>
                        <p class="text-sm text-gray-900">{{ $student->residential_address ?? 'Not provided' }}</p>
                    </div>
                    @if($student->boarding_address)
                    <div>
                        <p class="text-xs uppercase tracking-wide text-gray-400 font-semibold mb-1">Boarding House Address</p>
                        <p class="text-sm text-gray-900">{{ $student->boarding_address }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Emergency Contact -->
            <div>
                <h3 class="text-sm font-bold text-gray-700 mb-3 pb-2 border-b border-gray-200">Emergency Contact</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs uppercase tracking-wide text-gray-400 font-semibold mb-1">Guardian Name</p>
                        <p class="text-sm font-semibold text-gray-900">{{ $student->guardian_name ?? 'Not provided' }}</p>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-wide text-gray-400 font-semibold mb-1">Contact Number</p>
                        <p class="text-sm font-semibold text-gray-900">{{ $student->guardian_contact ?? 'Not provided' }}</p>
                    </div>
                </div>
            </div>

            <!-- Parents Details -->
            <div>
                <h3 class="text-sm font-bold text-gray-700 mb-3 pb-2 border-b border-gray-200">Parents Details</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs uppercase tracking-wide text-gray-400 font-semibold mb-1">Mother's Full Name</p>
                        <p class="text-sm font-semibold text-gray-900">{{ $student->mother_name ?? 'Not provided' }}</p>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-wide text-gray-400 font-semibold mb-1">Father's Full Name</p>
                        <p class="text-sm font-semibold text-gray-900">{{ $student->father_name ?? 'Not provided' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="bg-white border border-gray-200 rounded-2xl overflow-hidden card-shadow">
        <div class="px-6 py-4 border-b border-gray-100">
            <p class="text-xs uppercase tracking-wide text-gray-400 font-semibold">Incident Records</p>
            <h2 class="text-xl font-black text-gray-900 mt-1">Violation History</h2>
            <p class="text-xs text-gray-500 mt-1">Overview of all recorded incidents for this student.</p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-[10px] uppercase tracking-[0.2em] text-gray-500">
                    <tr>
                        <th class="px-6 py-4 text-left">Date</th>
                        <th class="px-6 py-4 text-left">Incident ID</th>
                        <th class="px-6 py-4 text-left">Violation Type</th>
                        <th class="px-6 py-4 text-left">Offense Level</th>
                        <th class="px-6 py-4 text-left">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($student->incidents as $incident)
                    @php
                        $statusMeta = [
                            'reported' => ['label' => 'Reported', 'class' => 'bg-blue-50 text-blue-700 border border-blue-200'],
                            'pending_approval' => ['label' => 'Pending', 'class' => 'bg-amber-50 text-amber-700 border border-amber-200'],
                            'under_review' => ['label' => 'Under Review', 'class' => 'bg-gray-100 text-gray-600 border border-gray-200'],
                            'approved' => ['label' => 'Approved', 'class' => 'bg-emerald-50 text-emerald-700 border border-emerald-200'],
                        ];
                        $status = $statusMeta[$incident->status] ?? ['label' => ucfirst($incident->status), 'class' => 'bg-gray-100 text-gray-600'];
                        $offenseMeta = [
                            'first' => ['label' => '1st Offense', 'class' => 'bg-blue-50 text-blue-700'],
                            'second' => ['label' => '2nd Offense', 'class' => 'bg-amber-50 text-amber-700'],
                            'third' => ['label' => '3rd Offense', 'class' => 'bg-rose-50 text-rose-700'],
                        ];
                        $offense = $offenseMeta[$incident->offense_number] ?? ['label' => ucfirst($incident->offense_number), 'class' => 'bg-gray-100 text-gray-600'];
                    @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-gray-900 font-medium">{{ \Carbon\Carbon::parse($incident->incident_date)->format('M d, Y') }}</td>
                        <td class="px-6 py-4 text-gray-600 font-mono text-xs">{{ $incident->incident_number }}</td>
                        <td class="px-6 py-4 text-gray-900 font-semibold">
                            {{ $incident->category->name ?? 'N/A' }}
                            @if($incident->custom_violation_description)
                            <br><span class="text-xs text-gray-500">{{ $incident->custom_violation_description }}</span>
                            @elseif($incident->clause)
                            <br><span class="text-xs text-gray-500">{{ $incident->clause->code ?? $incident->clause->clause_number }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex px-3 py-1 rounded-full text-xs font-bold {{ $offense['class'] }}">
                                {{ $offense['label'] }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex px-3 py-1 rounded-full text-xs font-bold {{ $status['class'] }}">
                                {{ $status['label'] }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                            <i class="fa-solid fa-inbox text-4xl mb-3 opacity-30"></i>
                            <p class="font-semibold">No incidents recorded</p>
                            <p class="text-xs mt-1">This student has a clean record.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>

@endsection
