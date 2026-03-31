<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Services\InterventionSuggestionService;

class AdviserController extends Controller
{
    protected InterventionSuggestionService $interventionSuggestionService;

    public function __construct(InterventionSuggestionService $interventionSuggestionService)
    {
        $this->interventionSuggestionService = $interventionSuggestionService;
    }

    public function index()
    {
        $advisers = User::whereHas('role', function ($query) {
            $query->where('name', 'adviser');
        })
        ->with(['role', 'advisedStudents'])
        ->orderBy('name')
        ->paginate(20);

        // Calculate statistics
        $totalAdvisers = User::whereHas('role', function ($query) {
            $query->where('name', 'adviser');
        })->where('status', 'active')->count();

        $adviserRole = \App\Models\Role::where('name', 'adviser')->first();
        
        $sectionsCovered = User::where('role_id', $adviserRole->id ?? 0)
            ->whereHas('advisedStudents')
            ->distinct('id')
            ->count();

        $reportsLogged = \App\Models\Incident::whereHas('reporter.role', function ($query) {
            $query->where('name', 'adviser');
        })->count();

        return view('advisers.index', compact('advisers', 'totalAdvisers', 'sectionsCovered', 'reportsLogged'));
    }

    public function create()
    {
        return view('advisers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'employee_id' => 'required|string|unique:users,employee_id|max:255',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
        ]);

