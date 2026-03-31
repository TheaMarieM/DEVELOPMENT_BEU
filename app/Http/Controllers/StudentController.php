<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\ParentModel;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class StudentController extends Controller
{
    public function index(): \Illuminate\View\View
    {
        $user = Auth::user();
        $students = Student::with(['adviser', 'parents'])
            ->when(
                $user && $user->role && $user->role->name === 'adviser',
                function ($query) use ($user) {
                    return $query->where('adviser_id', $user->id);
                }
            )
            ->orderBy('last_name')
            ->paginate(20);

        // Calculate statistics
        $totalEnrolled = Student::where('status', 'active')->count();
        
        $juniorHighDept = Student::where('status', 'active')
            ->where('grade_level', 'LIKE', '%')
            ->count();
        
        $atRiskAbsences = Student::where('status', 'active')
            ->withCount([
                'attendanceRecords as absent_count' => function ($query) {
                    $query->where('status', 'absent')
                        ->whereYear('date', now()->year);
                }
            ])
            ->having('absent_count', '>=', 10)
            ->count();
        
        $activeInterventions = \App\Models\InterventionSuggestion::where('status', 'active')->count();

        return view('students.index', compact('students', 'totalEnrolled', 'juniorHighDept', 'atRiskAbsences', 'activeInterventions'));
    }

    public function create()
    {
        $advisers = User::whereHas('role', function ($query) {
            $query->where('name', 'adviser');
        })->orderBy('name')->get();

        return view('students.create', compact('advisers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|string|unique:students,student_id|max:255',
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'date_of_birth' => 'required|date',
            'gender' => 'required|in:male,female',
            'grade_level' => 'required|string|max:255',
            'section' => 'required|string|max:255',
            'adviser_id' => 'required|exists:users,id',
            'address' => 'nullable|string',
        ]);

        $student = Student::create($validated);

        return redirect()->route('students.show', $student)
            ->with('success', 'Student registered successfully.');
    }

    public function show(Student $student)
    {
        $student->load(['adviser', 'parents', 'incidents.category', 'attendanceRecords']);

        return view('students.show', compact('student'));
    }

    public function edit(Student $student)
    {
        $advisers = User::whereHas('role', function ($query) {
            $query->where('name', 'adviser');
        })->orderBy('name')->get();

        return view('students.edit', compact('student', 'advisers'));
    }

    public function update(Request $request, Student $student)
    {
        $validated = $request->validate([
            'student_id' => 'required|string|max:255|unique:students,student_id,' . $student->id,
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'date_of_birth' => 'required|date',
            'gender' => 'required|in:male,female',
            'grade_level' => 'required|string|max:255',
            'section' => 'required|string|max:255',
            'adviser_id' => 'required|exists:users,id',
            'address' => 'nullable|string',
            'status' => 'required|in:active,inactive,dropped',
        ]);

        $student->update($validated);

        return redirect()->route('students.show', $student)
            ->with('success', 'Student updated successfully.');
    }

    /**
     * Quick student lookup for dashboard search
     */
    public function search(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'query' => 'required|string|min:2',
        ]);

        $term = trim($validated['query']);
        $currentYear = now()->year;

        $students = Student::query()
            ->where('status', 'active')
            ->where(function ($query) use ($term) {
                $query->where('first_name', 'like', "%{$term}%")
                    ->orWhere('last_name', 'like', "%{$term}%")
                    ->orWhere('student_id', 'like', "%{$term}%");
            })
            ->withCount([
                'incidents as incidents_count' => function ($query) use ($currentYear) {
                    $query->whereYear('incident_date', $currentYear);
                },
                'attendanceRecords as absent_count' => function ($query) use ($currentYear) {
                    $query->where('status', 'absent')->whereYear('date', $currentYear);
                },
                'attendanceRecords as tardy_count' => function ($query) use ($currentYear) {
                    $query->where('status', 'tardy')->whereYear('date', $currentYear);
                },
            ])
            ->orderBy('last_name')
            ->limit(8)
            ->get();

        $results = $students->map(function (Student $student) {
            return [
                'id' => $student->id,
                'name' => $student->full_name,
                'student_id' => $student->student_id,
                'grade_level' => $student->grade_level,
                'section' => $student->section,
                'incidents' => $student->incidents_count ?? 0,
                'absent_days' => $student->absent_count ?? 0,
                'tardy_days' => $student->tardy_count ?? 0,
                'status' => $student->status,
                'routes' => [
                    'view' => route('students.show', $student),
                    'add_incident' => route('incidents.create', ['student_id' => $student->id]),
                    'add_attendance' => route('attendance.index', ['student_id' => $student->id]),
                    'export_record' => route('reports.student', ['student' => $student->id]),
                    'export_attendance' => route('reports.attendance', ['student' => $student->id]),
                ],
            ];
        });

        return response()->json([
            'data' => $results,
        ]);
    }
}
