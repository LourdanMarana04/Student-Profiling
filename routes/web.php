<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QueryController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\FacultyController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\StudentSubmissionController;
use App\Http\Controllers\FacultyDashboardController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\StudentInteractionController;
use App\Models\Section;

// Authentication routes
require __DIR__.'/auth.php';

Route::get('/', function () {
    return auth()->check() ? redirect('/dashboard') : view('welcome');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        if (auth()->user()->isStudent()) {
            $student = \App\Models\Student::where('user_id', auth()->id())->first();

            if ($student) {
                return redirect()->route('students.show', $student);
            }
        }

        if (auth()->user()->isFaculty()) {
            $faculty = \App\Models\Faculty::where('user_id', auth()->id())->first();

            if ($faculty) {
                return redirect()->route('faculty.dashboard');
            }
        }

        return view('dashboard');
    })->name('dashboard');

    Route::get('/users', [UserController::class, 'index'])->name('users');
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::patch('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

    Route::get('/reports', [ReportsController::class, 'index'])->name('reports');
    Route::get('/reports/students', [ReportsController::class, 'students'])->name('reports.students');
    Route::get('/reports/students/export', [ReportsController::class, 'exportStudents'])->name('reports.students.export');
    Route::get('/reports/faculty', [ReportsController::class, 'faculty'])->name('reports.faculty');
    Route::get('/reports/faculty/export', [ReportsController::class, 'exportFaculty'])->name('reports.faculty.export');
    Route::get('/reports/departments', [ReportsController::class, 'departments'])->name('reports.departments');
    Route::get('/reports/departments/export', [ReportsController::class, 'exportDepartments'])->name('reports.departments.export');
    Route::get('/reports/courses', [ReportsController::class, 'courses'])->name('reports.courses');
    Route::get('/reports/at-risk', [ReportsController::class, 'atRisk'])->name('reports.at-risk');
    Route::get('/reports/profile-completeness', [ReportsController::class, 'profileCompleteness'])->name('reports.profile-completeness');
    Route::get('/reports/interventions', [ReportsController::class, 'interventions'])->name('reports.interventions');
    Route::get('/reports/student-timeline', [ReportsController::class, 'studentTimeline'])->name('reports.student-timeline');
    Route::get('/reports/faculty-profiling-view', [ReportsController::class, 'facultyProfilingView'])->name('reports.faculty-profiling-view');
    Route::get('/reports/admin-controls', [ReportsController::class, 'adminControls'])->name('reports.admin-controls');
    Route::patch('/reports/admin-controls', [ReportsController::class, 'updateAdminControls'])->name('reports.admin-controls.update');

    Route::get('/faculty/reports/at-risk', [ReportsController::class, 'facultyAtRisk'])->name('faculty.reports.at-risk');
    Route::get('/faculty/reports/profile-completeness', [ReportsController::class, 'facultyProfileCompleteness'])->name('faculty.reports.profile-completeness');
    Route::get('/faculty/reports/interventions', [ReportsController::class, 'facultyInterventions'])->name('faculty.reports.interventions');
    Route::get('/faculty/reports/student-timeline', [ReportsController::class, 'facultyStudentTimeline'])->name('faculty.reports.student-timeline');
    Route::get('/faculty/reports/profiling-view', [ReportsController::class, 'facultyProfilingInsights'])->name('faculty.reports.profiling-view');
    Route::get('/faculty/reports/section/{section}', [ReportsController::class, 'facultySectionProfiling'])
        ->name('faculty.reports.section');
    Route::get('/faculty/reports/section/{section}/export', [ReportsController::class, 'exportFacultySectionProfiling'])
        ->name('faculty.reports.section.export');

    Route::get('/admin/account-settings', function () {
        abort_unless(auth()->user()->isAdmin(), 403);

        return redirect()->route('profile.edit');
    })->name('admin.account-settings');
    Route::get('/admin/audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');
    Route::get('/admin/audit-settings', [\App\Http\Controllers\AuditSettingsController::class, 'index'])->name('admin.audit-settings');
    Route::patch('/admin/audit-settings', [\App\Http\Controllers\AuditSettingsController::class, 'update'])->name('admin.audit-settings.update');
    Route::get('/admin/submissions', [StudentSubmissionController::class, 'index'])->name('submissions.index');

    Route::get('/my-profile', [StudentController::class, 'myProfile'])->name('students.me');

    // Profile routes (needed by the user menu and profile update flows)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile/student', [ProfileController::class, 'updateStudentProfile'])->name('profile.student.update');
    Route::patch('/profile/student/photo', [ProfileController::class, 'updateStudentPhoto'])->name('profile.student.photo.update');
    Route::post('/profile/student/skills', [StudentSubmissionController::class, 'storeSkill'])->name('profile.student.skills.store');
    Route::post('/profile/student/activities', [StudentSubmissionController::class, 'storeActivity'])->name('profile.student.activities.store');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::patch('/submissions/skills/{skill}', [StudentSubmissionController::class, 'reviewSkill'])->name('submissions.skills.review');
    Route::patch('/submissions/activities/{activity}', [StudentSubmissionController::class, 'reviewActivity'])->name('submissions.activities.review');

    // Student routes
    Route::resource('students', StudentController::class);
    Route::post('students/{student}/assign-curriculum', [StudentController::class, 'assignCurriculum'])->name('students.assign-curriculum');
    Route::post('students/{student}/interventions', [StudentController::class, 'storeIntervention'])->name('students.interventions.store');
    Route::patch('students/{student}/interventions/{intervention}', [StudentController::class, 'updateIntervention'])->name('students.interventions.update');
    Route::delete('students/{student}/interventions/{intervention}', [StudentController::class, 'destroyIntervention'])->name('students.interventions.destroy');
    Route::post('students/{student}/violations', [StudentController::class, 'storeViolation'])->name('students.violations.store');
    Route::patch('students/{student}/violations/{violation}', [StudentController::class, 'updateViolation'])->name('students.violations.update');
    Route::delete('students/{student}/violations/{violation}', [StudentController::class, 'destroyViolation'])->name('students.violations.destroy');
    Route::post('students/{student}/correction-requests', [StudentController::class, 'storeCorrectionRequest'])->name('students.correction-requests.store');

    // Faculty Dashboard and Features
    Route::middleware(['auth'])->group(function () {
        // Dashboard
        Route::get('/faculty/dashboard', [FacultyDashboardController::class, 'index'])
            ->name('faculty.dashboard');
        Route::get('/faculty/courses/{course}/roster-export', [FacultyDashboardController::class, 'exportCourseRoster'])
            ->name('faculty.courses.roster-export');

        // Attendance
        Route::get('faculty/attendance/record/{schedule}', [AttendanceController::class, 'recordAttendance'])
            ->name('faculty.attendance.record');
        Route::post('faculty/attendance/record/{schedule}', [AttendanceController::class, 'storeAttendance'])
            ->name('faculty.attendance.store');
        Route::get('faculty/attendance/view/{section}', [AttendanceController::class, 'viewAttendance'])
            ->name('faculty.attendance.view');

        // Student Interaction
        Route::get('faculty/students/{student}', [StudentInteractionController::class, 'viewStudent'])
            ->name('faculty.students.view');
    });

    // Faculty routes
    Route::resource('faculties', FacultyController::class)->names('faculty');
    Route::get('faculties/{faculty}/export-cv', [FacultyController::class, 'exportCv'])->name('faculty.export-cv');
    Route::post('faculties/{faculty}/assign-course', [FacultyController::class, 'assignCourse'])->name('faculty.assign-course');
    Route::delete('faculties/{faculty}/remove-course', [FacultyController::class, 'removeCourse'])->name('faculty.remove-course');
    Route::patch('/faculty/alerts/subscription', [StudentController::class, 'updateAlertSubscription'])->name('faculty.alerts.subscription');

    // Query routes
    Route::get('/queries', [QueryController::class, 'index'])->name('queries.index');
    Route::get('/queries/search/skill', [QueryController::class, 'searchBySkill'])->name('queries.skill');
    Route::get('/queries/search/activity', [QueryController::class, 'searchByActivity'])->name('queries.activity');
    Route::get('/queries/search/advanced', [QueryController::class, 'advancedSearch'])->name('queries.advanced');
    Route::get('/api/skills', [QueryController::class, 'getSkills'])->name('api.skills');
    Route::get('/api/activities', [QueryController::class, 'getActivities'])->name('api.activities');
});
