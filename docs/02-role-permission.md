# Role & Permission Management PRD
## Royal Academic Print Suite

---

# Feature Information

## Feature Name
Role & Permission Management

## Module
Authorization System

## Priority
High

## Status
Planning

---

# Objective

Membangun sistem role dan permission
yang sederhana, aman, dan mudah dikelola
untuk membatasi akses user berdasarkan role.

Sistem authorization harus memastikan bahwa:

- Admin/Biro Akademik memiliki akses penuh
- Mahasiswa hanya dapat mengakses data pribadi
- Route dan fitur terlindungi dengan baik

---

# User Roles

## Admin / Biro Akademik

Role utama pengelola sistem akademik.

Memiliki akses penuh terhadap seluruh fitur sistem.

---

## Mahasiswa

Role user mahasiswa.

Hanya dapat mengakses data dan fitur milik pribadi.

---

# Authorization Concept

Sistem menggunakan:

- Role-Based Access Control (RBAC)
- Middleware authorization
- Route protection
- UI visibility restriction

---

# Main Features

## Role Validation

Sistem memvalidasi role user
setiap kali user mengakses route tertentu.

---

## Route Protection

Route tertentu hanya dapat diakses
oleh role yang sesuai.

---

## Sidebar Restriction

Sidebar menu akan tampil
sesuai role user.

---

## Feature Restriction

Fitur tertentu hanya dapat digunakan
oleh role tertentu.

---

# Permission Matrix

| Feature | Admin | Mahasiswa |
|---|---|---|
| Login | ✅ | ✅ |
| Dashboard | ✅ | ✅ |
| Kelola Mahasiswa | ✅ | ❌ |
| Cetak Ijazah | ✅ | ❌ |
| Preview Ijazah | ✅ | ✅ |
| Download PDF | ✅ | ✅ |
| Upload Template | ✅ | ❌ |
| Activity Logs | ✅ | ❌ |
| QR Verification | ✅ | ✅ |
| Pengaturan Sistem | ✅ | ❌ |

---

# Admin Permissions

## Dashboard Access

Admin dapat melihat:

- statistik sistem
- aktivitas terbaru
- total mahasiswa
- total pencetakan
- analytics dashboard

---

## Student Management

Admin dapat:

- tambah mahasiswa
- edit mahasiswa
- hapus mahasiswa
- import data mahasiswa
- search mahasiswa

---

## Certificate Management

Admin dapat:

- preview ijazah
- cetak ijazah
- export PDF
- batch printing
- upload template

---

## System Management

Admin dapat:

- melihat activity logs
- mengelola pengaturan sistem
- mengelola template

---

# Student Permissions

## Student Dashboard

Mahasiswa dapat melihat:

- status ijazah
- informasi pribadi
- notifikasi akademik

---

## Certificate Access

Mahasiswa dapat:

- preview ijazah
- download PDF
- scan QR verification

---

## Profile Access

Mahasiswa dapat:

- melihat profile pribadi
- update password pribadi

Mahasiswa tidak dapat mengubah:

- nama
- NIM
- data akademik utama

---

# Middleware Requirements

## Admin Middleware

```txt id="jqm4r6"
admin