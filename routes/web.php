<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\LocaleController;
use App\Livewire\Public\CertificateVerify;
use App\Livewire\Student\Dashboard as StudentDashboard;
use App\Livewire\Student\CourseCatalog;
use App\Livewire\Student\CourseDetail;
use App\Livewire\Student\MySchedule;
use App\Livewire\Student\MyAttendance;
use App\Livewire\Student\MyAcademic;
use App\Livewire\Student\MyCertificates;
use App\Livewire\Student\Profile as StudentProfile;
use App\Livewire\Student\ApplicationForm as StudentApplicationForm;
use App\Livewire\Student\PaymentPage as StudentPaymentPage;
use App\Livewire\Student\TalaqqiScheduler as StudentTalaqqiScheduler;
use App\Livewire\Teacher\Dashboard as TeacherDashboard;
use App\Livewire\Teacher\ClassSchedules;
use App\Livewire\Teacher\ClassAttendance;
use App\Livewire\Teacher\MarkEntry as TeacherMarkEntry;
use App\Livewire\Teacher\PlacementTestReview;
use App\Livewire\Teacher\TalaqqiSession as TeacherTalaqqiSession;
use App\Livewire\Teacher\StudentRoster;
use App\Livewire\Admin\Dashboard as AdminDashboard;
use App\Livewire\Admin\ApplicationReview as AdminApplicationReview;
use App\Livewire\Admin\PlacementTestManagement;
use App\Livewire\Admin\EnrollmentManagement;
use App\Livewire\Admin\PaymentManagement;
use App\Livewire\Admin\ScheduleManagement;
use App\Livewire\Admin\AttendanceReports;
use App\Livewire\Admin\AcademicOverview;
use App\Livewire\Admin\CertificateManagement;
use App\Livewire\Admin\CourseManagement;
use App\Livewire\Admin\Reports as AdminReports;
use App\Livewire\Admin\LecturerManagement as AdminLecturerManagement;
use App\Livewire\Admin\AdminManagement as AdminAdminManagement;

Route::get('/', function () {
    if (auth()->check()) {
        $user = auth()->user();
        if ($user->isAdmin()) {
            return redirect('/admin/dashboard');
        }
        if ($user->isTeacher()) {
            return redirect('/teacher/dashboard');
        }

        return redirect('/student/dashboard');
    }

    return redirect()->route('login');
})->name('home');

Route::middleware(['guest'])->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::get('/auth/google', [LoginController::class, 'redirectToGoogle'])->name('auth.google');
    Route::get('/auth/google/callback', [LoginController::class, 'handleGoogleCallback'])->name('auth.google.callback');
    Route::get('/auth/google/mock', [LoginController::class, 'handleGoogleCallbackMock'])->name('auth.google.callback.mock');
});
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
Route::post('/register', [RegisterController::class, 'register']);

Route::get('/locale/{locale}', [LocaleController::class, 'switch'])->name('locale.switch');

Route::get('/verify/certificate/{hash?}', CertificateVerify::class)->name('verify.certificate');

Route::middleware(['auth', 'role:student'])->prefix('student')->name('student.')->group(function () {
    Route::get('/dashboard', StudentDashboard::class)->name('dashboard');
    Route::get('/courses', CourseCatalog::class)->name('courses');
    Route::redirect('/my-courses', '/student/courses?tab=mine')->name('my-courses');
    Route::redirect('/applications', '/student/courses?tab=explore')->name('applications');
    Route::get('/course/{enrollment_id}', CourseDetail::class)->name('course.detail');
    Route::redirect('/payments', '/student/profile?tab=bayaran')->name('payments');
    Route::get('/schedule', MySchedule::class)->name('schedule');
    Route::get('/attendance', MyAttendance::class)->name('attendance');
    Route::get('/academic', MyAcademic::class)->name('academic');
    Route::get('/certificates', MyCertificates::class)->name('certificates');
    Route::get('/profile', StudentProfile::class)->name('profile');
    Route::get('/apply/{course_id}', StudentApplicationForm::class)->name('apply');
    Route::get('/pay/{payment_id}', StudentPaymentPage::class)->name('pay');
    Route::get('/talaqqi/book', StudentTalaqqiScheduler::class)->name('talaqqi.book');
});

Route::middleware(['auth', 'role:teacher'])->prefix('teacher')->name('teacher.')->group(function () {
    Route::get('/dashboard', TeacherDashboard::class)->name('dashboard');
    Route::get('/schedules', ClassSchedules::class)->name('schedules');
    Route::get('/attendance', ClassAttendance::class)->name('attendance');
    Route::get('/grades', TeacherMarkEntry::class)->name('grades');
    Route::get('/placement-tests', PlacementTestReview::class)->name('placement-tests');
    Route::get('/talaqqi/session', TeacherTalaqqiSession::class)->name('talaqqi.session');
    Route::get('/students', StudentRoster::class)->name('students');
});

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', AdminDashboard::class)->name('dashboard');
    Route::get('/applications', AdminApplicationReview::class)->name('applications');
    Route::get('/placement-tests', PlacementTestManagement::class)->name('placement-tests');
    Route::get('/enrollments', EnrollmentManagement::class)->name('enrollments');
    Route::get('/lecturers', AdminLecturerManagement::class)->name('lecturers');
    Route::get('/admins', AdminAdminManagement::class)->name('admins');
    Route::get('/payments', PaymentManagement::class)->name('payments');
    Route::get('/schedules', ScheduleManagement::class)->name('schedules');
    Route::get('/attendance', AttendanceReports::class)->name('attendance');
    Route::get('/academic', AcademicOverview::class)->name('academic');
    Route::get('/certificates', CertificateManagement::class)->name('certificates');
    Route::get('/courses', CourseManagement::class)->name('courses');
    Route::get('/reports', AdminReports::class)->name('reports');
});
