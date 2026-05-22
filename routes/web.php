<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CertificateTemplateController;
use App\Http\Controllers\CertificatePrintController;
use App\Http\Controllers\VerificationController;
use App\Http\Controllers\PrintHistoryController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\SystemSettingController;
use Illuminate\Support\Facades\Route;

// Guest Routes (Only accessible when not logged in)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

// Authenticated Routes (Require active login session)
Route::middleware('auth')->group(function () {
    
    // Logout Action
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Profile Settings Change Password
    Route::post('/profile/change-password', [ProfileController::class, 'changePassword'])->name('profile.change-password');

    // Admin Group Area (Role restricted)
    Route::middleware('role:admin')->prefix('admin')->group(function () {
        
        Route::get('/dashboard', function () {
            $totalStudents = \App\Models\User::where('role', 'mahasiswa')->count();
            $printedCertificates = \App\Models\Student::where('certificate_status', 'Sudah Cetak')->count();
            $queueCertificates = \App\Models\Student::where('certificate_status', 'Antrean Cetak')->count();
            
            // Dapatkan riwayat aktivitas / pencetakan ijazah terbaru
            $recentStudents = \App\Models\Student::with('user')->latest()->take(5)->get();

            return view('admin.dashboard', compact('totalStudents', 'printedCertificates', 'queueCertificates', $recentStudents ? 'recentStudents' : []));
        })->name('admin.dashboard');

        // Student CRUD, Import & Export Template
        Route::get('/students/template', [StudentController::class, 'downloadTemplate'])->name('students.template');
        Route::post('/students/import', [StudentController::class, 'import'])->name('students.import');
        Route::resource('students', StudentController::class)->except(['show']);

        // Certificate Templates Management
        Route::get('/templates', [CertificateTemplateController::class, 'index'])->name('admin.templates');
        Route::post('/templates', [CertificateTemplateController::class, 'store'])->name('admin.templates.store');
        Route::post('/templates/{template}/activate', [CertificateTemplateController::class, 'activate'])->name('admin.templates.activate');
        Route::post('/templates/{template}/config', [CertificateTemplateController::class, 'updateConfig'])->name('admin.templates.config');
        Route::delete('/templates/{template}', [CertificateTemplateController::class, 'destroy'])->name('admin.templates.destroy');

        // Certificate Printing Queue
        Route::get('/print', [CertificatePrintController::class, 'index'])->name('admin.print');
        Route::get('/certificates/preview/{student}', [CertificatePrintController::class, 'preview'])->name('admin.certificates.preview');
        Route::get('/certificates/print-batch', [CertificatePrintController::class, 'printBatch'])->name('admin.certificates.print-batch');
        Route::post('/certificates/update-status', [CertificatePrintController::class, 'updateStatus'])->name('admin.certificates.update-status');

        Route::get('/history', [PrintHistoryController::class, 'index'])->name('admin.history');

        Route::get('/logs', [ActivityLogController::class, 'index'])->name('admin.logs');
        Route::post('/logs/purge', [ActivityLogController::class, 'purge'])->name('admin.logs.purge');

        Route::get('/settings', [SystemSettingController::class, 'index'])->name('admin.settings');
        Route::post('/settings', [SystemSettingController::class, 'update'])->name('admin.settings.update');
    });

    // Student Group Area (Role restricted)
    Route::middleware('role:mahasiswa')->prefix('student')->group(function () {
        
        Route::get('/dashboard', function () {
            return view('student.dashboard');
        })->name('student.dashboard');

        Route::get('/preview', [CertificatePrintController::class, 'studentPreview'])->name('student.preview');

        Route::get('/verification', function () {
            return view('student.verification');
        })->name('student.verification');
    });

});

// Public Verification Route (Open for public scanning, no auth required)
Route::get('/verify/{nim}', [VerificationController::class, 'verify'])->name('verification.verify');

// Root Route Redirect (Automatically redirects to login or dashboard)
Route::get('/', function () {
    return redirect()->route('login');
});
