@extends('layouts.app')

@section('title', 'QR Verification - Royal Academic Print Suite')
@section('page-title', 'Pusat Verifikasi QR Ijazah')
@section('page-subtitle', 'Tautan resmi dan kode QR enkripsi untuk pembuktian keaslian ijazah Anda.')

@section('content')
@php
    $student = Auth::user()->student;
@endphp

<div class="max-w-3xl mx-auto">
    <!-- Session Alerts for Success / Error -->
    @if(session('success'))
    <div class="mb-5 p-4 rounded-xl bg-emerald-50 border border-emerald-250 text-emerald-800 flex items-center text-xs font-semibold">
        <i data-lucide="check-circle" class="w-4.5 h-4.5 text-emerald-500 mr-2.5 shrink-0"></i>
        <span>{{ session('success') }}</span>
    </div>
    @endif

    @if(session('error'))
    <div class="mb-5 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 flex items-center text-xs font-semibold">
        <i data-lucide="alert-circle" class="w-4.5 h-4.5 text-rose-500 mr-2.5 shrink-0"></i>
        <span>{{ session('error') }}</span>
    </div>
    @endif

    @if($student && $student->certificate_status === 'Sudah Cetak')
        <!-- VERIFIED STATE -->
        <div class="bg-white rounded-3xl border border-slate-200/80 shadow-md overflow-hidden">
            <!-- Header Section -->
            <div class="bg-gradient-to-r from-royal-900 to-slate-900 p-6 sm:p-8 text-white flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <span class="px-2.5 py-0.5 text-xxs font-extrabold uppercase tracking-widest bg-emerald-500/20 text-emerald-300 rounded-full border border-emerald-500/25">
                        Status: Terbit & Aktif
                    </span>
                    <h3 class="text-xl font-bold tracking-tight mt-2.5">QR Code Verifikasi Resmi</h3>
                    <p class="text-xs text-slate-400 mt-1 leading-relaxed">Tautkan kode ini di CV, LinkedIn, atau portofolio untuk membuktikan keaslian gelar Anda.</p>
                </div>
                <div class="shrink-0 flex items-center bg-white/10 backdrop-blur-md px-3.5 py-1.5 rounded-xl border border-white/20">
                    <i data-lucide="shield-check" class="w-4 h-4 text-emerald-400 mr-2"></i>
                    <span class="text-xxs font-black tracking-widest text-white uppercase">SECURE VERIFIED</span>
                </div>
            </div>

            <!-- Body Contents -->
            <div class="p-6 sm:p-8">
                <div class="grid grid-cols-1 md:grid-cols-12 gap-8 items-center">
                    
                    <!-- Left: QR Code Box -->
                    <div class="md:col-span-5 flex flex-col items-center justify-center p-6 bg-slate-50 border border-slate-100 rounded-2xl">
                        <div class="bg-white p-3 rounded-xl shadow-xs border border-slate-150 relative">
                            <!-- QRCode Container -->
                            <div id="student-verification-qrcode" 
                                 class="qrcode-container"
                                 data-url="{{ route('verification.verify', $student->nim) }}"
                                 style="width: 150px; height: 150px;">
                            </div>
                        </div>
                        <span class="text-xxs font-extrabold text-slate-400 tracking-wider uppercase mt-4">SCAN DENGAN KAMERA</span>
                    </div>

                    <!-- Right: Links and Details -->
                    <div class="md:col-span-7 space-y-5">
                        <div class="space-y-1">
                            <span class="text-xxs font-bold text-slate-400 uppercase tracking-wider block">Tautan Verifikasi Publik</span>
                            
                            <!-- Copy Tautan Input Group -->
                            <div class="flex items-center mt-1">
                                <input type="text" 
                                       id="verification-link-input"
                                       readonly
                                       value="{{ route('verification.verify', $student->nim) }}"
                                       class="flex-1 bg-slate-50 border border-slate-200 border-r-0 rounded-l-xl px-3 py-2 text-xs font-mono font-bold text-slate-700 focus:outline-hidden">
                                <button onclick="copyVerificationLink()" 
                                        class="px-4 py-2 bg-royal-600 hover:bg-royal-700 text-white font-bold text-xs rounded-r-xl transition-colors cursor-pointer flex items-center shrink-0">
                                    <i data-lucide="copy" class="w-3.5 h-3.5 mr-1.5" id="copy-icon"></i>
                                    <span id="copy-text">Salin</span>
                                </button>
                            </div>
                        </div>

                        <!-- Info details -->
                        <div class="space-y-3.5 text-xs">
                            <div class="flex justify-between py-2 border-b border-slate-100">
                                <span class="text-slate-400">Nomor Ijazah</span>
                                <span class="font-mono font-bold text-slate-800">{{ $student->certificate_number }}</span>
                            </div>
                            <div class="flex justify-between py-2 border-b border-slate-100">
                                <span class="text-slate-400">Gelar Resmi</span>
                                <span class="font-bold text-gold-600 uppercase tracking-wide">Sarjana {{ $student->degree ?: 'Sarjana' }}</span>
                            </div>
                            <div class="flex justify-between py-2 border-b border-slate-100">
                                <span class="text-slate-400">Tanggal Kelulusan</span>
                                <span class="font-semibold text-slate-800">
                                    @if($student->graduation_date)
                                        {{ $student->graduation_date->translatedFormat('d F Y') }}
                                    @else
                                        {{ now()->translatedFormat('d F Y') }}
                                    @endif
                                </span>
                            </div>
                        </div>

                        <div class="p-3.5 bg-emerald-50 border border-emerald-100 rounded-xl flex items-start text-xxs text-emerald-800 leading-relaxed">
                            <i data-lucide="shield" class="w-4 h-4 text-emerald-600 mr-2 shrink-0 mt-0.5 animate-pulse"></i>
                            <span>Kode QR dan tautan di atas terlindungi tanda tangan kriptografis Universitas Royal. Instansi yang memindai QR ini akan langsung diarahkan ke halaman detail autentik resmi kami.</span>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    @else
        <!-- PENDING STATE -->
        <div class="bg-white rounded-3xl border border-slate-200/80 shadow-md overflow-hidden text-center p-8 sm:p-12">
            <div class="w-16 h-16 rounded-full bg-amber-50 border border-amber-100 flex items-center justify-center mx-auto mb-6">
                <i data-lucide="qr-code" class="w-8 h-8 text-amber-500 animate-pulse"></i>
            </div>
            
            <h3 class="text-lg font-bold text-slate-900">QR Code Belum Aktif</h3>
            <p class="text-sm text-slate-500 max-w-md mx-auto mt-2 leading-relaxed">
                Tautan verifikasi publik dan Kode QR resmi hanya akan diaktifkan setelah Biro Akademik secara resmi mencetak ijazah fisik Anda.
            </p>

            <!-- Pending Timeline / Explainer -->
            <div class="mt-8 max-w-sm mx-auto p-4 bg-slate-50 border border-slate-100 rounded-2xl text-left space-y-3">
                <span class="text-xxs font-extrabold text-slate-450 uppercase tracking-widest block mb-1">Panduan Alur Aktifasi</span>
                
                <div class="flex items-start space-x-3 text-xs">
                    <div class="w-5 h-5 rounded-full bg-emerald-600 text-white flex items-center justify-center shrink-0 mt-0.5">
                        <i data-lucide="check" class="w-3 h-3"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-slate-800">Verifikasi Kelulusan</h4>
                        <p class="text-xxs text-slate-400">Data akademik Anda telah lulus sidang yudisium resmi.</p>
                    </div>
                </div>

                <div class="flex items-start space-x-3 text-xs">
                    <div class="w-5 h-5 rounded-full bg-amber-500 text-white flex items-center justify-center shrink-0 mt-0.5 animate-pulse">
                        <i data-lucide="clock" class="w-3 h-3"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-slate-800">Proses Pencetakan & Penerbitan</h4>
                        <p class="text-xxs text-slate-400">Menunggu antrean validasi tanda tangan elektronik oleh Biro Akademik.</p>
                    </div>
                </div>

                <div class="flex items-start space-x-3 text-xs opacity-50">
                    <div class="w-5 h-5 rounded-full bg-slate-200 text-slate-505 flex items-center justify-center shrink-0 mt-0.5">
                        <span class="text-xxs font-bold">3</span>
                    </div>
                    <div>
                        <h4 class="font-bold text-slate-500">QR Aktif Otomatis</h4>
                        <p class="text-xxs text-slate-400">Tautan verifikasi publik `/verify/{nim}` langsung dapat diakses.</p>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

