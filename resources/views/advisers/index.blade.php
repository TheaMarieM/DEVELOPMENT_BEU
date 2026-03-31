@extends('layouts.app')

@section('content')
<!-- Header -->
<header class="bg-white border-b border-gray-200 px-8 py-5 flex flex-wrap gap-4 items-center justify-between sticky top-0 z-30">
    <div>
        <p class="text-[11px] uppercase font-bold tracking-[0.3em] text-emerald-600">User Management</p>
        <h2 class="text-xl font-bold text-gray-900">Personnel Management: Advisers</h2>
        <p class="text-xs text-gray-500 font-medium mt-0.5">Manage access, advisory coverage, and registry compliance for BEU faculty</p>
    </div>
    <div class="flex flex-wrap gap-3 items-center">
        <button class="px-4 py-2 border border-gray-200 text-xs font-bold text-gray-600 rounded-lg hover:bg-gray-50 transition flex items-center gap-2">
            <i class="fa-solid fa-download text-sm"></i> Export Directory
        </button>
        <a href="{{ route('advisers.create') }}" class="px-5 py-2.5 bg-emerald-600 text-white rounded-lg text-sm font-semibold shadow-sm hover:bg-emerald-700 transition flex items-center gap-2">
            <i class="fa-solid fa-plus text-xs"></i> Register New Adviser
        </a>
    </div>
</header>

<div class="p-8 max-w-7xl mx-auto space-y-8">
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded-xl border border-gray-200 card-shadow">
            <div class="flex items-center justify-between mb-3">
                <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Total Advisers</p>
                <span class="text-[10px] font-semibold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded">Active</span>
            </div>
            <div class="flex items-baseline gap-2">
                <h4 class="text-4xl font-bold text-gray-900">{{ str_pad($totalAdvisers, 2, '0', STR_PAD_LEFT) }}</h4>
                <span class="text-xs text-gray-500">Faculty in roster</span>
            </div>
            <p class="text-xs text-gray-400 mt-2">Includes all homeroom advisers cleared for the current term.</p>
        </div>

        <div class="bg-white p-6 rounded-xl border border-gray-200 card-shadow">
            <div class="flex items-center justify-between mb-3">
                <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Sections Covered</p>
                <span class="text-[10px] font-semibold text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded">JHS Dept</span>
            </div>
            <div class="flex items-baseline gap-2">
                <h4 class="text-4xl font-bold text-gray-900">{{ str_pad($sectionsCovered, 2, '0', STR_PAD_LEFT) }}</h4>
                <span class="text-xs text-gray-500">Advisory sections</span>
            </div>
            <p class="text-xs text-gray-400 mt-2">Updated as soon as registrar confirms class assignments.</p>
        </div>

        <div class="bg-white p-6 rounded-xl border border-gray-200 card-shadow">
            <div class="flex items-center justify-between mb-3">
                <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Reports Logged</p>
                <span class="text-[10px] font-semibold text-amber-600 bg-amber-50 px-2 py-0.5 rounded">This Year</span>
            </div>
            <div class="flex items-baseline gap-2">
                <h4 class="text-4xl font-bold text-amber-600">{{ $reportsLogged }}</h4>
                <span class="text-xs text-gray-500">Case updates</span>
            </div>
            <p class="text-xs text-gray-400 mt-2">Incident reports submitted by or assigned to advisers.</p>
        </div>
    </div>

    <!-- Toolbar -->
    <div class="bg-white border border-gray-200 rounded-xl card-shadow px-6 py-4 flex flex-wrap gap-4 items-center justify-between">
        <div class="relative w-full lg:w-96">
            <i class="fa-solid fa-magnifying-glass absolute left-3 top-2.5 text-gray-400 text-xs"></i>
            <input type="text" placeholder="Search by name or employee ID..." class="pl-9 pr-4 py-2 border border-gray-200 rounded-lg text-xs focus:ring-1 focus:ring-emerald-500 outline-none w-full bg-gray-50">
        </div>
        <div class="flex gap-2 text-[11px] font-semibold text-gray-500 uppercase tracking-wide">
            <span class="px-3 py-1 rounded-full border border-emerald-100 text-emerald-700 bg-emerald-50">Active</span>
            <span class="px-3 py-1 rounded-full border border-gray-200">Pending Orientation</span>
            <span class="px-3 py-1 rounded-full border border-gray-200">Missing Contact</span>
        </div>
    </div>

    <!-- Advisers Table -->
    <div class="bg-white rounded-xl border border-gray-200 card-shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
            <span class="text-xs text-gray-500">Showing {{ $advisers->count() }} adviser(s)</span>
            <button class="px-4 py-2 border border-gray-200 rounded-lg text-xs font-bold text-gray-600 hover:bg-gray-50 flex items-center gap-2">
                <i class="fa-solid fa-bars-staggered"></i>
                View Filters
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4 text-[10px] font-bold uppercase text-gray-400 tracking-wider">Faculty Profile</th>
                        <th class="px-6 py-4 text-[10px] font-bold uppercase text-gray-400 tracking-wider">Employee ID</th>
                        <th class="px-6 py-4 text-[10px] font-bold uppercase text-gray-400 tracking-wider">Handling Section</th>
                        <th class="px-6 py-4 text-[10px] font-bold uppercase text-gray-400 tracking-wider">Registry Status</th>
                        <th class="px-6 py-4 text-[10px] font-bold uppercase text-gray-400 tracking-wider text-right">Administrative Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                    @forelse($advisers as $adviser)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-700 font-bold text-sm uppercase">
                                    {{ substr($adviser->name, 0, 1) }}{{ substr(explode(' ', $adviser->name)[1] ?? '', 0, 1) }}
                                </div>
                                <div>
                                    <div class="font-semibold text-gray-800">{{ $adviser->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $adviser->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-gray-600 font-mono text-xs">{{ $adviser->employee_id }}</td>
                        <td class="px-6 py-4 text-gray-700">
                            @if($adviser->advisedStudents->isNotEmpty())
                                @php
                                    $student = $adviser->advisedStudents->first();
                                @endphp
                                Grade {{ $student->grade_level }} - {{ $student->section }}
                            @else
                                <span class="text-gray-400 italic">No section assigned</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 uppercase tracking-wide">
                                Verified
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex gap-2 justify-end">
                                <a href="{{ route('advisers.edit', $adviser) }}" class="text-blue-600 hover:text-blue-800 text-xs font-bold uppercase tracking-wide">
                                    Update Record
                                </a>
                                <span class="text-gray-300">|</span>
                                <a href="{{ route('advisers.show', $adviser) }}" class="text-gray-600 hover:text-gray-800 text-xs font-bold uppercase tracking-wide">
                                    View Details
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-400 text-sm">
                            No advisers registered yet.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($advisers->hasPages())
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $advisers->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
