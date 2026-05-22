<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class StudentController extends Controller
{
    /**
     * Tampilkan daftar mahasiswa dengan fitur pencarian dan penyaringan.
     */
    public function index(Request $request)
    {
        $query = Student::with('user');

        // Pencarian berdasarkan NIM atau Nama
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nim', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('name', 'like', "%{$search}%");
                    });
            });
        }

        // Penyaringan berdasarkan Fakultas
        if ($request->filled('faculty')) {
            $query->where('faculty', $request->faculty);
        }

        // Penyaringan berdasarkan Program Studi
        if ($request->filled('major')) {
            $query->where('major', $request->major);
        }

        // Penyaringan berdasarkan Status Ijazah
        if ($request->filled('certificate_status')) {
            $query->where('certificate_status', $request->certificate_status);
        }

        // Dapatkan data dengan pagination (10 per halaman)
        $students = $query->latest()->paginate(10)->withQueryString();

        // Data filter untuk dropdown
        $faculties = Student::distinct()->pluck('faculty');
        $majors = Student::distinct()->pluck('major');

        return view('admin.students', compact('students', 'faculties', 'majors'));
    }

    /**
     * Tambahkan data mahasiswa baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nim' => ['required', 'string', 'unique:users,username', 'unique:students,nim', 'regex:/^[0-9]+$/'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'unique:users,email'],
            'major' => ['required', 'string', 'max:255'],
            'faculty' => ['required', 'string', 'max:255'],
            'admission_year' => ['required', 'integer', 'min:2000', 'max:'.date('Y')],
            'gpa' => ['required', 'numeric', 'between:0.00,4.00'],
            'status' => ['required', 'string', Rule::in(['Aktif', 'Lulus', 'Cuti'])],
            'certificate_status' => ['required', 'string', Rule::in(['Belum Cetak', 'Antrean Cetak', 'Sudah Cetak'])],
            'certificate_number' => ['nullable', 'string', 'unique:students,certificate_number'],
            'graduation_date' => ['nullable', 'date'],
            'degree' => ['nullable', 'string', 'max:255'],
        ], [
            'nim.required' => 'NIM wajib diisi.',
            'nim.unique' => 'NIM sudah terdaftar sebagai username pengguna lain.',
            'nim.regex' => 'NIM harus berupa angka.',
            'name.required' => 'Nama lengkap wajib diisi.',
            'gpa.between' => 'IPK harus di antara 0.00 dan 4.00.',
            'admission_year.integer' => 'Tahun masuk harus berupa angka.',
        ]);

        DB::transaction(function () use ($request) {
            // 1. Buat User akun login
            $user = User::create([
                'name' => $request->name,
                'username' => $request->nim,
                'email' => $request->email ?: $request->nim.'@royal.ac.id',
                'password' => Hash::make('password'), // default password
                'role' => 'mahasiswa',
            ]);

            // 2. Buat record Student akademik
            Student::create([
                'user_id' => $user->id,
                'nim' => $request->nim,
                'major' => $request->major,
                'faculty' => $request->faculty,
                'admission_year' => $request->admission_year,
                'gpa' => $request->gpa,
                'status' => $request->status,
                'certificate_status' => $request->certificate_status,
                'certificate_number' => $request->certificate_number,
                'graduation_date' => $request->graduation_date,
                'degree' => $request->degree,
            ]);
        });

        return back()->with('success', "Mahasiswa {$request->name} berhasil ditambahkan!");
    }

    /**
     * Ubah data mahasiswa yang sudah ada.
     */
    public function update(Request $request, Student $student)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', Rule::unique('users', 'email')->ignore($student->user_id)],
            'major' => ['required', 'string', 'max:255'],
            'faculty' => ['required', 'string', 'max:255'],
            'admission_year' => ['required', 'integer', 'min:2000', 'max:'.date('Y')],
            'gpa' => ['required', 'numeric', 'between:0.00,4.00'],
            'status' => ['required', 'string', Rule::in(['Aktif', 'Lulus', 'Cuti'])],
            'certificate_status' => ['required', 'string', Rule::in(['Belum Cetak', 'Antrean Cetak', 'Sudah Cetak'])],
            'certificate_number' => ['nullable', 'string', Rule::unique('students', 'certificate_number')->ignore($student->id)],
            'graduation_date' => ['nullable', 'date'],
            'degree' => ['nullable', 'string', 'max:255'],
        ]);

        DB::transaction(function () use ($request, $student) {
            // 1. Perbarui akun User terkait
            $student->user->update([
                'name' => $request->name,
                'email' => $request->email ?: $student->nim.'@royal.ac.id',
            ]);

            // 2. Perbarui detail akademik Student
            $student->update([
                'major' => $request->major,
                'faculty' => $request->faculty,
                'admission_year' => $request->admission_year,
                'gpa' => $request->gpa,
                'status' => $request->status,
                'certificate_status' => $request->certificate_status,
                'certificate_number' => $request->certificate_number,
                'graduation_date' => $request->graduation_date,
                'degree' => $request->degree,
            ]);
        });

        return back()->with('success', "Data mahasiswa {$request->name} berhasil diperbarui!");
    }

    /**
     * Hapus data mahasiswa secara permanen beserta user akunnya.
     */
    public function destroy(Student $student)
    {
        $name = $student->user->name;

        DB::transaction(function () use ($student) {
            // Menghapus user akan otomatis menghapus record student karena cascade constraints
            $student->user->delete();
        });

        return back()->with('success', "Mahasiswa {$name} beserta akunnya berhasil dihapus.");
    }

    /**
     * Impor data mahasiswa massal menggunakan CSV secara native.
     */
    public function import(Request $request)
    {
        $request->validate([
            'csv_file' => ['required', 'file', 'mimes:csv,txt'],
        ], [
            'csv_file.required' => 'Silakan pilih file CSV terlebih dahulu.',
            'csv_file.mimes' => 'Format file harus berupa CSV.',
        ]);

        $file = $request->file('csv_file');
        $handle = fopen($file->getRealPath(), 'r');

        if (! $handle) {
            return back()->with('error', 'Gagal membuka file CSV.');
        }

        // Ambil header kolom
        $header = fgetcsv($handle, 1000, ',');

        if (! $header) {
            fclose($handle);

            return back()->with('error', 'File CSV kosong.');
        }

        // Bersihkan spasi atau karakter aneh dari header
        $header = array_map(function ($h) {
            return strtolower(trim($h, "\xEF\xBB\xBF "));
        }, $header);

        // Header yang dibutuhkan: nim, nama, program_studi, fakultas, tahun_masuk, ipk, status_mahasiswa, status_ijazah, nomor_ijazah, tanggal_lulus, gelar
        $requiredHeaders = ['nim', 'nama', 'program_studi', 'fakultas', 'tahun_masuk', 'ipk'];

        foreach ($requiredHeaders as $req) {
            if (! in_array($req, $header)) {
                fclose($handle);

                return back()->with('error', "Format header CSV tidak valid. Kolom '{$req}' wajib ada.");
            }
        }

        $rowNumber = 1;
        $successCount = 0;
        $errors = [];

        DB::beginTransaction();

        try {
            while (($row = fgetcsv($handle, 1000, ',')) !== false) {
                $rowNumber++;

                // Lewati baris kosong
                if (count($row) === 1 && empty($row[0])) {
                    continue;
                }

                // Asosiasikan baris dengan header
                $data = array_combine($header, $row);

                if (! $data) {
                    $errors[] = "Baris {$rowNumber}: Jumlah kolom tidak cocok dengan header.";

                    continue;
                }

                // Ambil & rapikan input
                $nim = trim($data['nim'] ?? '');
                $name = trim($data['nama'] ?? '');
                $major = trim($data['program_studi'] ?? '');
                $faculty = trim($data['fakultas'] ?? '');
                $admissionYear = intval(trim($data['tahun_masuk'] ?? 0));
                $gpa = floatval(trim($data['ipk'] ?? 0));

                // Kolom opsional dengan default
                $status = trim($data['status_mahasiswa'] ?? 'Aktif') ?: 'Aktif';
                $certificateStatus = trim($data['status_ijazah'] ?? 'Belum Cetak') ?: 'Belum Cetak';
                $certificateNumber = trim($data['nomor_ijazah'] ?? '') ?: null;
                $graduationDate = trim($data['tanggal_lulus'] ?? '') ?: null;
                $degree = trim($data['gelar'] ?? '') ?: null;

                // VALIDASI BARIS
                if (empty($nim) || empty($name) || empty($major) || empty($faculty) || ! $admissionYear || ! $gpa) {
                    $errors[] = "Baris {$rowNumber}: Kolom wajib (NIM, Nama, Prodi, Fakultas, Tahun Masuk, IPK) tidak boleh kosong.";

                    continue;
                }

                if (! preg_match('/^[0-9]+$/', $nim)) {
                    $errors[] = "Baris {$rowNumber}: NIM '{$nim}' harus berupa angka.";

                    continue;
                }

                if ($gpa < 0 || $gpa > 4.00) {
                    $errors[] = "Baris {$rowNumber}: IPK '{$gpa}' harus berada di antara 0.00 dan 4.00.";

                    continue;
                }

                // Periksa keunikan NIM di DB
                if (User::where('username', $nim)->exists() || Student::where('nim', $nim)->exists()) {
                    $errors[] = "Baris {$rowNumber}: NIM '{$nim}' sudah terdaftar dalam sistem.";

                    continue;
                }

                // Periksa keunikan nomor ijazah jika diisi
                if ($certificateNumber && Student::where('certificate_number', $certificateNumber)->exists()) {
                    $errors[] = "Baris {$rowNumber}: Nomor Ijazah '{$certificateNumber}' sudah digunakan mahasiswa lain.";

                    continue;
                }

                // Simpan Data
                $user = User::create([
                    'name' => $name,
                    'username' => $nim,
                    'email' => $nim.'@royal.ac.id',
                    'password' => Hash::make('password'),
                    'role' => 'mahasiswa',
                ]);

                Student::create([
                    'user_id' => $user->id,
                    'nim' => $nim,
                    'major' => $major,
                    'faculty' => $faculty,
                    'admission_year' => $admissionYear,
                    'gpa' => $gpa,
                    'status' => $status,
                    'certificate_status' => $certificateStatus,
                    'certificate_number' => $certificateNumber,
                    'graduation_date' => $graduationDate,
                    'degree' => $degree,
                ]);

                $successCount++;
            }

            fclose($handle);

            if (count($errors) > 0) {
                // Rollback transaksi jika ada error untuk mencegah data setengah diimpor
                DB::rollBack();

                return back()->with('import_errors', $errors)->with('error', 'Gagal mengimpor file CSV karena ditemukan beberapa kesalahan validasi data.');
            }

            DB::commit();

            return back()->with('success', "Berhasil mengimpor {$successCount} data mahasiswa secara massal!");

        } catch (\Exception $e) {
            DB::rollBack();
            fclose($handle);

            return back()->with('error', 'Terjadi kesalahan sistem saat memproses impor: '.$e->getMessage());
        }
    }

    /**
     * Unduh template file CSV contoh.
     */
    public function downloadTemplate()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="template_mahasiswa_royal.csv"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $columns = ['nim', 'nama', 'program_studi', 'fakultas', 'tahun_masuk', 'ipk', 'status_mahasiswa', 'status_ijazah', 'nomor_ijazah', 'tanggal_lulus', 'gelar'];

        $callback = function () use ($columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            // Tambahkan baris contoh
            fputcsv($file, [
                '23220465',
                'Andi Wijaya',
                'Sistem Informasi',
                'Fakultas Ilmu Komputer',
                '2023',
                '3.75',
                'Lulus',
                'Belum Cetak',
                '105/UNROY/SI/S1/2027',
                '2027-08-25',
                'S.Kom.',
            ]);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
