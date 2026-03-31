@extends('layouts.principal')

@section('content')
@php
    $statusStyles = [
        'pending_approval' => ['label' => 'Pending Approval', 'pill' => 'bg-red-100 text-red-700 border border-red-200'],
        'approved' => ['label' => 'Approved', 'pill' => 'bg-green-100 text-green-700 border border-green-200'],
        'under_review' => ['label' => 'Returned for Revision', 'pill' => 'bg-yellow-100 text-yellow-700 border border-yellow-200'],
    ];
    $statusMeta = $statusStyles[$incident->status] ?? ['label' => ucfirst(str_replace('_', ' ', $incident->status)), 'pill' => 'bg-gray-100 text-gray-600 border border-gray-200'];
    $primaryStudent = $incident->students->first();
    $studentName = $primaryStudent->full_name ?? '—';
    $gradeSection = $primaryStudent ? "Grade {$primaryStudent->grade_level} - {$primaryStudent->section}" : '—';
    $incidentDate = optional($incident->incident_date);
    $displayCategories = $categoryLegend->pad(4, null);
    $offenseLabels = ['First offense', 'Second offense', 'Third offense', 'Fourth offense', 'Fifth offense'];
    $activeOffense = max(1, min($maxOffenseCount, count($offenseLabels)));
    $sanctionText = $incident->students->map(fn ($student) => $student->sanction_details->sanction_description ?? null)->filter()->unique()->implode(' | ');
    $sanctionText = $sanctionText ?: 'Sanction pending assignment by the Discipline Office.';
    $implementationDateText = optional(
        $incident->approvals
            ->where('status', 'approved')
            ->sortBy('approved_at')
            ->first()
    )?->approved_at?->format('F d, Y') ?? $incidentDate?->format('F d, Y') ?? 'To be scheduled';
    $durationText = 'Refer to sanction memorandum';
    $showReturnForm = old('remarks') || $errors->has('remarks');
@endphp

<header class="bg-white border-b border-gray-200 px-8 py-4 flex justify-between items-center sticky top-0 z-40 shadow-sm">
    <div>
        <div class="flex items-center gap-2">
            <span class="{{ $statusMeta['pill'] }} text-[10px] font-bold px-2 py-0.5 rounded border uppercase">{{ $statusMeta['label'] }}</span>
            <h2 class="text-lg font-bold text-gray-800">Reviewing Case {{ $incident->incident_number ?? '—' }}</h2>
        </div>
        <p class="text-xs text-gray-500 font-medium mt-0.5">
            Submitted by {{ $incident->reporter->name ?? 'Discipline Office' }} on {{ $incidentDate?->format('M d, Y') ?? '—' }}
        </p>
    </div>

    <div class="flex gap-3">
        <button class="px-4 py-2 border border-gray-300 bg-white text-gray-600 rounded-lg text-xs font-bold hover:bg-gray-50 transition-colors">
            <i class="fa-solid fa-file-pdf mr-1"></i> Export PDF
        </button>
        @if($incident->status === 'pending_approval')
            <form method="POST" action="{{ route('principal.incidents.approve', $incident) }}" class="flex">
                @csrf
                <input type="hidden" name="remarks" value="Approved by Principal">
                <button type="submit" class="px-4 py-2 bg-green-700 text-white rounded-lg text-xs font-bold shadow-sm hover:bg-green-800 transition-colors flex items-center gap-2">
                    <i class="fa-solid fa-check-double"></i> Approve & Close Case
                </button>
            </form>
        @endif
        @if($incident->status === 'approved')
            <span class="px-4 py-2 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-lg text-xs font-bold flex items-center gap-2">
                <i class="fa-solid fa-check-circle"></i> Case Closed
            </span>
        @endif
    </div>
</header>

