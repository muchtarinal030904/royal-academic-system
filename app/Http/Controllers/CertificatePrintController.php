<?php

namespace App\Http\Controllers;

use App\Models\CertificateTemplate;
use App\Models\PrintHistory;
use App\Models\Student;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CertificatePrintController extends Controller
{
    /**
     * Tampilkan daftar antrean mahasiswa untuk pencetakan ijazah.
     */
    public function index(Request $request)
    {
        $activeTemplate = CertificateTemplate::active()->first();

        // Mulai query mahasiswa lulus (status: Lulus)
        $query = Student::with('user')->where('status', 'Lulus');

        // Pencarian NIM atau Nama
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nim', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('name', 'like', "%{$search}%");
                    });
            });
        }

        // Penyaringan berdasarkan Program Studi
        if ($request->filled('major')) {
            $query->where('major', $request->major);
        }

        // Penyaringan berdasarkan Status Ijazah
        if ($request->filled('certificate_status')) {
            $query->where('certificate_status', $request->certificate_status);
        }

        $students = $query->latest()->paginate(15)->withQueryString();

        // List Program Studi untuk filter dropdown
        $majors = Student::distinct()->where('status', 'Lulus')->pluck('major');

        return view('admin.print', compact('students', 'majors', 'activeTemplate'));
    }

    /**
     * Tampilkan pratinjau ijazah tunggal untuk mahasiswa terpilih.
     */
    public function preview(Student $student)
    {
        $activeTemplate = CertificateTemplate::active()->first();

        if (! $activeTemplate) {
            return redirect()->route('admin.print')->with('error', 'Belum ada template ijazah aktif yang dikonfigurasi. Silakan aktifkan template terlebih dahulu.');
        }

        // Pastikan mahasiswa lulus
        if ($student->status !== 'Lulus') {
            return redirect()->route('admin.print')->with('error', 'Hanya mahasiswa dengan status kelulusan Lulus yang dapat diterbitkan ijazahnya.');
        }

        $students = collect([$student]);
        $isBatch = false;

        return view('admin.print_preview', compact('students', 'activeTemplate', 'isBatch'));
    }

    /**
     * Tampilkan halaman pratinjau pencetakan massal (Batch Print).
     */
    public function printBatch(Request $request)
    {
        $activeTemplate = CertificateTemplate::active()->first();

        if (! $activeTemplate) {
            return redirect()->route('admin.print')->with('error', 'Belum ada template ijazah aktif yang dikonfigurasi. Silakan aktifkan template terlebih dahulu.');
        }

        $idsString = $request->query('ids', '');
        if (empty($idsString)) {
            return redirect()->route('admin.print')->with('error', 'Silakan pilih minimal satu mahasiswa untuk dicetak massal.');
        }

        $studentIds = explode(',', $idsString);
        $students = Student::with('user')
            ->whereIn('id', $studentIds)
            ->where('status', 'Lulus')
            ->get();

        if ($students->isEmpty()) {
            return redirect()->route('admin.print')->with('error', 'Tidak ada data mahasiswa valid yang cocok untuk dicetak.');
        }

        $isBatch = true;

        return view('admin.print_preview', compact('students', 'activeTemplate', 'isBatch'));
    }

    /**
     * Perbarui status pencetakan ijazah menjadi "Sudah Cetak" setelah diproses.
     */
    public function updateStatus(Request $request)
    {
        $request->validate([
            'student_ids' => ['required', 'array'],
            'student_ids.*' => ['integer', 'exists:students,id'],
        ]);

        try {
            // Ambil data mahasiswa beserta user sebelum diubah untuk log audit
            $students = Student::with('user')->whereIn('id', $request->student_ids)->get();

            DB::transaction(function () use ($students, $request) {
                // Perbarui status pencetakan
                Student::whereIn('id', $request->student_ids)->update([
                    'certificate_status' => 'Sudah Cetak',
                ]);

                $user = Auth::user();
                $ip = $request->ip();
                $ua = $request->userAgent();

                // Simpan ke print_histories dan catat ke log audit aktivitas
                foreach ($students as $st) {
                    PrintHistory::create([
                        'student_id' => $st->id,
                        'user_id' => $user ? $user->id : null,
                        'certificate_number' => $st->certificate_number ?: 'BELUM TERBIT',
                        'printed_at' => now(),
                        'ip_address' => $ip,
                        'user_agent' => $ua,
                    ]);

                    AuditLogService::log(
                        'PRINT_CERTIFICATE',
                        'Mencetak ijazah resmi milik mahasiswa: '.($st->user->name ?? 'Mahasiswa')." (NIM: {$st->nim}, No: ".($st->certificate_number ?: '-').').'
                    );
                }
            });

            return response()->json([
                'success' => true,
                'message' => 'Berhasil memperbarui status pencetakan untuk '.$students->count().' ijazah mahasiswa!',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui status: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Menampilkan preview ijazah pribadi milik mahasiswa login secara aman.
     */
    public function studentPreview()
    {
        $user = Auth::user();
        $student = Student::with('user')->where('user_id', $user->id)->first();

        if (! $student) {
            abort(404, 'Data akademik mahasiswa Anda tidak ditemukan.');
        }

        // Cek status ijazah
        if ($student->certificate_status !== 'Sudah Cetak') {
            return redirect()->route('student.dashboard')->with('error', 'Ijazah Anda belum resmi dicetak oleh Biro Akademik.');
        }

        $activeTemplate = CertificateTemplate::active()->first();
        if (! $activeTemplate) {
            return redirect()->route('student.dashboard')->with('error', 'Sistem tidak mendeteksi template ijazah aktif. Silakan hubungi Biro Akademik.');
        }

        $students = collect([$student]);
        $isBatch = false;
        $isStudentView = true;

        return view('admin.print_preview', compact('students', 'activeTemplate', 'isBatch', 'isStudentView'));
    }
}
