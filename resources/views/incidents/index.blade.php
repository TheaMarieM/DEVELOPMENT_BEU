@extends('layouts.app')

@section('content')
<!-- Header -->
<header class="bg-white border-b border-gray-200 shadow-sm sticky top-0 z-40">
    <div class="px-8 py-4 flex flex-wrap items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <span class="w-12 h-12 rounded-2xl bg-green-50 text-green-700 border border-green-100 flex items-center justify-center">
                <i class="fa-solid fa-clipboard-list"></i>
            </span>
            <div>
                <h2 class="text-2xl font-black text-gray-900">Incident Management Logs</h2>
            </div>
        </div>
        <div class="flex flex-wrap items-center gap-3 text-xs font-semibold text-gray-500"></div>
    </div>
</header>

<div class="p-8" x-data="{ activeTab: 'log' }">
    
    <!-- Tabs Navigation -->
    <div class="flex flex-wrap gap-2 mb-6 bg-gray-50 border border-gray-200 rounded-2xl p-1">
        <button @click="activeTab = 'entry'" 
                :class="activeTab === 'entry' ? 'bg-white text-green-700 shadow-sm border border-green-200' : 'text-gray-500 hover:text-gray-700 border-transparent'"
                class="px-4 py-2 rounded-xl font-semibold text-sm transition flex items-center gap-2">
            <i class="fa-solid fa-pen-to-square"></i> New Incident
        </button>
        <button @click="activeTab = 'log'" 
                :class="activeTab === 'log' ? 'bg-white text-green-700 shadow-sm border border-green-200' : 'text-gray-500 hover:text-gray-700 border-transparent'"
                class="px-4 py-2 rounded-xl font-semibold text-sm transition flex items-center gap-2">
            <i class="fa-solid fa-table-list"></i> Master Log
        </button>
        <button @click="activeTab = 'archives'" 
                :class="activeTab === 'archives' ? 'bg-white text-green-700 shadow-sm border border-green-200' : 'text-gray-500 hover:text-gray-700 border-transparent'"
                class="px-4 py-2 rounded-xl font-semibold text-sm transition flex items-center gap-2">
            <i class="fa-solid fa-box-archive"></i> Archives
        </button>
    </div>

    <!-- New Incident Entry Form -->
    <div x-show="activeTab === 'entry'" class="bg-white rounded-xl border border-gray-200 card-shadow mb-8 p-8 animate-fade-in">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wider">New Incident Entry</h3>
        </div>

        <form action="{{ route('incidents.store') }}" method="POST" enctype="multipart/form-data" id="incidentForm">
            @csrf
            
            <section class="bg-gray-50 border border-gray-200 rounded-2xl p-6 mb-6" x-data="participantManager()">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Involved Parties -->
                <div class="relative">
                    <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-2">Involved Parties</label>

                    <!-- Simple Student Select -->
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

                    <!-- Selected Students List -->
                    <div id="selectedStudentsList" class="space-y-2 mb-3"></div>
                    
                    <!-- Hidden inputs container -->
                    <div id="studentsInputContainer"></div>
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
                        
                        // Check if already added
                        if (selectedStudents.includes(studentId)) {
                            alert('Student already added');
                            return;
                        }
                        
                        // Add to array
                        selectedStudents.push(studentId);
                        
                        // Update visual list
                        updateStudentsList();
                        
                        // Update hidden inputs
                        updateHiddenInputs();
                        
                        // Reset select
                        select.value = '';
                    }
                    
                    function removeStudentSimple(studentId) {
                        selectedStudents = selectedStudents.filter(id => id !== studentId);
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
                        selectedStudents.forEach(studentId => {
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
                        
                        selectedStudents.forEach(studentId => {
                            const input = document.createElement('input');
                            input.type = 'hidden';
                            input.name = 'students[]';
                            input.value = studentId;
                            container.appendChild(input);
                        });
                        
                        console.log('Hidden inputs updated. Student IDs:', selectedStudents);
                    }
                    
                    // Initialize
                    updateStudentsList();
                </script>

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
                                if (picker) {
                                    picker.classList.add('hidden');
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

            <section class="bg-white border border-gray-200 rounded-2xl p-6 mb-6">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Date -->
                    <div>
                           <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-2">Date</label>
                           <input type="date" name="incident_date" required value="{{ old('incident_date') }}"
                               class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-green-500 focus:border-transparent outline-none">
                    </div>

                    <!-- Time -->
                    <div>
                           <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-2">Time</label>
                           <input type="time" name="incident_time" required value="{{ old('incident_time') }}"
                               class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-green-500 focus:border-transparent outline-none">
                    </div>

                    <!-- Location -->
                    <div>
                        <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-2">Location</label>
                        <input type="text" name="location" placeholder="e.g., Canteen, Classroom 10A" required value="{{ old('location') }}"
                               class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-green-500 focus:border-transparent outline-none">
                    </div>
                </div>
            </section>

            <section class="bg-white border border-gray-200 rounded-2xl p-6 mb-6">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Mandatory Violation Summary -->
                    <div>
                        <label class="block text-[11px] font-bold text-red-500 uppercase tracking-wider mb-2">Mandatory Violation Summary</label>
                        <textarea name="description" rows="4" required
                                  class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-green-500 focus:border-transparent outline-none resize-none">{{ old('description') }}</textarea>
                    </div>

                    <!-- Narrative Report (Optional) -->
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

    <!-- Master Incident Log -->
    <div x-show="activeTab === 'log'" class="bg-white rounded-xl border border-gray-200 card-shadow overflow-hidden animate-fade-in" style="display: none;"
         x-data="bulkActions()">
        <div class="px-6 py-5 border-b border-gray-100 flex flex-col sm:flex-row justify-between sm:items-center gap-4">
            <div>
                <h3 class="font-bold text-gray-800">Master Incident Log</h3>
                <p class="text-xs text-gray-500 mt-1">Historical record organized by section and grade</p>
            </div>
            
            <form method="GET" class="flex flex-wrap gap-2">
                <!-- Grade Filter -->
                <select name="grade_level" onchange="this.form.submit()" class="px-3 py-2 border border-gray-200 rounded-lg text-xs font-medium focus:ring-1 focus:ring-green-500 outline-none bg-gray-50 text-gray-600">
                    <option value="">All Grades</option>
                    <option value="7" {{ request('grade_level') == '7' ? 'selected' : '' }}>Grade 7</option>
                    <option value="8" {{ request('grade_level') == '8' ? 'selected' : '' }}>Grade 8</option>
                    <option value="9" {{ request('grade_level') == '9' ? 'selected' : '' }}>Grade 9</option>
                    <option value="10" {{ request('grade_level') == '10' ? 'selected' : '' }}>Grade 10</option>
                    <option value="11" {{ request('grade_level') == '11' ? 'selected' : '' }}>Grade 11</option>
                    <option value="12" {{ request('grade_level') == '12' ? 'selected' : '' }}>Grade 12</option>
                </select>

                <!-- Section Filter -->
                <input type="text" name="section" value="{{ request('section') }}" placeholder="Filter Section..." 
                       class="px-3 py-2 border border-gray-200 rounded-lg text-xs font-medium focus:ring-1 focus:ring-green-500 outline-none bg-gray-50 w-32">
                
                <!-- Status Filter -->
                <select name="status" onchange="this.form.submit()" class="px-3 py-2 border border-gray-200 rounded-lg text-xs font-medium focus:ring-1 focus:ring-green-500 outline-none bg-gray-50 text-gray-600">
                    <option value="">All Statuses</option>
                    <option value="reported" {{ request('status') == 'reported' ? 'selected' : '' }}>Reported</option>
                    <option value="under_review" {{ request('status') == 'under_review' ? 'selected' : '' }}>For Revision</option>
                    <option value="pending_approval" {{ request('status') == 'pending_approval' ? 'selected' : '' }}>Pending Approval</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Closed</option>
                </select>
                
                @if(request('search')) <input type="hidden" name="search" value="{{ request('search') }}"> @endif
                
                <button type="submit" class="px-3 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg text-xs font-bold transition-colors">
                    Filter
                </button>
            </form>
        </div>

        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
            <form method="GET" class="relative w-full max-w-md">
                @if(request('grade_level')) <input type="hidden" name="grade_level" value="{{ request('grade_level') }}"> @endif
                @if(request('section')) <input type="hidden" name="section" value="{{ request('section') }}"> @endif
                @if(request('status')) <input type="hidden" name="status" value="{{ request('status') }}"> @endif
                
                <i class="fa-solid fa-magnifying-glass absolute left-3 top-2.5 text-gray-400 text-xs"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by student, case ID, or details..." 
                       class="pl-9 pr-4 py-2 border border-gray-200 rounded-lg text-xs focus:ring-1 focus:ring-green-500 outline-none w-full bg-white shadow-sm">
            </form>
            <div class="flex items-center gap-2 ml-4">
                <!-- Bulk Actions (shown when items selected) -->
                <div x-show="selectedIds.length > 0" class="flex items-center gap-2" x-cloak>
                    <span class="text-xs text-gray-500 font-medium" x-text="selectedIds.length + ' selected'"></span>
                    <button @click="bulkExport()" class="px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs font-bold transition-colors flex items-center gap-1">
                        <i class="fa-solid fa-download"></i> Export
                    </button>
                    <button @click="bulkArchive()" class="px-3 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg text-xs font-bold transition-colors flex items-center gap-1">
                        <i class="fa-solid fa-box-archive"></i> Archive
                    </button>
                    <button @click="clearSelection()" class="px-2 py-2 text-gray-400 hover:text-gray-600 text-xs">
                        <i class="fa-solid fa-times"></i>
                    </button>
                </div>
                <!-- Export Button -->
                <button @click="exportAll()" x-show="selectedIds.length === 0" class="px-4 py-2 border border-gray-200 rounded-lg text-xs font-bold text-gray-600 hover:bg-gray-50 flex items-center gap-2">
                    <i class="fa-solid fa-file-csv text-green-600"></i>
                    Export CSV
                </button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-4 py-4 w-10">
                            <input type="checkbox" @change="toggleAll($event)" class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                        </th>
                        <th class="px-6 py-4 text-[10px] font-bold uppercase text-gray-400 tracking-wider">Case ID</th>
                        <th class="px-6 py-4 text-[10px] font-bold uppercase text-gray-400 tracking-wider">Incident Detail</th>
                        <th class="px-6 py-4 text-[10px] font-bold uppercase text-gray-400 tracking-wider">Parties Involved</th>
                        <th class="px-6 py-4 text-[10px] font-bold uppercase text-gray-400 tracking-wider">Reporting Authority</th>
                        <th class="px-6 py-4 text-[10px] font-bold uppercase text-gray-400 tracking-wider">Workflow Status</th>
                        <th class="px-6 py-4 text-[10px] font-bold uppercase text-gray-400 tracking-wider">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                    @forelse($incidents as $incident)
                    <tr class="hover:bg-gray-50 transition-colors" :class="{ 'bg-green-50': selectedIds.includes({{ $incident->id }}) }">
                        <td class="px-4 py-4">
                            <input type="checkbox" value="{{ $incident->id }}" @change="toggleSelection({{ $incident->id }})" :checked="selectedIds.includes({{ $incident->id }})" class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                        </td>
                        <td class="px-6 py-4 text-gray-500 text-xs font-mono">#INC-{{ now()->year }}-{{ str_pad($incident->id, 3, '0', STR_PAD_LEFT) }}</td>
                        <td class="px-6 py-4">
                            <div class="font-semibold text-gray-800">{{ $incident->category->name ?? 'N/A' }}</div>
                            <div class="text-xs text-gray-500">
                                {{ $incident->incident_date->format('M d, Y') }} 
                                @if($incident->students->isNotEmpty())
                                    • Grade {{ $incident->students->first()->grade_level }}
                                @else
                                    • Non-Student
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 text-gray-700">
                            @if($incident->students->isNotEmpty())
                                {{ $incident->students->first()->full_name }}
                            @elseif($incident->non_student_participant)
                                {{ $incident->non_student_participant }} <span class="text-xs text-gray-400 italic">(External)</span>
                            @else
                                N/A
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-gray-700">{{ $incident->reporter->name ?? 'N/A' }}</div>
                            <div class="text-[10px] text-gray-400 uppercase tracking-wide">{{ $incident->reporter->role->name ?? 'N/A' }}</div>
                        </td>
                        <td class="px-6 py-4">
                            @if($incident->status === 'pending_approval')
                                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-100 uppercase">
                                    <i class="fa-solid fa-circle-notch fa-spin mr-1.5"></i> Submitted for Review
                                </span>
                            @elseif($incident->status === 'approved')
                                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[10px] font-bold bg-green-50 text-green-700 border border-green-100 uppercase">
                                    <i class="fa-solid fa-circle-check mr-1.5"></i> Done / Closed
                                </span>
                            @elseif($incident->status === 'closed')
                                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[10px] font-bold bg-gray-600 text-white border border-gray-700 uppercase">
                                    Closed
                                </span>
                            @elseif($incident->status === 'under_review')
                                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[10px] font-bold bg-yellow-50 text-yellow-700 border border-yellow-100 uppercase">
                                    <i class="fa-solid fa-rotate-left mr-1.5"></i> For Revision
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[10px] font-bold bg-gray-50 text-gray-700 border border-gray-100 uppercase">
                                    {{ $incident->status }}
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex gap-2">
                                <a href="{{ route('incidents.show', $incident) }}" class="px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white rounded text-[10px] font-bold uppercase transition-colors">
                                    Manage
                                </a>
                                @if($incident->status === 'approved')
                                <form action="{{ route('incidents.archive', $incident) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" onclick="return confirm('Archive this incident? It will be moved to closed status.')" class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded text-[10px] font-bold uppercase transition-colors">
                                        Archive
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-400 text-sm">
                            No incidents recorded yet.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($incidents->hasPages())
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $incidents->links() }}
        </div>
        @endif
    </div>

    <!-- Archives Section -->
    <div x-show="activeTab === 'archives'" class="bg-white rounded-xl border border-gray-200 card-shadow overflow-hidden animate-fade-in" style="display: none;">
        <div class="px-6 py-5 border-b border-gray-100">
            <h3 class="font-bold text-gray-800">Archived Cases</h3>
            <p class="text-xs text-gray-500 mt-1">Closed and archived incident records</p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4 text-[10px] font-bold uppercase text-gray-400 tracking-wider">Case ID</th>
                        <th class="px-6 py-4 text-[10px] font-bold uppercase text-gray-400 tracking-wider">Incident Detail</th>
                        <th class="px-6 py-4 text-[10px] font-bold uppercase text-gray-400 tracking-wider">Parties Involved</th>
                        <th class="px-6 py-4 text-[10px] font-bold uppercase text-gray-400 tracking-wider">Date Archived</th>
                        <th class="px-6 py-4 text-[10px] font-bold uppercase text-gray-400 tracking-wider">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                    @forelse($archivedIncidents ?? [] as $archived)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 text-gray-500 text-xs font-mono">#INC-{{ now()->year }}-{{ str_pad($archived->id, 3, '0', STR_PAD_LEFT) }}</td>
                        <td class="px-6 py-4">
                            <div class="font-semibold text-gray-800">{{ $archived->category->name ?? 'N/A' }}</div>
                            <div class="text-xs text-gray-500">{{ $archived->incident_date->format('M d, Y') }}</div>
                        </td>
                        <td class="px-6 py-4 text-gray-700">
                            @if($archived->students->isNotEmpty())
                                {{ $archived->students->first()->full_name }}
                            @elseif($archived->non_student_participant)
                                {{ $archived->non_student_participant }}
                            @else
                                N/A
                            @endif
                        </td>
                        <td class="px-6 py-4 text-gray-500 text-xs">{{ $archived->updated_at->format('M d, Y') }}</td>
                        <td class="px-6 py-4">
                            <a href="{{ route('incidents.show', $archived) }}" class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded text-[10px] font-bold uppercase transition-colors">
                                View
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-400 text-sm">
                            <i class="fa-solid fa-box-archive text-3xl mb-3 opacity-30"></i>
                            <p>No archived cases yet.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<script>
function participantManager() {
    return {
        isNonStudent: false,
        nonStudentName: '',
        participants: [],
        
        addParticipant() {
            if (this.isNonStudent) {
                const name = this.nonStudentName.trim();
                if (!name) return;
                
                if (this.participants.some(p => p.type === 'non-student' && p.name.toLowerCase() === name.toLowerCase())) {
                    alert('This participant is already added.');
                    return;
                }

                this.participants.push({
                    type: 'non-student',
                    id: null,
                    name: name,
                    detail: 'External Participant'
                });
                this.nonStudentName = '';
            } else {
                const select = this.$refs.studentSelect;
                const option = select.options[select.selectedIndex];
                const id = select.value;
                
                if (!id) return;
                
                if (this.participants.some(p => p.type === 'student' && p.id === id)) {
                    alert('This student is already added.');
                    return;
                }

                const name = option.getAttribute('data-name');
                const detail = option.getAttribute('data-detail');

                this.participants.push({
                    type: 'student',
                    id: id,
                    name: name,
                    detail: detail
                });
                select.value = "";
            }
        },
        
        removeParticipant(index) {
            this.participants.splice(index, 1);
        }
    }
}

function updateFileName(input) {
    const fileName = input.files[0]?.name;
    if (fileName) {
        document.getElementById('file-name').textContent = fileName;
    }
}

// Bulk Actions Manager
function bulkActions() {
    return {
        selectedIds: [],
        
        toggleSelection(id) {
            const index = this.selectedIds.indexOf(id);
            if (index === -1) {
                this.selectedIds.push(id);
            } else {
                this.selectedIds.splice(index, 1);
            }
        },
        
        toggleAll(event) {
            if (event.target.checked) {
                // Select all visible incidents
                this.selectedIds = Array.from(document.querySelectorAll('tbody input[type="checkbox"]'))
                    .map(cb => parseInt(cb.value));
            } else {
                this.selectedIds = [];
            }
        },
        
        clearSelection() {
            this.selectedIds = [];
            document.querySelectorAll('input[type="checkbox"]').forEach(cb => cb.checked = false);
        },
        
        async bulkExport() {
            if (this.selectedIds.length === 0) return;
            
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("bulk.incidents.export") }}';
            form.innerHTML = `<input type="hidden" name="_token" value="{{ csrf_token() }}">`;
            this.selectedIds.forEach(id => {
                form.innerHTML += `<input type="hidden" name="incident_ids[]" value="${id}">`;
            });
            document.body.appendChild(form);
            form.submit();
        },
        
        async bulkArchive() {
            if (this.selectedIds.length === 0) return;
            
            if (!confirm(`Are you sure you want to archive ${this.selectedIds.length} incident(s)?`)) {
                return;
            }
            
            try {
                const response = await fetch('{{ route("bulk.incidents.archive") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ incident_ids: this.selectedIds })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    alert(data.message);
                    window.location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            } catch (error) {
                alert('Failed to archive incidents: ' + error.message);
            }
        },
        
        exportAll() {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("bulk.incidents.export") }}';
            form.innerHTML = `<input type="hidden" name="_token" value="{{ csrf_token() }}">`;
            document.body.appendChild(form);
            form.submit();
        }
    }
}

</script>
@endsection
