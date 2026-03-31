<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\IncidentController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\ParentController;
use App\Http\Controllers\ApprovalController;
use App\Http\Controllers\AdviserController;
use App\Http\Controllers\StudentPortalController;
use App\Http\Controllers\ParentPortalController;
use App\Http\Controllers\PrincipalDashboardController;
use App\Http\Controllers\InterventionSuggestionController;
use App\Http\Controllers\AttendanceController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// Show login credentials page
Route::get('/login-info', function () {
    return view('auth.login-info');
})->name('login.info');

// Disable registration
Route::get('/register', function () {
    return redirect()->route('login');
})->name('register');

Route::get('/', function () {
    return redirect()->route('login.info');
});

Route::get('/dashboard', function () {
    $user = Auth::user();
    
    if ($user && $user->role) {
        if ($user->role->name === 'student') {
            return redirect()->route('student.dashboard');
        }
        if ($user->role->name === 'parent') {
            return redirect()->route('parent.dashboard');
        }
        if (in_array($user->role->name, ['principal', 'assistant_principal'])) {
            return redirect()->route('principal.dashboard');
        }
        if ($user->role->name === 'adviser') {
            return redirect()->route('adviser.dashboard');
        }
    }
    
    return app(DashboardController::class)->index(request());
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Incident Management (with rate limiting and file validation)
    Route::resource('incidents', IncidentController::class)
        ->middleware(['throttle:60,1', 'validate.file']);
    Route::get('/incidents/{incident}/summary-report', [IncidentController::class, 'summaryReport'])
        ->name('incidents.summary-report')
        ->middleware('throttle:60,1');
    Route::post('/incidents/{incident}/submit-report', [IncidentController::class, 'submitReport'])
        ->name('incidents.submit-report')
        ->middleware('throttle:30,1');
    Route::post('/incidents/{incident}/approve', [IncidentController::class, 'approve'])
        ->name('incidents.approve')
        ->middleware('throttle:30,1');
    Route::post('/incidents/{incident}/reject', [IncidentController::class, 'reject'])
        ->name('incidents.reject')
        ->middleware('throttle:30,1');
    Route::post('/incidents/{incident}/toggle-notification', [IncidentController::class, 'toggleParentNotification'])
        ->name('incidents.toggle-notification')
        ->middleware('throttle:30,1');
    Route::post('/incidents/{incident}/students/{student}/toggle-sanction', [IncidentController::class, 'toggleSanctionCompliance'])
        ->name('incidents.toggle-sanction')
        ->middleware('throttle:30,1');
    Route::put('/incidents/{incident}/students/{student}/update-sanction', [IncidentController::class, 'updateStudentSanction'])
        ->name('incidents.update-student-sanction')
        ->middleware('throttle:30,1');
    Route::post('/incidents/{incident}/archive', [IncidentController::class, 'archive'])
        ->name('incidents.archive')
        ->middleware('throttle:30,1');
    
    // Attendance Management (with rate limiting)
    Route::get('/attendance', [AttendanceController::class, 'index'])
        ->name('attendance.index')
        ->middleware('throttle:60,1');
    Route::post('/attendance', [AttendanceController::class, 'store'])
        ->name('attendance.store')
        ->middleware('throttle:30,1');
    Route::delete('/attendance/{attendance}', [AttendanceController::class, 'destroy'])
        ->name('attendance.destroy')
        ->middleware('throttle:30,1');
    
    // Adviser Management (with rate limiting)
    Route::resource('advisers', AdviserController::class)
        ->middleware('throttle:60,1');
    
    // Student Management (with rate limiting)
    Route::get('/students/search', [StudentController::class, 'search'])
        ->name('students.search')
        ->middleware('throttle:60,1');
    Route::resource('students', StudentController::class)
        ->middleware('throttle:60,1');
    
    // Parent Management (with rate limiting)
    Route::resource('parents', ParentController::class)
        ->middleware('throttle:60,1');
    Route::post('/parents/{parent}/notify', [ParentController::class, 'notify'])
        ->name('parents.notify')
        ->middleware('throttle:30,1');
    
    // Approval Management (with rate limiting)
    Route::get('/approvals', [ApprovalController::class, 'index'])
        ->name('approvals.index')
        ->middleware('throttle:60,1');
    Route::post('/approvals/{approval}/process', [ApprovalController::class, 'process'])
        ->name('approvals.process')
        ->middleware('throttle:30,1');
});

// Student Portal Routes
Route::middleware(['auth', 'verified', 'role:student'])->prefix('student')->name('student.')->group(function () {
    Route::get('/dashboard', [StudentPortalController::class, 'dashboard'])->name('dashboard');
    Route::get('/profile', [StudentPortalController::class, 'profile'])->name('profile');
    // Route::get('/incidents', [StudentPortalController::class, 'incidents'])->name('incidents');
});

// Parent Portal Routes
Route::middleware(['auth', 'verified', 'role:parent'])->prefix('parent')->name('parent.')->group(function () {
    Route::get('/dashboard', [ParentPortalController::class, 'dashboard'])->name('dashboard');
    Route::get('/child/{student:student_id}', [ParentPortalController::class, 'viewChild'])->name('view-child');
    Route::get('/profile', [ParentPortalController::class, 'profile'])->name('profile');
});

