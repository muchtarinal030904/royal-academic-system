# Rencana Implementasi - Riwayat Cetak, Log Aktivitas & Pengaturan Sistem (Fase 5 & 6)

Merancang sistem pelacakan pencetakan (*Print History*), perekaman log keamanan audit (*Activity Logs*), dan manajemen pengaturan sistem (*System Settings*) untuk melengkapi *Royal Academic Print Suite* menjadi platform berstandar enterprise.

---

## 🛠️ Perubahan yang Diusulkan

### 1. Skema Basis Data & Model Baru

#### [NEW] [2026_05_21_140000_create_print_histories_table.php](file:///home/yoruuu/Documents/kelompok_cetak_ijazah/database/migrations/2026_05_21_140000_create_print_histories_table.php)
Tabel untuk mencatat riwayat resmi pencetakan ijazah mahasiswa oleh staf akademik.
- `id` (Primary Key)
- `student_id` (Foreign Key ke `students`, onDelete cascade)
- `user_id` (Foreign Key ke `users` - admin yang mencetak, nullable / set null)
- `certificate_number` (String)
- `printed_at` (Datetime)
- `ip_address` (String)
- `user_agent` (String)
- Timestamps

#### [NEW] [PrintHistory.php](file:///home/yoruuu/Documents/kelompok_cetak_ijazah/app/Models/PrintHistory.php)
Model Eloquent untuk `PrintHistory` yang menjabarkan relasi ke `Student` dan `User` (pencetak).

---

#### [NEW] [2026_05_21_140500_create_activity_logs_table.php](file:///home/yoruuu/Documents/kelompok_cetak_ijazah/database/migrations/2026_05_21_140500_create_activity_logs_table.php)
Tabel log audit keamanan umum yang mencatat setiap peristiwa administratif yang penting.
- `id` (Primary Key)
- `user_id` (Foreign Key ke `users`, nullable)
- `username` (String, fallback jika user dihapus)
- `action` (String, e.g., 'LOGIN', 'LOGOUT', 'UPDATE_TEMPLATE', 'CHANGE_PASSWORD', 'IMPORT_STUDENTS')
- `description` (Text, detail aktivitas)
- `ip_address` (String)
- `user_agent` (String)
- Timestamps

#### [NEW] [ActivityLog.php](file:///home/yoruuu/Documents/kelompok_cetak_ijazah/app/Models/ActivityLog.php)
Model Eloquent untuk log audit sistem.

---

### 2. Logika Pengendali & Middleware (Controller & Middleware)

#### [NEW] [AuditLogService.php](file:///home/yoruuu/Documents/kelompok_cetak_ijazah/app/Services/AuditLogService.php)
Sebuah kelas helper/layanan khusus untuk mempermudah perekaman log aktivitas di seluruh aplikasi dari controller atau event manapun secara konsisten.
```php
AuditLogService::log('LOGIN', 'Melakukan login ke dalam sistem.');
AuditLogService::log('UPDATE_TEMPLATE', 'Mengubah konfigurasi koordinat untuk template: Utama.');
```

#### [NEW] [PrintHistoryController.php](file:///home/yoruuu/Documents/kelompok_cetak_ijazah/app/Http/Controllers/PrintHistoryController.php)
Mengambil data riwayat pencetakan dengan pencarian nama/NIM/Nomor Ijazah, filter rentang tanggal, filter admin pencetak, serta fitur ekspor ringkasan.

#### [NEW] [ActivityLogController.php](file:///home/yoruuu/Documents/kelompok_cetak_ijazah/app/Http/Controllers/ActivityLogController.php)
Menyediakan daftar riwayat log aktivitas administratif untuk admin beserta filter tipe aksi log untuk keperluan audit kepatuhan keamanan.

#### [NEW] [SystemSettingController.php](file:///home/yoruuu/Documents/kelompok_cetak_ijazah/app/Http/Controllers/SystemSettingController.php)
Mengelola pengaturan aplikasi (nama rektor, nama dekan, batas retensi log) dan fungsi pembersihan log audit lama secara aman.

---

### 3. Antarmuka Pengguna (Views)

#### [MODIFY] [history.blade.php](file:///home/yoruuu/Documents/kelompok_cetak_ijazah/resources/views/admin/history.blade.php)
Mengganti placeholder dengan tabel riwayat penerbitan ijazah yang sangat premium. Menampilkan kartu rangkuman (total cetak bulan ini, cetak hari ini), formulir filter, tombol cetak ulang pratinjau, dan ekspor.

#### [MODIFY] [logs.blade.php](file:///home/yoruuu/Documents/kelompok_cetak_ijazah/resources/views/admin/logs.blade.php)
Mengganti placeholder dengan umpan log (*activity stream feed*) berdesain garis waktu (timeline) modern, warna badge berdasarkan kategori bahaya keamanan (LOGIN/LOGOUT: Slate, UPDATE/IMPORT: Amber, PASSWORD_CHANGE: Red), pencarian, dan opsi hapus riwayat lama.

#### [MODIFY] [settings.blade.php](file:///home/yoruuu/Documents/kelompok_cetak_ijazah/resources/views/admin/settings.blade.php)
Mengubah placeholder menjadi panel pengaturan multi-tab yang sangat premium:
- **Tab 1: Institusi**: Mengedit nama institusi, nama rektor, NIP rektor, logo universitas secara dinamis (akan dimasukkan ke dalam basis data/konfigurasi).
- **Tab 2: Sistem & Keamanan**: Konfigurasi retensi log audit, tombol bersihkan log secara manual, dan sakelar keamanan akses.

---

### 4. Integrasi Logika Aplikasi Otomatis
- Menghubungkan proses cetak di `CertificatePrintController::updateStatus` agar otomatis menyisipkan rekaman riwayat ke tabel `print_histories` sekaligus mencatat ke `activity_logs`.
- Menghubungkan `AuthController` agar merekam log saat login, logout, atau saat gagal login (mencegah serangan brute-force).
- Menghubungkan `ProfileController` untuk merekam log saat kata sandi diganti.
- Menghubungkan `CertificateTemplateController` untuk merekam log saat koordinat ijazah diperbarui.

---

## 📋 Rencana Verifikasi

### Uji Otomatis & Manual:
1. **Verifikasi Jalur Logika**:
   - Jalankan migrasi basis data baru: `php artisan migrate`.
   - Lakukan login, ganti password, simpan koordinat, dan lakukan cetak ijazah.
   - Periksa tabel database `activity_logs` dan `print_histories` untuk memastikan data terisi sempurna.
2. **Verifikasi UI**:
   - Buka halaman `/admin/history` dan pastikan data tampil lengkap dengan filter tanggal kerja.
   - Buka halaman `/admin/logs` untuk meninjau log aktivitas terbaru yang baru saja kita lakukan.
   - Buka halaman `/admin/settings` untuk mengganti data rektor dan pastikan tersimpan dengan benar.
