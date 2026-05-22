<?php

namespace App\Http\Controllers;

use App\Models\CertificateTemplate;
use App\Models\Student;

class VerificationController extends Controller
{
    /**
     * Memverifikasi ijazah mahasiswa secara publik berdasarkan NIM.
     * Dapat diakses secara publik tanpa harus login (guest).
     */
    public function verify($nim)
    {
        // Cari mahasiswa beserta relasi user
        $student = Student::with('user')->where('nim', $nim)->first();

        // Ambil template aktif untuk referensi visual/verifikasi jika diperlukan
        $activeTemplate = CertificateTemplate::active()->first();

        // Periksa apakah mahasiswa terdaftar dan memenuhi kriteria verifikasi:
        // 1. Status kelulusan harus 'Lulus'
        // 2. Status ijazah harus 'Sudah Cetak'
        $isValid = false;
        $rejectReason = '';

        if (! $student) {
            $rejectReason = 'Nomor Induk Mahasiswa (NIM) tidak terdaftar di sistem kami.';
        } elseif ($student->status !== 'Lulus') {
            $rejectReason = 'Mahasiswa yang bersangkutan belum terverifikasi Lulus secara resmi.';
        } elseif ($student->certificate_status !== 'Sudah Cetak') {
            $rejectReason = 'Ijazah resmi untuk mahasiswa ini belum diterbitkan atau dicetak oleh Biro Akademik.';
        } else {
            $isValid = true;
        }

        return view('verification.show', compact('student', 'isValid', 'rejectReason', 'activeTemplate'));
    }
}
