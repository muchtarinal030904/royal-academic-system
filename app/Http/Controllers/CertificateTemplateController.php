<?php

namespace App\Http\Controllers;

use App\Models\CertificateTemplate;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class CertificateTemplateController extends Controller
{
    /**
     * Tampilkan daftar template ijazah.
     */
    public function index()
    {
        $templates = CertificateTemplate::latest()->get();

        return view('admin.templates', compact('templates'));
    }

    /**
     * Simpan template ijazah baru beserta berkas gambarnya.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'background' => ['required', 'image', 'mimes:jpeg,png,jpg', 'max:5120'], // Max 5MB
        ], [
            'name.required' => 'Nama template wajib diisi.',
            'background.required' => 'File gambar template wajib diunggah.',
            'background.image' => 'File harus berupa gambar.',
            'background.mimes' => 'Format gambar harus JPG, JPEG, atau PNG.',
            'background.max' => 'Ukuran gambar maksimal adalah 5MB.',
        ]);

        try {
            $file = $request->file('background');
            $fileName = 'tpl_'.time().'_'.uniqid().'.'.$file->getClientOriginalExtension();

            // Simpan langsung ke public/templates agar mudah diakses asset()
            $destinationPath = public_path('templates');
            if (! File::exists($destinationPath)) {
                File::makeDirectory($destinationPath, 0755, true);
            }

            $file->move($destinationPath, $fileName);
            $backgroundPath = 'templates/'.$fileName;

            // Buat record baru
            $template = CertificateTemplate::create([
                'name' => $request->name,
                'background_path' => $backgroundPath,
                'is_active' => CertificateTemplate::count() === 0, // Otomatis aktif jika ini template pertama
            ]);

            // Log template creation
            AuditLogService::log('CREATE_TEMPLATE', "Berhasil mengunggah template ijazah baru: '{$template->name}'.");

            return back()->with('success', "Template '{$template->name}' berhasil ditambahkan!");
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengunggah template: '.$e->getMessage());
        }
    }

    /**
     * Aktifkan template ijazah tertentu dan nonaktifkan template lainnya.
     */
    public function activate(CertificateTemplate $template)
    {
        try {
            DB::transaction(function () use ($template) {
                // Nonaktifkan semua template
                CertificateTemplate::query()->update(['is_active' => false]);

                // Aktifkan template terpilih
                $template->update(['is_active' => true]);
            });

            // Log template activation
            AuditLogService::log('ACTIVATE_TEMPLATE', "Mengaktifkan template ijazah '{$template->name}' sebagai template utama.");

            return back()->with('success', "Template '{$template->name}' sekarang aktif sebagai template utama!");
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengaktifkan template: '.$e->getMessage());
        }
    }

    /**
     * Perbarui konfigurasi koordinat elemen teks template (Visual Editor Simpan).
     */
    public function updateConfig(Request $request, CertificateTemplate $template)
    {
        $request->validate([
            'fields_config' => ['required', 'array'],
        ]);

        try {
            $template->update([
                'fields_config' => $request->fields_config,
            ]);

            // Log template visual editor configuration update
            AuditLogService::log('UPDATE_TEMPLATE', "Memperbarui konfigurasi koordinat tata letak untuk template '{$template->name}'.");

            return response()->json([
                'success' => true,
                'message' => 'Konfigurasi tata letak elemen ijazah berhasil disimpan!',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan konfigurasi: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Hapus template ijazah beserta berkas fisiknya.
     */
    public function destroy(CertificateTemplate $template)
    {
        try {
            $filePath = public_path($template->background_path);

            // Hapus berkas fisik jika ada
            if (File::exists($filePath)) {
                File::delete($filePath);
            }

            $name = $template->name;
            $wasActive = $template->is_active;

            $template->delete();

            // Jika template yang dihapus adalah template aktif, aktifkan template lain yang tersisa secara acak/terbaru
            if ($wasActive && CertificateTemplate::count() > 0) {
                $nextTemplate = CertificateTemplate::latest()->first();
                $nextTemplate->update(['is_active' => true]);
            }

            // Log template deletion
            AuditLogService::log('DELETE_TEMPLATE', "Menghapus template '{$name}' secara permanen.");

            return back()->with('success', "Template '{$name}' berhasil dihapus secara permanen.");
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus template: '.$e->getMessage());
        }
    }
}