<div class="p-8 max-w-4xl mx-auto">
    <div class="bg-white p-8 rounded-none md:rounded-lg border border-gray-300 card-shadow relative">
        <div class="absolute inset-0 flex items-center justify-center pointer-events-none opacity-[0.03]">
            <img src="https://upload.wikimedia.org/wikipedia/en/c/c8/St._Paul_University_Philippines_logo.png" class="w-96 grayscale" alt="Watermark">
        </div>

        <div class="text-center mb-8 relative z-10">
            <div class="flex justify-center mb-3">
                <img src="{{ asset('images/spup-logo.png') }}" alt="SPUP Logo" class="w-16 h-16 object-contain">
            </div>
            <h1 class="font-serif font-bold text-xl text-gray-900">St. Paul University Philippines</h1>
            <p class="font-serif text-sm text-gray-600">Tuguegarao City, Cagayan 3500</p>
            <div class="mt-4 border-b border-gray-800 w-16 mx-auto mb-2"></div>
            <h2 class="font-bold text-sm uppercase tracking-widest text-gray-800">Basic Education Unit</h2>
            <p class="text-[10px] font-bold text-gray-500">PAASCU LEVEL III ACCREDITED | ISO CERTIFIED</p>
            <h3 class="mt-6 font-bold text-lg underline decoration-2 underline-offset-4 uppercase">Violation Report</h3>
            <p class="text-xs font-bold text-gray-500 mt-1">S.Y. {{ now()->format('Y') }} - {{ now()->addYear()->format('Y') }}</p>
        </div>

        <div class="grid grid-cols-2 gap-x-8 gap-y-4 mb-6 relative z-10 text-sm border-b-2 border-gray-100 pb-6">
            <div class="flex items-end border-b border-gray-300 pb-1">
                <span class="font-bold text-gray-600 w-32 shrink-0">Name of Student:</span>
                <span class="font-medium text-gray-900 flex-1 ml-2">{{ $studentName }}</span>
            </div>
            <div class="flex items-end border-b border-gray-300 pb-1">
                <span class="font-bold text-gray-600 w-16 shrink-0">Date:</span>
                <span class="font-medium text-gray-900 flex-1 ml-2">{{ $incidentDate?->format('F d, Y') ?? '—' }}</span>
            </div>
            <div class="flex items-end border-b border-gray-300 pb-1">
                <span class="font-bold text-gray-600 w-32 shrink-0">Grade & Section:</span>
                <span class="font-medium text-gray-900 flex-1 ml-2">{{ $gradeSection }}</span>
            </div>
            <div class="flex items-end border-b border-gray-300 pb-1">
                <span class="font-bold text-gray-600 w-16 shrink-0">Time:</span>
                <span class="font-medium text-gray-900 flex-1 ml-2">{{ $incidentDate?->format('h:i A') ?? '—' }}</span>
            </div>
        </div>

        <div class="mb-6 relative z-10">
            <div class="flex items-start">
                <span class="font-bold text-gray-700 text-sm w-36 mt-1">Nature of Violation:</span>
                <div class="grid grid-cols-2 gap-x-8 gap-y-2">
                    @foreach($displayCategories as $index => $category)
                        <div class="flex items-center">
                            @php
                                $isChecked = $category && optional($incident->category)->id === $category->id;
                                $label = $category->name ?? 'Category ' . ($index + 1);
                                $description = $category->description ?? '—';
                            @endphp
                            <div class="custom-checkbox {{ $isChecked ? 'checked' : '' }}"></div>
                            <span class="text-sm {{ $isChecked ? 'font-bold text-gray-900' : 'text-gray-500' }}">{{ $label }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="mb-8 relative z-10 bg-gray-50 p-3 rounded border border-gray-200 flex justify-between items-center text-sm flex-wrap gap-2">
            @foreach($offenseLabels as $index => $label)
                <span class="{{ ($index + 1) === $activeOffense ? 'font-bold text-red-600 border-b-2 border-red-600' : 'text-gray-400' }}">{{ $label }}</span>
            @endforeach
        </div>

        <div class="space-y-6 relative z-10">
            <div>
                <label class="block font-bold text-sm text-gray-800 mb-1">Deviation Clause: <span class="font-normal text-gray-500 italic">(Student Handbook)</span></label>
                <div class="w-full p-3 bg-gray-50 border-b border-gray-300 text-sm text-gray-800 italic">
                    {{ $incident->custom_violation_description ?? $incident->clause->description ?? 'No clause has been mapped to this violation.' }}
                </div>
            </div>

            <div>
                <label class="block font-bold text-sm text-gray-800 mb-1">Narration of Facts:</label>
                <p class="text-[10px] text-gray-400 mb-2 italic">(Please see attached student's and teacher's narrative reports if available.)</p>
                <div class="w-full p-4 bg-white border border-gray-200 rounded text-sm text-gray-700 leading-relaxed shadow-inner h-32 overflow-y-auto">
                    {{ $incident->description ?? 'Discipline narrative not provided.' }}
                </div>
                <div class="mt-2 flex gap-2 flex-wrap">
                    @foreach($incident->students as $student)
                        @if($student->pivot->narrative_file_path)
                            <a href="{{ route('principal.incidents.attachment', [$incident, $student]) }}" class="text-[10px] bg-blue-50 text-blue-600 px-2 py-1 rounded border border-blue-100 flex items-center gap-1 hover:bg-blue-100">
                                <i class="fa-solid fa-paperclip"></i> {{ $student->full_name }} Attachment
                            </a>
                        @endif
                    @endforeach
                </div>
            </div>

            <div>
                <label class="block font-bold text-sm text-gray-800 mb-1">Action/s Taken: <span class="font-normal text-gray-500 italic">(Sanction/s stipulated in the Student Handbook)</span></label>
                <div class="w-full p-3 bg-red-50 border-l-4 border-red-500 text-sm text-red-900 font-medium">
                    {{ $sanctionText }}
                </div>
            </div>

            <div class="grid grid-cols-2 gap-8 pt-4">
                <div class="border-b border-gray-300 pb-1">
                    <span class="block text-[10px] font-bold text-gray-400 uppercase">Date of Implementation</span>
                    <span class="block text-sm font-bold text-gray-800">{{ $implementationDateText }}</span>
                </div>
                <div class="border-b border-gray-300 pb-1">
                    <span class="block text-[10px] font-bold text-gray-400 uppercase">Duration of the Sanction</span>
                    <span class="block text-sm font-bold text-gray-800">{{ $durationText }}</span>
                </div>
            </div>
        </div>

        <div class="mt-12 pt-8 border-t-2 border-gray-100 relative z-10">
            <div class="grid grid-cols-1 gap-y-8">
                <div class="col-span-2 pt-4">
                    <p class="text-[10px] font-bold text-gray-400 uppercase mb-2">Principal / Asst. Principal Action</p>
                    <div class="bg-gray-50 border border-gray-200 p-4 rounded-lg flex flex-wrap gap-3 justify-between items-center">
                        <div>
                            <p class="text-sm font-bold text-gray-700">{{ auth()->user()->name ?? 'Sr. Maria Principal, SPC' }}</p>
                            <p class="text-[10px] text-gray-400">Principal - Basic Education Unit</p>
                        </div>
                        <div class="flex gap-2 items-center">
                            @if($incident->status === 'pending_approval')
                                <button type="button" onclick="document.getElementById('return-form').classList.toggle('hidden')" class="bg-white border border-gray-300 text-gray-500 hover:text-red-600 hover:border-red-300 px-3 py-1.5 rounded text-[10px] font-bold uppercase transition-colors">
                                    Return for Revision
                                </button>
                            @endif
                            @if($incident->status === 'approved')
                                <div class="bg-emerald-100 text-emerald-800 border border-emerald-200 px-4 py-1.5 rounded text-[10px] font-bold uppercase flex items-center gap-2">
                                    <i class="fa-solid fa-check-circle"></i> Case Closed
                                </div>
                            @else
                                <div class="bg-yellow-100 text-yellow-800 border border-yellow-200 px-4 py-1.5 rounded text-[10px] font-bold uppercase flex items-center gap-2">
                                    <i class="fa-solid fa-hourglass-half"></i> {{ $statusMeta['label'] }}
                                </div>
                            @endif
                        </div>
                    </div>
                    @if($incident->status === 'pending_approval')
                        <form id="return-form" method="POST" action="{{ route('principal.incidents.return', $incident) }}" class="mt-4 {{ $showReturnForm ? '' : 'hidden' }}">
                            @csrf
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Return Remarks</label>
                            <textarea name="remarks" rows="3" class="w-full border border-gray-300 rounded-lg p-3 text-sm focus:ring-2 focus:ring-red-400" required>{{ old('remarks') }}</textarea>
                            @error('remarks')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                            <div class="mt-3 flex gap-3">
                                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg text-xs font-bold uppercase tracking-wider">Return Case</button>
                                <button type="button" onclick="document.getElementById('return-form').classList.add('hidden')" class="px-4 py-2 border border-gray-300 rounded-lg text-xs font-bold">Cancel</button>
                            </div>
                        </form>
                    @elseif($incident->status === 'approved')
                        <div class="mt-4 bg-emerald-50 border border-emerald-200 rounded-lg p-4">
                            <p class="text-sm text-emerald-800 font-semibold">This case has been closed and approved.</p>
                            <p class="text-xs text-emerald-600 mt-1">No further action is required. This record is archived for reference only.</p>
                        </div>
                    @endif
                </div>

                <div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase mb-2">Approval History</p>
                    <div class="space-y-3">
                        @forelse($incident->approvals->sortByDesc('approved_at') as $approval)
                            @php
                                $approverRole = $approval->approver?->role?->name ?? '';
                                $approverTitle = $approverRole === 'assistant_principal' ? 'Assistant Principal' : ($approverRole === 'principal' ? 'Principal' : 'System');
                            @endphp
                            <div class="border border-gray-200 rounded-lg px-4 py-3 flex justify-between items-center">
                                <div>
                                    <p class="text-sm font-semibold text-gray-800">{{ ucfirst($approval->status) }} by {{ $approverTitle }}</p>
                                    <p class="text-xs text-gray-500">{{ $approval->approver->name ?? 'System' }} · {{ optional($approval->approved_at)->format('M d, Y h:i A') }}</p>
                                    @if($approval->remarks)
                                        <p class="text-xs text-gray-600 mt-1">“{{ $approval->remarks }}”</p>
                                    @endif
                                </div>
                                <span class="text-[10px] font-bold uppercase {{ $approval->status === 'approved' ? 'text-green-600' : 'text-red-600' }}">{{ strtoupper($approval->status) }}</span>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500">No approval actions recorded yet.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
