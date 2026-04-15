@extends('layouts.app')

@section('content')
<div class="h-full bg-slate-50 overflow-y-auto">
    <!-- Header -->
    <header class="bg-gradient-to-r from-green-800 to-green-700 border-b border-green-900 px-8 py-5 shadow-lg">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-xl font-black text-white">Log New Incident</h2>
                <p class="text-xs text-green-100 font-medium mt-1">Record a behavioral violation</p>
            </div>
            <a href="{{ route('dashboard') }}" class="text-white/80 hover:text-white text-sm font-semibold flex items-center gap-2 transition-colors">
                <i class="fa-solid fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>
    </header>

    <div class="p-8 max-w-5xl mx-auto pb-20 space-y-6">
        @if(!empty($selectedStudent))
            <div class="bg-emerald-50 border border-emerald-200 rounded-2xl px-6 py-4 flex flex-wrap items-center justify-between gap-4">
                <div>
                    <p class="text-[11px] font-bold text-emerald-600 uppercase tracking-widest">Logging for student</p>
                    <h3 class="text-xl font-black text-gray-900">{{ $selectedStudent->full_name }}</h3>
                    <p class="text-sm text-gray-600 mt-1">ID: {{ $selectedStudent->student_id }} • Grade {{ $selectedStudent->grade_level }} - {{ $selectedStudent->section }}</p>
                </div>
                <a href="{{ route('students.show', $selectedStudent) }}" class="text-sm font-bold text-emerald-700 hover:text-emerald-900 inline-flex items-center gap-1">
                    View student profile <i class="fa-solid fa-arrow-up-right-from-square text-xs"></i>
                </a>
            </div>
        @endif

        <form action="{{ route('incidents.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6" id="incidentForm" data-selected-student="{{ $selectedStudentId ?? '' }}">
            @csrf
            <section class="bg-gray-50 border border-gray-200 rounded-2xl p-6 mb-6">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Involved Parties -->
                    <div class="relative">
                        <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-2">Involved Parties</label>

                        <div class="bg-gray-50 p-3 rounded-lg border border-gray-200 mb-3">
                            <select id="studentSelectSimple" class="w-full px-3 py-2 border border-gray-200 rounded text-sm mb-2">
                                <option value="">Select a student...</option>
                                @foreach($students as $student)
                                    <option value="{{ $student->id }}">{{ $student->last_name }}, {{ $student->first_name }} (Grade {{ $student->grade_level }} - {{ $student->section }})</option>
                                @endforeach
                            </select>
                            <button type="button" onclick="addStudentSimple()" class="w-full px-4 py-2 bg-green-600 text-white rounded text-sm font-bold hover:bg-green-700">
                                <i class="fa-solid fa-plus mr-1"></i> Add Student
                            </button>
                        </div>

                        <div id="selectedStudentsList" class="space-y-2 mb-3"></div>
                        <div id="studentsInputContainer"></div>
                    </div>

                    <!-- Violation Type -->
                    <div>
                        <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-2">Violation Type (Standardized)</label>
                        <input type="hidden" name="is_custom_violation" id="quick-custom-flag" value="{{ old('is_custom_violation', 0) }}">
                        <p class="text-xs font-semibold text-gray-500">Select Violation Category</p>
                        <div class="grid grid-cols-2 gap-2">
                            @foreach($violationCategories->take(4) as $index => $category)
                                @php
                                    $firstClauseId = $category->clauses->first()?->id;
                                    $labelIndex = $index + 1;
                                @endphp
                                <label class="flex items-center gap-2 rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-700">
                                    <input type="checkbox"
                                           class="violation-category-choice rounded border-gray-300 text-green-600 focus:ring-green-500"
                                           data-clause-id="{{ $firstClauseId }}"
                                           data-category-id="{{ $category->id }}"
                                           data-category-name="{{ $category->name }}"
                                           data-display-label="Category {{ $labelIndex }} Violations">
                                    <span>Category {{ $labelIndex }}</span>
                                </label>
                            @endforeach
                        </div>
                        <div id="quick-violation-picker" class="mt-3 hidden">
                            <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-2">Select Violation</label>
                            <select name="violation_clause_id" id="quick-violation-select"
                                    class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-green-500 focus:border-transparent outline-none">
                                <option value="">Select violation...</option>
                            @foreach($violationCategories as $category)
                                @if($category->clauses->isNotEmpty())
                                    <optgroup label="{{ $category->name }}" data-category-id="{{ $category->id }}" data-original-label="{{ $category->name }}">
                                        @foreach($category->clauses as $clause)
                                            @php
                                                $displayDescription = ($category->name === 'Category 3 Violations' && $clause->clause_number === '28')
                                                    ? 'Leaving the school without a valid gate pass issued by the Principal or Assistant principal'
                                                    : $clause->description;
                                            @endphp
                                            <option value="{{ $clause->id }}"
                                                    data-category-id="{{ $category->id }}"
                                                    data-requires-count="{{ in_array($clause->description, ['Tardiness (accumulated)', 'Absenteeism (accumulated unexcused absences)'], true) ? '1' : '0' }}"
                                                    data-has-options="{{ $clause->options->isNotEmpty() ? '1' : '0' }}"
                                                    @if($category->name === 'Category 3 Violations' && $clause->clause_number === '28')
                                                        data-note="Clinic pass is not a valid gate pass. Pupil/student must secure a valid gate pass from the principal or assistant principal."
                                                    @endif>
                                                {{ $displayDescription }}
                                            </option>
                                        @endforeach
                                    </optgroup>
                                @endif
                            @endforeach
                            </select>
                            <div id="quick-custom-wrapper" class="mt-2 hidden">
                                <label class="flex items-center gap-2 text-[11px] text-gray-500">
                                    <input type="checkbox" id="quick-custom-toggle" class="rounded border-gray-300 text-green-600 focus:ring-green-500" disabled>
                                    Violation not on the list? Type it manually.
                                </label>
                                <div id="quick-custom-fields" class="mt-3 space-y-3 hidden">
                                    <div>
                                        <textarea name="custom_violation_description" rows="3" placeholder="Describe the violation..."
                                                  class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-green-500 focus:border-transparent outline-none">{{ old('custom_violation_description') }}</textarea>
                                    </div>
                                    <p class="text-[11px] text-gray-400">Custom entries stay under the selected category.</p>
                                </div>
                            </div>
                            <p id="quick-violation-note" class="mt-2 text-xs text-amber-700 bg-amber-50 border border-amber-200 rounded-md px-3 py-2 hidden"></p>
                            <div id="quick-violation-offense" class="mt-3 hidden">
                                <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-2">Offense Level</label>
                                <div class="space-y-2">
                                    <label class="flex items-center gap-2 text-sm text-gray-700">
                                        <input type="checkbox" name="offense_level[]" value="first" class="offense-choice rounded border-gray-300 text-green-600 focus:ring-green-500">
                                        First Offense
                                    </label>
                                    <label class="flex items-center gap-2 text-sm text-gray-700">
                                        <input type="checkbox" name="offense_level[]" value="second" class="offense-choice rounded border-gray-300 text-green-600 focus:ring-green-500">
                                        Second Offense
                                    </label>
                                    <label class="flex items-center gap-2 text-sm text-gray-700">
                                        <input type="checkbox" name="offense_level[]" value="third" class="offense-choice rounded border-gray-300 text-green-600 focus:ring-green-500">
                                        Third Offense
                                    </label>
                                    <label class="flex items-center gap-2 text-sm text-gray-700">
                                        <input type="checkbox" name="offense_level[]" value="fourth" class="offense-choice rounded border-gray-300 text-green-600 focus:ring-green-500">
                                        Fourth Offense
                                    </label>
                                    <label class="flex items-center gap-2 text-sm text-gray-700">
                                        <input type="checkbox" name="offense_level[]" value="fifth" class="offense-choice rounded border-gray-300 text-green-600 focus:ring-green-500">
                                        Fifth Offense
                                    </label>
                                </div>
                            </div>
                            <div id="quick-violation-count" class="mt-3 hidden">
                                <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-2">Number of times</label>
                                <input type="number" min="1" step="1" placeholder="Enter count"
                                       class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-green-500 focus:border-transparent outline-none">
                            </div>
                            <div id="quick-violation-suboption" class="mt-3 hidden">
                                <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-2">Select detail</label>
                                <select name="violation_clause_option_id" id="quick-violation-option"
                                        class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-green-500 focus:border-transparent outline-none">
                                    <option value="">Select detail...</option>
                                    @foreach($violationCategories as $category)
                                        @foreach($category->clauses as $clause)
                                            @foreach($clause->options as $option)
                                                <option value="{{ $option->id }}" data-clause-id="{{ $clause->id }}">
                                                    {{ $option->description }}
                                                </option>
                                            @endforeach
                                        @endforeach
                                    @endforeach
                            </select>
                            </div>
                        </div>
                        <input type="hidden" name="custom_violation_category_id" id="quick-custom-category-id" value="">
                    </div>
                </div>
            </section>

            <section class="bg-white border border-gray-200 rounded-2xl p-6 mb-6">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-2">Date</label>
                        <input type="date" name="incident_date" required value="{{ old('incident_date') }}"
                               class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-green-500 focus:border-transparent outline-none">
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-2">Time</label>
                        <input type="time" name="incident_time" required value="{{ old('incident_time') }}"
                               class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-green-500 focus:border-transparent outline-none">
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-2">Location</label>
                        <input type="text" name="location" placeholder="e.g., Canteen, Classroom 10A" required value="{{ old('location') }}"
                               class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-green-500 focus:border-transparent outline-none">
                    </div>
                </div>
            </section>

            <section class="bg-white border border-gray-200 rounded-2xl p-6 mb-6">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-[11px] font-bold text-red-500 uppercase tracking-wider mb-2">Mandatory Violation Summary</label>
                        <textarea name="description" rows="4" required
                                  class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-green-500 focus:border-transparent outline-none resize-none">{{ old('description') }}</textarea>
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-2">Narrative Report (Optional)</label>
                        <div class="border-2 border-dashed border-gray-200 rounded-lg p-6 text-center hover:border-green-500 transition-colors cursor-pointer" onclick="document.getElementById('narrative_file').click()">
                            <i class="fa-solid fa-cloud-arrow-up text-3xl text-gray-300 mb-2"></i>
                            <p class="text-xs text-gray-400 mb-1" id="file-name">Click to upload scanned narrative picture</p>
                            <input type="file" id="narrative_file" name="narrative_file" accept="image/*,.pdf" class="hidden" onchange="updateFileName(this)">
                        </div>
                    </div>
                </div>
            </section>

            <div class="flex justify-end">
                <button type="submit" class="bg-green-700 hover:bg-green-800 text-white px-8 py-3 rounded-lg text-sm font-bold transition-all shadow-md flex items-center gap-2">
                    <i class="fa-solid fa-file-circle-check"></i>
                    Process Incident Log
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    let selectedStudents = [];

    function addStudentSimple() {
        const select = document.getElementById('studentSelectSimple');
        const studentId = select.value;
        const studentText = select.options[select.selectedIndex].text;

        if (!studentId) {
            alert('Please select a student');
            return;
        }

        if (selectedStudents.includes(studentId)) {
            alert('Student already added');
            return;
        }

        selectedStudents.push(studentId);
        updateStudentsList();
        updateHiddenInputs();
        select.value = '';
    }

    function removeStudentSimple(studentId) {
        selectedStudents = selectedStudents.filter((id) => id !== studentId);
        updateStudentsList();
        updateHiddenInputs();
    }

    function updateStudentsList() {
        const container = document.getElementById('selectedStudentsList');
        const select = document.getElementById('studentSelectSimple');

        if (selectedStudents.length === 0) {
            container.innerHTML = '<div class="text-center py-4 border-2 border-dashed border-gray-100 rounded-lg"><p class="text-xs text-gray-400 italic">No students added yet</p></div>';
            return;
        }

        let html = '';
        selectedStudents.forEach((studentId) => {
            const option = select.querySelector(`option[value="${studentId}"]`);
            const studentName = option ? option.text : 'Unknown';

            html += `
                <div class="flex items-center justify-between bg-white border border-gray-200 rounded-lg px-3 py-2 shadow-sm">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold bg-green-100 text-green-700">
                            <i class="fa-solid fa-user-graduate"></i>
                        </div>
                        <div class="text-sm font-bold text-gray-800">${studentName}</div>
                    </div>
                    <button type="button" onclick="removeStudentSimple('${studentId}')" class="text-red-400 hover:text-red-600 transition-colors p-1">
                        <i class="fa-solid fa-times"></i>
                    </button>
                </div>
            `;
        });

        container.innerHTML = html;
    }

    function updateHiddenInputs() {
        const container = document.getElementById('studentsInputContainer');
        container.innerHTML = '';

        selectedStudents.forEach((studentId) => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'students[]';
            input.value = studentId;
            container.appendChild(input);
        });
    }

    function initSelectedStudent() {
        const form = document.getElementById('incidentForm');
        const preset = form?.dataset.selectedStudent;
        if (!preset) {
            return;
        }
        selectedStudents = [preset];
        updateStudentsList();
        updateHiddenInputs();
    }

    document.addEventListener('DOMContentLoaded', function () {
        initSelectedStudent();
        updateStudentsList();
        updateHiddenInputs();
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const toggle = document.getElementById('quick-custom-toggle');
        const select = document.getElementById('quick-violation-select');
        const fields = document.getElementById('quick-custom-fields');
        const flag = document.getElementById('quick-custom-flag');
        const categoryChoices = Array.from(document.querySelectorAll('.violation-category-choice'));
        const picker = document.getElementById('quick-violation-picker');
        const customWrapper = document.getElementById('quick-custom-wrapper');
        const optgroups = Array.from(select?.querySelectorAll('optgroup') || []);
        const customCategoryInput = document.getElementById('quick-custom-category-id');
        const countWrapper = document.getElementById('quick-violation-count');
        const optionWrapper = document.getElementById('quick-violation-suboption');
        const optionSelect = document.getElementById('quick-violation-option');
        const note = document.getElementById('quick-violation-note');
        const offenseWrapper = document.getElementById('quick-violation-offense');
        const offenseChoices = Array.from(offenseWrapper?.querySelectorAll('.offense-choice') || []);
        const offenseLevelOrder = {
            first: 1,
            second: 2,
            third: 3,
            fourth: 4,
            fifth: 5,
        };
        let activeCategoryId = null;

        function updateViolationNote() {
            if (!note || !select) {
                return;
            }
            const selectedOption = select.options[select.selectedIndex];
            const message = selectedOption?.getAttribute('data-note');
            if (message) {
                note.textContent = message;
                note.classList.remove('hidden');
            } else {
                note.textContent = '';
                note.classList.add('hidden');
            }
        }

        function getMaxOffenseForCategory(categoryId) {
            if (!categoryId) {
                return null;
            }
            const choice = categoryChoices.find((item) => item.getAttribute('data-category-id') === categoryId);
            const label = choice?.getAttribute('data-display-label') || choice?.getAttribute('data-category-name') || '';
            const match = label.match(/Category\s+(\d+)/i);
            const categoryNumber = match ? Number.parseInt(match[1], 10) : null;
            if (categoryNumber === 1 || categoryNumber === 2) {
                return 5;
            }
            if (categoryNumber === 3) {
                return 4;
            }
            if (categoryNumber === 4) {
                return 2;
            }
            return null;
        }

        function updateOffenseChoices(categoryId) {
            const maxOffense = getMaxOffenseForCategory(categoryId);
            offenseChoices.forEach((input) => {
                const level = offenseLevelOrder[input.value] || 0;
                const label = input.closest('label');
                const allowed = !maxOffense || level <= maxOffense;
                if (label) {
                    label.classList.toggle('hidden', !allowed);
                }
                if (!allowed) {
                    input.checked = false;
                }
            });
        }

        function resetCategoryUI() {
            activeCategoryId = null;
            if (picker) {
                picker.classList.add('hidden');
            }
            if (customWrapper) {
                customWrapper.classList.add('hidden');
            }
            if (toggle) {
                toggle.checked = false;
                toggle.disabled = true;
            }
            if (fields) {
                fields.classList.add('hidden');
            }
            if (flag) {
                flag.value = '0';
            }
            if (select) {
                select.value = '';
                Array.from(select.options).forEach((option) => {
                    option.hidden = false;
                });
            }
            if (note) {
                note.textContent = '';
                note.classList.add('hidden');
            }
            if (offenseWrapper) {
                offenseWrapper.classList.add('hidden');
                offenseChoices.forEach((input) => {
                    input.checked = false;
                });
                updateOffenseChoices(null);
            }
            if (countWrapper) {
                countWrapper.classList.add('hidden');
            }
            if (optionWrapper) {
                optionWrapper.classList.add('hidden');
            }
            if (optionSelect) {
                optionSelect.value = '';
            }
            if (customCategoryInput) {
                customCategoryInput.value = '';
            }
            optgroups.forEach((group) => {
                group.hidden = false;
                const originalLabel = group.getAttribute('data-original-label');
                if (originalLabel) {
                    group.label = originalLabel;
                }
            });
        }

        function syncCustomState() {
            if (!toggle) {
                return;
            }
            if (!activeCategoryId) {
                resetCategoryUI();
                return;
            }
            if (toggle.checked) {
                if (fields) {
                    fields.classList.remove('hidden');
                }
                if (flag) {
                    flag.value = '1';
                }
                if (select) {
                    select.value = '';
                }
                updateViolationNote();
                if (offenseWrapper) {
                    offenseWrapper.classList.add('hidden');
                    offenseChoices.forEach((input) => {
                        input.checked = false;
                    });
                }
                if (optionWrapper) {
                    optionWrapper.classList.add('hidden');
                }
                if (optionSelect) {
                    optionSelect.value = '';
                }
            } else {
                if (fields) {
                    fields.classList.add('hidden');
                }
                if (flag) {
                    flag.value = '0';
                }
                if (picker) {
                    picker.classList.remove('hidden');
                }
                updateViolationNote();
            }
        }

        function handleCategoryChange(selected) {
            categoryChoices.forEach((choice) => {
                if (choice !== selected) {
                    choice.checked = false;
                }
            });

            if (selected.checked) {
                activeCategoryId = selected.getAttribute('data-category-id');
                if (customCategoryInput) {
                    customCategoryInput.value = activeCategoryId;
                }
                const options = Array.from(select.options);
                options.forEach((option) => {
                    if (!option.value) {
                        option.hidden = false;
                        return;
                    }
                    option.hidden = option.getAttribute('data-category-id') !== activeCategoryId;
                });
                optgroups.forEach((group) => {
                    const groupCategoryId = group.getAttribute('data-category-id');
                    const isActive = groupCategoryId === activeCategoryId;
                    group.hidden = !isActive;
                    if (isActive) {
                        const displayLabel = selected.getAttribute('data-display-label');
                        group.label = displayLabel || group.label;
                    }
                });
                if (picker) {
                    picker.classList.remove('hidden');
                }
                if (customWrapper) {
                    customWrapper.classList.remove('hidden');
                }
                if (toggle) {
                    toggle.disabled = false;
                }
                select.value = '';
                if (countWrapper) {
                    countWrapper.classList.add('hidden');
                }
                if (optionWrapper) {
                    optionWrapper.classList.add('hidden');
                }
                if (optionSelect) {
                    optionSelect.value = '';
                }
                if (customWrapper) {
                    customWrapper.classList.remove('hidden');
                }
                updateViolationNote();
                syncCustomState();
            } else {
                resetCategoryUI();
            }
        }

        select?.addEventListener('change', () => {
            const selectedOption = select.options[select.selectedIndex];
            const requiresCount = selectedOption?.getAttribute('data-requires-count') === '1';
            const hasOptions = selectedOption?.getAttribute('data-has-options') === '1';
            const hasSelection = Boolean(selectedOption?.value);
            const selectedCategoryId = selectedOption?.getAttribute('data-category-id');
            if (countWrapper) {
                countWrapper.classList.toggle('hidden', !requiresCount);
            }
            if (optionWrapper && optionSelect) {
                optionSelect.value = '';
                Array.from(optionSelect.options).forEach((option) => {
                    if (!option.value) {
                        option.hidden = false;
                        return;
                    }
                    option.hidden = option.getAttribute('data-clause-id') !== selectedOption?.value;
                });
                optionWrapper.classList.toggle('hidden', !hasOptions);
            }
            if (offenseWrapper) {
                if (hasSelection) {
                    updateOffenseChoices(selectedCategoryId);
                } else {
                    updateOffenseChoices(null);
                }
                offenseWrapper.classList.toggle('hidden', !hasSelection);
            }
            if (customWrapper) {
                customWrapper.classList.toggle('hidden', hasSelection);
            }
            updateViolationNote();
        });

        select?.addEventListener('focus', () => {
            if (!select) {
                return;
            }
            const selectedOption = select.options[select.selectedIndex];
            const hasSelection = Boolean(selectedOption?.value);
            if (customWrapper) {
                customWrapper.classList.toggle('hidden', hasSelection);
            }
        });

        categoryChoices.forEach((choice) => {
            choice.addEventListener('change', () => handleCategoryChange(choice));
        });

        offenseChoices.forEach((choice) => {
            choice.addEventListener('change', () => {
                if (!choice.checked) {
                    return;
                }
                offenseChoices.forEach((other) => {
                    if (other !== choice) {
                        other.checked = false;
                    }
                });
            });
        });

        toggle?.addEventListener('change', syncCustomState);
        resetCategoryUI();
        updateViolationNote();
    });
</script>
@endsection
