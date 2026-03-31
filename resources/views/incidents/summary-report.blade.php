@extends('layouts.app')

@section('content')
<div class="py-12 bg-gray-100 min-h-screen">
    <div class="max-w-4xl mx-auto">
        
        <!-- Navigation -->
        <div class="mb-6 flex justify-between items-center px-4 md:px-0">
            <h2 class="text-xl font-bold text-gray-800">Create Summary Report</h2>
            <a href="{{ route('incidents.show', $incident) }}" class="text-sm text-gray-600 hover:text-gray-900">
                <i class="fa-solid fa-arrow-left mr-1"></i> Back to Incident
            </a>
        </div>

        <!-- Report Paper Container -->
        <form action="{{ route('incidents.submit-report', $incident) }}" method="POST" class="bg-white shadow-lg rounded-none md:rounded-lg overflow-hidden border border-gray-300">
            @csrf
            
            <div class="p-10 md:p-14 space-y-8">
                
                <!-- Header -->
                <div class="text-center border-b border-gray-200 pb-6">
                    <div class="flex justify-center mb-4">
                        <img src="{{ asset('images/spup_logo.png') }}" alt="SPUP Logo" class="w-20 h-20 object-contain">
                    </div>
                    <h1 class="font-serif text-xl md:text-2xl font-bold text-gray-900">St. Paul University Philippines</h1>
                    <p class="text-sm text-gray-600">Tuguegarao City, Cagayan 3500</p>
                    <div class="mt-4">
                        <p class="font-bold text-gray-800 tracking-widest text-sm">BASIC EDUCATION UNIT</p>
                        <p class="text-[10px] text-gray-500 uppercase tracking-wide">PAASCU Level III Accredited | ISO Certified</p>
                    </div>
                    <div class="mt-6">
                        <h2 class="font-bold text-gray-900 border-b-2 border-gray-900 inline-block pb-1 uppercase tracking-wider">Violation Report</h2>
                        <p class="text-xs text-gray-600 mt-1 font-semibold">S.Y. {{ date('Y') }} - {{ date('Y', strtotime('+1 year')) }}</p>
                    </div>
                </div>

                <!-- Student Info Grid -->
                <!-- Assuming Single Student Report for now, or listing first student if multiple -->
                <!-- If the requirement is strict on the screenshot layout, it implies a per-student report. 
                     However, for multi-student incidents, we might list all names. -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-y-4 gap-x-12">
                    <div class="space-y-4">
                        <div class="flex items-end border-b border-gray-300 pb-1">
                            <span class="text-xs font-bold text-gray-600 uppercase w-32 shrink-0">Name of Student:</span>
                            <span class="font-semibold text-gray-900 flex-1 text-sm">
                                {{ $incident->students->pluck('full_name')->join(', ') }}
                                @if($incident->non_student_participant)
                                    <span class="text-gray-500 text-xs"> (and {{ $incident->non_student_participant }})</span>
                                @endif
                            </span>
                        </div>
                        <div class="flex items-end border-b border-gray-300 pb-1">
                            <span class="text-xs font-bold text-gray-600 uppercase w-32 shrink-0">Grade & Section:</span>
                            <span class="font-semibold text-gray-900 flex-1 text-sm">
                                @if($incident->students->first())
                                    {{ $incident->students->first()->grade_level }} - {{ $incident->students->first()->section }}
                                    @if($incident->students->count() > 1) (Mixed) @endif
                                @else
                                    N/A
                                @endif
                            </span>
                        </div>
                    </div>
                    <div class="space-y-4">
                        <div class="flex items-end border-b border-gray-300 pb-1">
                            <span class="text-xs font-bold text-gray-600 uppercase w-24 shrink-0">Date:</span>
                            <span class="font-semibold text-gray-900 flex-1 text-sm">{{ $incident->incident_date->format('F d, Y') }}</span>
                        </div>
                        <div class="flex items-end border-b border-gray-300 pb-1">
                            <span class="text-xs font-bold text-gray-600 uppercase w-24 shrink-0">Time:</span>
                            <span class="font-semibold text-gray-900 flex-1 text-sm">{{ $incident->incident_date->format('h:i A') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Detailed Sections -->
                
                <!-- Nature of Violation -->
                <div class="bg-gray-50 p-4 rounded-sm border border-gray-200">
                    <label class="block text-xs font-bold text-gray-600 uppercase mb-3">Nature of Violation</label>
                    <div class="grid grid-cols-2 gap-2 text-sm">
                        <!-- Checkbox that is checked if category matches -->
                         <div class="flex items-center">
                            <input type="checkbox" disabled checked class="form-checkbox h-4 w-4 text-gray-800 rounded border-gray-300">
                            <span class="ml-2 font-medium text-gray-900">
                                {{ $incident->category ? $incident->category->name : 'Uncategorized' }}
                            </span>
                        </div>
                    </div>
                    
                    <!-- Offense Count Badge -->
                    <div class="mt-4 flex gap-2">
                         @php
                            // Get max offense count among students
                            $maxOffense = $incident->students->max('pivot.offense_count') ?? 1;
                            $offenseLabels = [1 => 'First Offense', 2 => 'Second Offense', 3 => 'Third Offense', 4 => 'Fourth Offense', 5 => 'Fifth Offense'];
                            $isEditable = in_array($incident->status, ['reported', 'under_review']);
                        @endphp
                        <input type="hidden" name="offense_count" id="offense_count" value="{{ $maxOffense }}">
                        <div class="inline-flex rounded-md shadow-sm" role="group">
                            @foreach($offenseLabels as $k => $label)
                                @if($isEditable)
                                    <button type="button" data-offense="{{ $k }}" id="offense-btn-{{ $k }}"
                                        class="offense-select-btn px-3 py-1 text-xs font-medium border border-gray-200 transition-all
                                        {{ $maxOffense == $k ? 'bg-red-50 text-red-700 border-red-200 z-10' : 'bg-white text-gray-500 hover:bg-gray-50' }}
                                        {{ $loop->first ? 'rounded-l-lg' : '' }}
                                        {{ $loop->last ? 'rounded-r-lg' : '' }}
                                        ">
                                        {{ $label }}
                                    </button>
                                @else
                                    <span id="offense-btn-{{ $k }}"
                                        class="px-3 py-1 text-xs font-medium border border-gray-200 cursor-not-allowed
                                        {{ $maxOffense == $k ? 'bg-red-50 text-red-700 border-red-200 z-10' : 'bg-gray-100 text-gray-400' }}
                                        {{ $loop->first ? 'rounded-l-lg' : '' }}
                                        {{ $loop->last ? 'rounded-r-lg' : '' }}
                                        ">
                                        {{ $label }}
                                    </span>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Deviation Clause -->
                <div>
                    <label class="block text-xs font-bold text-gray-600 uppercase mb-2">Deviation Clause: <i class="font-normal normal-case text-gray-400">(Student Handbook)</i></label>
                    <div class="relative">
                        <select name="violation_clause_id" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-sm text-sm focus:ring-1 focus:ring-gray-400 focus:border-gray-400 outline-none transition appearance-none text-gray-800 font-medium">
                            <option value="" disabled {{ !$incident->violation_clause_id ? 'selected' : '' }}>-- Select Deviation Clause --</option>
                            @forelse($clauses as $clause)
                                <option value="{{ $clause->id }}" {{ $incident->violation_clause_id == $clause->id ? 'selected' : '' }}>
                                    {{ $clause->description }}
                                </option>
                            @empty
                                <option value="" disabled>No deviation clauses available for this category</option>
                            @endforelse
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                            <i class="fa-solid fa-chevron-down text-xs"></i>
                        </div>
                    </div>
                    @if($incident->custom_violation_description)
                        <div class="mt-3 text-xs text-amber-700 bg-amber-50 border border-amber-100 rounded px-3 py-2">
                            <span class="font-bold uppercase tracking-wide">Custom Entry:</span>
                            <span class="font-medium">{{ $incident->custom_violation_description }}</span>
                        </div>
                    @endif
                    @if(!in_array($incident->status, ['reported', 'under_review']))
                        <script>document.getElementsByName('violation_clause_id')[0].disabled = true;</script>
                    @endif
                </div>

                <!-- Narration of Facts -->
                <div>
                    <label class="block text-xs font-bold text-gray-600 uppercase mb-2">Narration of Facts:</label>
                    <textarea name="description" rows="6" {{ !in_array($incident->status, ['reported', 'under_review']) ? 'readonly' : '' }}
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-sm text-sm focus:ring-1 focus:ring-gray-400 focus:border-gray-400 outline-none transition resize-none leading-relaxed text-gray-800 font-medium"
                        placeholder="Enter the detailed narration of facts here...">{{ $incident->description }}</textarea>
                    
                    @if(in_array($incident->status, ['reported', 'under_review']))
                    <p class="text-[10px] text-gray-400 mt-1 text-right">You can edit this description before submitting.</p>
                    @endif
                </div>

                <!-- Action Taken -->
                <div>
                    <label class="block text-xs font-bold text-gray-600 uppercase mb-2">Action/s Taken: <i class="font-normal normal-case text-gray-400">(Sanction/s stipulated in the Student Handbook)</i></label>
                    @php
                        $defaultAction = $incident->action_taken;
                        if (!$defaultAction) {
                            $actions = [];
                            foreach($incident->students as $student) {
                                $sancName = $student->pivot->sanction_id ? \App\Models\Sanction::find($student->pivot->sanction_id)->name : 'Pending Sanction Assignment';
                                $status = $student->pivot->sanction_complied ? '(Complied)' : '(Pending Compliance)';
                                $actions[] = $student->full_name . ': ' . $sancName . ' ' . $status;
                            }
                            if (count($actions) > 0) {
                                $defaultAction = implode("\n", $actions);
                            }
                        }
                    @endphp
                    <textarea name="action_taken" rows="4" {{ !in_array($incident->status, ['reported', 'under_review']) ? 'readonly' : '' }}
                        class="w-full px-4 py-3 bg-red-50 border border-red-100 rounded-sm text-sm font-semibold text-red-800 focus:ring-1 focus:ring-red-400 focus:border-red-400 outline-none transition resize-none leading-relaxed placeholder-red-300"
                        placeholder="Enter actions taken...">{{ $defaultAction }}</textarea>
                </div>

                <!-- Footer / Dates -->
                <div class="grid grid-cols-2 gap-12 pt-8 border-t border-gray-200 mt-8">
                    <div>
                        <label class="block text-[10px] font-bold text-gray-500 uppercase">Date of Implementation</label>
                        <p class="text-sm font-bold text-gray-900 mt-1">{{ date('F d, Y') }}</p>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-gray-500 uppercase">Duration of Sanction</label>
                        <p class="text-sm font-bold text-gray-900 mt-1">Refer to sanction memorandum</p>
                    </div>
                </div>

            </div>

            <!-- Submit Actions -->
            @if(in_array($incident->status, ['reported', 'under_review']))
            <div class="bg-gray-50 px-10 py-6 border-t border-gray-200 flex items-center justify-between">
                <p class="text-xs text-gray-500">
                    <i class="fa-solid fa-lock mr-1"></i> This report will be forwarded to the Principal's office.
                </p>
                <button type="submit" onclick="return confirm('Are you sure you want to submit this report to the Principal?')"
                    class="px-6 py-3 bg-green-700 hover:bg-green-800 text-white font-bold rounded shadow-sm hover:shadow transition flex items-center gap-2 text-sm uppercase tracking-wide">
                    <i class="fa-solid fa-file-check"></i> Submit to Principal & Asst. Principal
                </button>
            </div>
            @else
            <div class="bg-gray-50 px-10 py-6 border-t border-gray-200 flex items-center justify-between print:hidden">
                <p class="text-xs text-gray-500">
                    <i class="fa-solid fa-info-circle mr-1"></i> This report is currently <strong>{{ ucwords(str_replace('_', ' ', $incident->status)) }}</strong>.
                </p>
                <button type="button" onclick="window.print()"
                    class="px-6 py-3 bg-gray-700 hover:bg-gray-800 text-white font-bold rounded shadow-sm hover:shadow transition flex items-center gap-2 text-sm uppercase tracking-wide">
                    <i class="fa-solid fa-print"></i> Print Report
                </button>
            </div>
            @endif
        </form>

    </div>
</div>

<style>
    @media print {
        body * {
            visibility: hidden;
        }
        form, form * {
            visibility: visible;
        }
        form {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            margin: 0;
            padding: 0;
            box-shadow: none !important;
            border: none !important;
        }
        .print\:hidden {
            display: none !important;
        }
    }
</style>

<script>
    function selectOffense(offenseNumber) {
        // Update hidden input
        document.getElementById('offense_count').value = offenseNumber;
        
        // Update button styles
        for (let i = 1; i <= 5; i++) {
            const btn = document.getElementById('offense-btn-' + i);
            if (i === offenseNumber) {
                btn.classList.remove('bg-white', 'text-gray-500', 'hover:bg-gray-50');
                btn.classList.add('bg-red-50', 'text-red-700', 'border-red-200', 'z-10');
            } else {
                btn.classList.remove('bg-red-50', 'text-red-700', 'border-red-200', 'z-10');
                btn.classList.add('bg-white', 'text-gray-500', 'hover:bg-gray-50');
            }
        }
    }

    // Add event listeners to offense select buttons
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.offense-select-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const offenseNumber = parseInt(this.getAttribute('data-offense'));
                selectOffense(offenseNumber);
            });
        });
    });
</script>
@endsection