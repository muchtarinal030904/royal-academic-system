# QR Verification System PRD
## Royal Academic Print Suite

---

# Feature Information

## Feature Name
QR Verification System

## Module
Certificate Verification

## Priority
High

## Status
Planning

---

# Objective

Membangun sistem verifikasi ijazah
menggunakan QR Code
untuk memastikan keaslian dokumen akademik.

Sistem harus memungkinkan:
- validasi cepat
- akses mudah
- keamanan dasar dokumen
- pengalaman modern seperti institusi profesional

---

# User Roles

## Admin / Biro Akademik

Dapat:

- generate QR
- melihat histori verifikasi
- memvalidasi data dokumen

---

## Mahasiswa

Dapat:

- melihat QR ijazah
- membagikan QR verification

---

## Public User

Dapat:

- scan QR
- melihat validasi dokumen

Tanpa perlu login.

---

# Main Features

## QR Code Generation

Generate QR unik untuk setiap ijazah.

---

## QR Verification Page

Menampilkan status validasi dokumen.

---

## Public Verification Access

QR dapat diverifikasi publik.

---

## Verification Status

Menampilkan status valid/tidak valid.

---

## Verification History

Menyimpan histori scan dan verifikasi.

---

# Main Workflow

## Verification Flow

1. QR Code dicetak pada ijazah
2. User scan QR
3. Sistem membuka halaman verifikasi
4. Sistem memvalidasi token
5. Sistem menampilkan status dokumen

---

# QR Code Structure

## Verification URL

```txt id="h8v5ta"
/verify/{token}