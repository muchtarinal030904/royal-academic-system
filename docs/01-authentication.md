# Authentication System PRD
## Royal Academic Print Suite

---

# Feature Information

## Feature Name
Authentication System

## Module
Authentication

## Priority
High

## Status
Planning

---

# Objective

Membangun sistem autentikasi modern,
aman, ringan, dan responsif
untuk Admin/Biro Akademik
dan Mahasiswa Universitas Royal.

Sistem login harus memberikan pengalaman
seperti portal akademik universitas modern
dengan tampilan profesional dan keamanan dasar yang baik.

---

# User Roles

## Admin / Biro Akademik

Memiliki akses penuh terhadap:

- dashboard admin
- manajemen mahasiswa
- pencetakan ijazah
- template ijazah
- export PDF
- activity logs
- pengaturan sistem

---

## Mahasiswa

Memiliki akses terhadap:

- dashboard mahasiswa
- status ijazah
- preview ijazah
- download PDF
- QR verification
- profile pribadi

---

# Authentication Concept

Sistem tidak menyediakan fitur registrasi publik.

Seluruh akun dibuat langsung oleh
Admin/Biro Akademik Universitas Royal.

Mahasiswa hanya dapat login menggunakan
akun yang telah diberikan oleh pihak kampus.

---

# Authentication Flow

## Login Flow

1. User membuka halaman login
2. User memasukkan NIM / ID Akademik
3. User memasukkan password
4. Sistem melakukan validasi
5. Sistem memverifikasi akun
6. Sistem membuat authenticated session
7. User diarahkan sesuai role

---

# Redirect Rules

## Admin / Biro Akademik

Redirect setelah login:

```txt
/admin/dashboard