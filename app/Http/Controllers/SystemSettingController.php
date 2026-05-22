<?php

namespace App\Http\Controllers;

use App\Models\SystemSetting;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class SystemSettingController extends Controller
{
    /**
     * Tampilkan halaman pengaturan sistem.
     */
    public function index()
    {
        return view('admin.settings');
    }

    /**
     * Simpan pengaturan konfigurasi institusi dan sistem.
     */
    public function update(Request $request)
    {
        $request->validate([
            'institution_name' => ['required', 'string', 'max:255'],
            'rector_name' => ['required', 'string', 'max:255'],
            'rector_nip' => ['required', 'string', 'max:100'],
            'dean_name' => ['required', 'string', 'max:255'],
            'dean_nip' => ['required', 'string', 'max:100'],
            'log_retention_days' => ['required', 'integer', 'min:7', 'max:365'],
            'logo' => ['nullable', 'image', 'mimes:png,jpg,jpeg', 'max:2048'], // Max 2MB
        ], [
            'institution_name.required' => 'Nama institusi wajib diisi.',
            'rector_name.required' => 'Nama rektor wajib diisi.',
            'rector_nip.required' => 'NIP rektor wajib diisi.',
            'dean_name.required' => 'Nama dekan wajib diisi.',
            'dean_nip.required' => 'NIP dekan wajib diisi.',
            'log_retention_days.required' => 'Batas hari retensi log wajib diisi.',
            'log_retention_days.min' => 'Batas hari retensi minimal adalah 7 hari.',
            'log_retention_days.max' => 'Batas hari retensi maksimal adalah 365 hari.',
            'logo.image' => 'File logo harus berupa gambar.',
            'logo.mimes' => 'Format logo harus PNG, JPG, atau JPEG.',
            'logo.max' => 'Ukuran logo maksimal adalah 2MB.',
        ]);

        try {
            // Simpan data teks ke database
            SystemSetting::set('institution_name', $request->institution_name);
            SystemSetting::set('rector_name', $request->rector_name);
            SystemSetting::set('rector_nip', $request->rector_nip);
            SystemSetting::set('dean_name', $request->dean_name);
            SystemSetting::set('dean_nip', $request->dean_nip);
            SystemSetting::set('log_retention_days', $request->log_retention_days);

            // Simpan pengunggahan berkas logo jika ada
            if ($request->hasFile('logo')) {
                $file = $request->file('logo');
                $fileName = 'logo_univ_'.time().'.'.$file->getClientOriginalExtension();

                $destinationPath = public_path('images');
                if (! File::exists($destinationPath)) {
                    File::makeDirectory($destinationPath, 0755, true);
                }

                // Hapus logo lama jika ada
                $oldLogo = SystemSetting::get('logo_path');
                if ($oldLogo && File::exists(public_path($oldLogo))) {
                    File::delete(public_path($oldLogo));
                }

                $file->move($destinationPath, $fileName);
                SystemSetting::set('logo_path', 'images/'.$fileName);
            }

            // Catat ke log audit
            AuditLogService::log('UPDATE_SETTINGS', 'Memperbarui konfigurasi institusi akademik dan batas retensi log sistem.');

            return back()->with('success', 'Konfigurasi pengaturan sistem berhasil diperbarui!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memperbarui pengaturan: '.$e->getMessage());
        }
    }
}
