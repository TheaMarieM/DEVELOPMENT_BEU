<?php

namespace App\Http\Controllers;

use App\Models\AttendanceRecord;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    /**
     * Display attendance management page with form and list
     */
    public function index(Request $request)
    {
        $query = AttendanceRecord::with(['student', 'recorder'])
            ->whereIn('status', ['absent', 'tardy', 'excused']);

        // Filter by student
        if ($request->filled('student_id')) {
            $query->where('student_id', $request->student_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('date', '<=', $request->date_to);
        }

        $attendanceRecords = $query->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        $students = Student::where('status', 'active')
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();

        $focusedStudent = $request->filled('student_id')
            ? $students->firstWhere('id', (int) $request->student_id)
            : null;

        return view('attendance.index', compact('attendanceRecords', 'students', 'focusedStudent'));
    }

    /**
     * Store new attendance record
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'status' => 'required|in:absent,tardy,excused',
            'date' => 'required|date',
            'time_in' => 'nullable|date_format:H:i',
            'remarks' => 'nullable|string|max:500',
        ]);

        $student = Student::findOrFail($validated['student_id']);

        // Use updateOrCreate to handle duplicate entries (same student, same date)
        $attendance = AttendanceRecord::updateOrCreate(
            [
                'student_id' => $student->id,
                'date' => $validated['date'],
            ],
            [
                'status' => $validated['status'],
                'time_in' => $validated['time_in'] ?? null,
                'remarks' => $validated['remarks'] ?? null,
                'recorded_by' => Auth::id(),
            ]
        );

        $statusText = ucfirst($validated['status']);
        $action = $attendance->wasRecentlyCreated ? 'logged' : 'updated';
        
        return redirect()->route('attendance.index')
            ->with('success', "Attendance {$action} successfully: {$student->full_name} marked as {$statusText} on {$validated['date']}");
    }

    /**
     * Delete attendance record
     */
    public function destroy(AttendanceRecord $attendance)
    {
        $studentName = $attendance->student->full_name;
        $attendance->delete();

        return redirect()->route('attendance.index')
            ->with('success', "Attendance record for {$studentName} has been deleted.");
    }
}