@section('scripts')
@if($student && $student->certificate_status === 'Sudah Cetak')
<!-- Load QRCode JS via CDN -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const qrContainer = document.getElementById("student-verification-qrcode");
        if (qrContainer) {
            const url = qrContainer.getAttribute("data-url");
            
            qrContainer.innerHTML = "";
            new QRCode(qrContainer, {
                text: url,
                width: 150,
                height: 150,
                colorDark : "#0f172a",
                colorLight : "#ffffff",
                correctLevel : QRCode.CorrectLevel.H
            });
        }
    });

    function copyVerificationLink() {
        const linkInput = document.getElementById("verification-link-input");
        const copyIcon = document.getElementById("copy-icon");
        const copyText = document.getElementById("copy-text");

        // Copy input text
        linkInput.select();
        linkInput.setSelectionRange(0, 99999); // For mobile devices
        navigator.clipboard.writeText(linkInput.value);

        // Update Button State to Checked
        const originalIcon = copyIcon.getAttribute("data-lucide");
        copyIcon.setAttribute("data-lucide", "check");
        copyText.innerText = "Tersalin!";
        
        // Re-init lucide for the updated icon
        lucide.createIcons();

        // Revert back after 2 seconds
        setTimeout(() => {
            copyIcon.setAttribute("data-lucide", "copy");
            copyText.innerText = "Salin";
            lucide.createIcons();
        }, 2000);
    }
</script>
@endif
@endsection
