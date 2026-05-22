# Task Checklist - Phase 5 & 6: History, Logs & Settings

Rangkaian pengerjaan Fase 5 & 6 untuk mengimplementasikan modul Riwayat Cetak, Log Keamanan Audit, dan Pengaturan Sistem Dinamis pada Royal Academic Print Suite.

## Checklist Kemajuan
- `[/]` **Step 1: Database Migrations & Models**
    - `[ ]` Buat berkas migrasi `create_print_histories_table`
    - `[ ]` Buat berkas migrasi `create_activity_logs_table`
    - `[ ]` Jalankan `php artisan migrate` untuk memperbarui database
    - `[ ]` Buat model `PrintHistory` dengan relasi `Student` dan `User`
    - `[ ]` Buat model `ActivityLog` dengan relasi `User`
- `[ ]` **Step 2: Security Audit Logging Service**
    - `[ ]` Buat `AuditLogService` untuk standarisasi penulisan log aktivitas
    - `[ ]` Integrasikan logging di `AuthController` (Login, Logout, Gagal Login)
    - `[ ]` Integrasikan logging di `ProfileController` (Ganti Password)
    - `[ ]` Integrasikan logging di `CertificateTemplateController` (Ganti Koordinat)
- `[ ]` **Step 3: Print History Tracking Integration**
    - `[ ]` Hubungkan pencatatan ke `print_histories` saat ijazah resmi dicetak di `CertificatePrintController`
- `[ ]` **Step 4: Controllers & Routing Setup**
    - `[ ]` Buat `PrintHistoryController` (Filter, Search, Reset)
    - `[ ]` Buat `ActivityLogController` (Log Audit timeline)
    - `[ ]` Buat `SystemSettingController` (Simpan data Rektor/Dekan secara dinamis)
    - `[ ]` Daftarkan rute-rute baru di `routes/web.php`
- `[ ]` **Step 5: High-Fidelity UI Views**
    - `[ ]` Perbarui `/admin/history` (`history.blade.php`) dengan dashboard cetak premium
    - `[ ]` Perbarui `/admin/logs` (`logs.blade.php`) dengan timeline log audit berwarna
    - `[x]` **Perbaikan Tambahan (Hotfix):** Mengatasi isu sidebar tidak mengecil secara fisik dengan menambahkan `overflow-x-hidden` pada `<aside>` saat collapsed, menyelaraskan logo di tengah (`justify-center`), serta mengatur inisialisasi awal agar sidebar default-nya mengecil (collapsed) secara elegan saat pertama kali dibuka.
    - `[x]` **Penyederhanaan Visual Profil (Hotfix):** Menghapus modul profil user yang berulang (redundan) di sidebar agar antarmuka jauh lebih bersih (*clean UI*), serta mengubah avatar pojok kanan atas menjadi **Dynamic Initials Avatar** berbasis inisial nama asli user yang sedang login menggunakan API Universitas Royal dengan warna tema biru mewah.
- `[ ]` **Step 6: Verification & Final Walkthrough**
    - `[ ]` Jalankan verifikasi alur data secara end-to-end
    - `[ ]` Buat walkthrough.md yang mendokumentasikan hasil pengujian akhir