// Principal / Assistant Principal Routes
Route::middleware(['auth', 'verified', 'role:principal,assistant_principal'])
    ->prefix('principal')
    ->name('principal.')
    ->group(function () {
        Route::get('/dashboard', [PrincipalDashboardController::class, 'dashboard'])->name('dashboard');
        Route::get('/archives', [PrincipalDashboardController::class, 'archives'])->name('archives');
        Route::get('/incidents/{incident}', [PrincipalDashboardController::class, 'show'])->name('incidents.show');
        Route::post('/incidents/{incident}/approve', [PrincipalDashboardController::class, 'approve'])->name('incidents.approve');
        Route::post('/incidents/{incident}/return', [PrincipalDashboardController::class, 'returnForRevision'])->name('incidents.return');
        Route::get('/incidents/{incident}/students/{student}/attachment', [PrincipalDashboardController::class, 'downloadAttachment'])
            ->name('incidents.attachment');
    });

// Adviser Routes
Route::middleware(['auth', 'verified', 'role:adviser'])
    ->prefix('adviser')
    ->name('adviser.')
    ->group(function () {
        Route::get('/dashboard', [AdviserController::class, 'dashboard'])->name('dashboard');
        Route::get('/register-student', [AdviserController::class, 'createStudent'])->name('students.create');
        Route::post('/students', [AdviserController::class, 'storeStudent'])->name('students.store');
        Route::get('/students/{student}', [AdviserController::class, 'showStudent'])->name('students.show');
        Route::get('/students/{student}/profile', [AdviserController::class, 'showProfile'])->name('students.profile');
        Route::get('/students/{student}/edit', [AdviserController::class, 'editStudent'])->name('students.edit');
        Route::put('/students/{student}', [AdviserController::class, 'updateStudent'])->name('students.update');
    });

// Intervention Suggestion Decisions
Route::middleware(['auth', 'verified', 'role:discipline_chair,principal,assistant_principal,adviser'])
    ->prefix('interventions')
    ->name('interventions.')
    ->group(function () {
        Route::post('/convert', [InterventionSuggestionController::class, 'convert'])
            ->name('convert')
            ->middleware('role:discipline_chair');
        Route::post('/{suggestion}/decide', [InterventionSuggestionController::class, 'decide'])->name('decide');
    });

// Analytics API Routes (for AJAX charts)
Route::middleware(['auth', 'verified', 'role:discipline_chair,principal,assistant_principal'])
    ->prefix('api/analytics')
    ->name('api.analytics.')
    ->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\AnalyticsController::class, 'dashboard'])->name('dashboard');
        Route::get('/overview', [\App\Http\Controllers\AnalyticsController::class, 'overview'])->name('overview');
        Route::get('/trends', [\App\Http\Controllers\AnalyticsController::class, 'trends'])->name('trends');
        Route::get('/categories', [\App\Http\Controllers\AnalyticsController::class, 'categories'])->name('categories');
        Route::get('/grade-levels', [\App\Http\Controllers\AnalyticsController::class, 'gradeLevels'])->name('grade-levels');
        Route::get('/sections', [\App\Http\Controllers\AnalyticsController::class, 'sections'])->name('sections');
        Route::get('/severity', [\App\Http\Controllers\AnalyticsController::class, 'severity'])->name('severity');
        Route::get('/top-offenders', [\App\Http\Controllers\AnalyticsController::class, 'topOffenders'])->name('top-offenders');
        Route::get('/comparative', [\App\Http\Controllers\AnalyticsController::class, 'comparative'])->name('comparative');
        Route::get('/performance', [\App\Http\Controllers\AnalyticsController::class, 'performance'])->name('performance');
        Route::post('/clear-cache', [\App\Http\Controllers\AnalyticsController::class, 'clearCache'])->name('clear-cache');
    });

// Activity Logs (Admin Only)
Route::middleware(['auth', 'verified', 'role:discipline_chair,principal'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/activity-logs', [\App\Http\Controllers\ActivityLogController::class, 'index'])->name('activity-logs');
        Route::get('/activity-logs/export', [\App\Http\Controllers\ActivityLogController::class, 'export'])->name('activity-logs.export');
        Route::get('/activity-logs/recent', [\App\Http\Controllers\ActivityLogController::class, 'recent'])->name('activity-logs.recent');
        Route::get('/activity-logs/{modelType}/{modelId}', [\App\Http\Controllers\ActivityLogController::class, 'forModel'])->name('activity-logs.model');
        Route::get('/analytics', [\App\Http\Controllers\AnalyticsController::class, 'index'])->name('analytics');
    });

// Bulk Actions Routes
Route::middleware(['auth', 'verified', 'role:discipline_chair,principal'])
    ->prefix('bulk')
    ->name('bulk.')
    ->group(function () {
        Route::post('/incidents/approve', [\App\Http\Controllers\BulkActionsController::class, 'approveIncidents'])->name('incidents.approve');
        Route::post('/incidents/archive', [\App\Http\Controllers\BulkActionsController::class, 'archiveIncidents'])->name('incidents.archive');
        Route::post('/incidents/export', [\App\Http\Controllers\BulkActionsController::class, 'exportIncidents'])->name('incidents.export');
    });

// Reports Routes (Printable Reports)
Route::middleware(['auth', 'verified', 'role:discipline_chair,principal,assistant_principal,adviser'])
    ->prefix('reports')
    ->name('reports.')
    ->group(function () {
        Route::get('/', [\App\Http\Controllers\ReportController::class, 'index'])->name('index');
        Route::get('/incident/{incident}', [\App\Http\Controllers\ReportController::class, 'incident'])->name('incident');
        Route::get('/student', [\App\Http\Controllers\ReportController::class, 'studentRecord'])->name('student');
        Route::get('/attendance', [\App\Http\Controllers\ReportController::class, 'attendanceRecord'])->name('attendance');
        Route::get('/monthly', [\App\Http\Controllers\ReportController::class, 'monthlySummary'])->name('monthly');
        Route::get('/quarterly', [\App\Http\Controllers\ReportController::class, 'quarterlyReport'])->name('quarterly');
    });

require __DIR__.'/auth.php';
