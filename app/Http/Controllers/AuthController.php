<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

use App\Services\AuditLogService;

class AuthController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLogin()
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole(Auth::user());
        }

        return view('auth.login');
    }

    /**
     * Handle the authentication attempt.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ], [
            'username.required' => 'NIM / ID Akademik wajib diisi.',
            'password.required' => 'Kata sandi wajib diisi.',
        ]);

        // Attempt login using 'username' (which is NIM / ID Akademik)
        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            // Log successful login
            AuditLogService::log('LOGIN', 'Pengguna berhasil masuk ke sistem.');

            return $this->redirectBasedOnRole(Auth::user());
        }

        // Log failed login attempt
        AuditLogService::log('LOGIN_FAILED', "Percobaan masuk gagal menggunakan ID Akademik: {$request->username}.");

        throw ValidationException::withMessages([
            'username' => 'NIM / ID Akademik atau kata sandi yang Anda masukkan salah.',
        ]);
    }

    /**
     * Log the user out of the application.
     */
    public function logout(Request $request)
    {
        // Log logout before session is destroyed
        AuditLogService::log('LOGOUT', 'Pengguna keluar dari sistem.');

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    /**
     * Helper to redirect users based on their role.
     */
    protected function redirectBasedOnRole($user)
    {
        if ($user->role === 'admin') {
            return redirect()->intended('/admin/dashboard');
        }

        return redirect()->intended('/student/dashboard');
    }
}
