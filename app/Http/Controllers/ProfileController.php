<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

use App\Services\AuditLogService;

class ProfileController extends Controller
{
    /**
     * Mengubah kata sandi pengguna terautentikasi.
     */
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'new_password' => ['required', 'confirmed', Password::min(8)],
        ], [
            'current_password.required' => 'Kata sandi saat ini wajib diisi.',
            'current_password.current_password' => 'Kata sandi saat ini salah.',
            'new_password.required' => 'Kata sandi baru wajib diisi.',
            'new_password.confirmed' => 'Konfirmasi kata sandi baru tidak cocok.',
            'new_password.min' => 'Kata sandi baru minimal harus 8 karakter.',
        ]);

        $request->user()->update([
            'password' => Hash::make($request->new_password),
        ]);

        // Log password change event
        AuditLogService::log('CHANGE_PASSWORD', 'Pengguna berhasil memperbarui kata sandi akun.');

        return back()->with('success', 'Kata sandi Anda berhasil diperbarui!');
    }
}