        $adviserRole = \App\Models\Role::where('name', 'adviser')->first();

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'employee_id' => $validated['employee_id'],
            'password' => Hash::make($validated['password']),
            'role_id' => $adviserRole->id,
            'phone' => $validated['phone'] ?? null,
            'department' => $validated['department'] ?? null,
        ]);

        return redirect()->route('advisers.index')
            ->with('success', 'Adviser registered successfully.');
    }

    public function show(User $adviser)
    {
        $adviser->load(['role', 'advisedStudents', 'reportedIncidents']);

        return view('advisers.show', compact('adviser'));
    }

    public function edit(User $adviser)
    {
        return view('advisers.edit', compact('adviser'));
    }

    public function update(Request $request, User $adviser)
    {
        // Although fields are readonly in UI, validation is kept to ensure data integrity
        $validated = $request->validate([
            'grade_level' => 'nullable|string|max:255',
            'section' => 'nullable|string|max:255',
        ]);

        // Note: Personal info (name, email, etc) is read-only in UI so we skip updating it here 
        // to prevent accidental overwrite if form tampering occurs, or just focus on section updates.

        // Update Section/Grade handling if provided
        // This updates ALL students currently assigned to this adviser
        if ($request->has('grade_level') || $request->has('section')) {
            $updateStudents = [];
            if ($request->filled('grade_level')) {
                $updateStudents['grade_level'] = $request->grade_level;
            }
            if ($request->filled('section')) {
                $updateStudents['section'] = $request->section;
            }
            
            if (!empty($updateStudents) && $adviser->advisedStudents()->count() > 0) {
                $adviser->advisedStudents()->update($updateStudents);
            }
        }

        return redirect()->route('advisers.show', $adviser)
            ->with('success', 'Adviser section assignment updated successfully.');
    }

    public function destroy(User $adviser)
    {
        $adviser->delete();

        return redirect()->route('advisers.index')
            ->with('success', 'Adviser deleted successfully.');
    }

    // Adviser Portal Methods
    public function dashboard()
    {
        $adviser = Auth::user();
        
        // Get all advisees with their incident counts
        $advisees = \App\Models\Student::where('adviser_id', $adviser->id)
            ->withCount(['incidents'])
            ->get()
            ->map(function ($student) {
                // Count attendance records
                $tardyCount = \App\Models\AttendanceRecord::where('student_id', $student->id)
                    ->where('status', 'tardy')
                    ->count();
                
                $absentCount = \App\Models\AttendanceRecord::where('student_id', $student->id)
                    ->where('status', 'absent')
                    ->count();
                
                $student->tardy_count = $tardyCount;
                $student->absent_count = $absentCount;
                
                return $student;
            });

        // Calculate class totals
        $totalIncidents = $advisees->sum('incidents_count');
        $totalTardy = $advisees->sum('tardy_count');
        $totalAbsent = $advisees->sum('absent_count');
        $totalAdvisees = $advisees->count();

        $insights = $this->interventionSuggestionService->getDashboardInsights(3, [
            'grade_level' => $adviser->grade_level,
            'section' => $adviser->section,
        ]);

        return view('adviser.dashboard', array_merge(
            compact('advisees', 'totalIncidents', 'totalTardy', 'totalAbsent', 'totalAdvisees'),
            [
                'suggestions' => $insights['suggestions'],
                'suggestionsSource' => $insights['suggestionsSource'],
                'recentPlans' => $insights['recentPlans'],
            ]
        ));
    }

    public function showStudent(\App\Models\Student $student)
    {
        // Ensure the student belongs to the logged-in adviser
        if ($student->adviser_id !== Auth::id()) {
            abort(403, 'Unauthorized access to student records.');
        }

        // Load student with incidents and attendance
        $student->load(['incidents.category', 'incidents.clause']);
        
        $tardyCount = \App\Models\AttendanceRecord::where('student_id', $student->id)
            ->where('status', 'tardy')
            ->count();
        
        $absentCount = \App\Models\AttendanceRecord::where('student_id', $student->id)
            ->where('status', 'absent')
            ->count();

        // Get attendance records with details
        $attendanceRecords = \App\Models\AttendanceRecord::where('student_id', $student->id)
            ->whereIn('status', ['absent', 'tardy', 'excused'])
            ->with('recorder')
            ->orderBy('date', 'desc')
            ->get();

        return view('adviser.students.show', compact('student', 'tardyCount', 'absentCount', 'attendanceRecords'));
    }

    public function createStudent()
    {
        return view('adviser.students.create');
    }

    public function storeStudent(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'student_id' => 'required|string|unique:students,student_id|max:255',
            'email' => 'required|email|unique:students,email',
            'date_of_birth' => 'required|date',
            'gender' => 'required|in:male,female',
            'residential_address' => 'required|string',
            'boarding_address' => 'nullable|string',
            'guardian_name' => 'required|string|max:255',
            'guardian_contact' => 'required|string|max:255',
            'mother_name' => 'nullable|string|max:255',
            'father_name' => 'nullable|string|max:255',
        ]);

        $adviser = Auth::user();

        $student = \App\Models\Student::create([
            'first_name' => $validated['first_name'],
            'middle_name' => $validated['middle_name'],
            'last_name' => $validated['last_name'],
            'student_id' => $validated['student_id'],
            'email' => $validated['email'],
            'date_of_birth' => $validated['date_of_birth'],
            'gender' => $validated['gender'],
            'grade_level' => $adviser->grade_level,
            'section' => $adviser->section,
            'residential_address' => $validated['residential_address'],
            'boarding_address' => $validated['boarding_address'],
            'guardian_name' => $validated['guardian_name'],
            'guardian_contact' => $validated['guardian_contact'],
            'mother_name' => $validated['mother_name'],
            'father_name' => $validated['father_name'],
            'adviser_id' => Auth::id(),
        ]);

        return redirect()->route('adviser.dashboard')
            ->with('success', 'Student registered successfully.');
    }

    public function showProfile(\App\Models\Student $student)
    {
        // Ensure the student belongs to the logged-in adviser
        if ($student->adviser_id !== Auth::id()) {
            abort(403, 'Unauthorized access to student records.');
        }

        return view('adviser.students.profile', compact('student'));
    }

    public function editStudent(\App\Models\Student $student)
    {
        // Ensure the student belongs to the logged-in adviser
        if ($student->adviser_id !== Auth::id()) {
            abort(403, 'Unauthorized access to student records.');
        }

        return view('adviser.students.edit', compact('student'));
    }

    public function updateStudent(Request $request, \App\Models\Student $student)
    {
        // Ensure the student belongs to the logged-in adviser
        if ($student->adviser_id !== Auth::id()) {
            abort(403, 'Unauthorized access to student records.');
        }

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'student_id' => 'required|string|max:255|unique:students,student_id,' . $student->id,
            'email' => 'nullable|email|unique:students,email,' . $student->id,
            'date_of_birth' => 'nullable|date',
            'gender' => 'required|in:male,female',
            'residential_address' => 'nullable|string',
            'boarding_address' => 'nullable|string',
            'guardian_name' => 'nullable|string|max:255',
            'guardian_contact' => 'nullable|string|max:255',
            'mother_name' => 'nullable|string|max:255',
            'father_name' => 'nullable|string|max:255',
        ]);

        $student->update([
            'first_name' => $validated['first_name'],
            'middle_name' => $validated['middle_name'],
            'last_name' => $validated['last_name'],
            'student_id' => $validated['student_id'],
            'email' => $validated['email'],
            'date_of_birth' => $validated['date_of_birth'],
            'gender' => $validated['gender'],
            'residential_address' => $validated['residential_address'],
            'boarding_address' => $validated['boarding_address'],
            'guardian_name' => $validated['guardian_name'],
            'guardian_contact' => $validated['guardian_contact'],
            'mother_name' => $validated['mother_name'],
            'father_name' => $validated['father_name'],
        ]);

        return redirect()->route('adviser.students.profile', $student)
            ->with('success', 'Student profile updated successfully.');
    }
}

