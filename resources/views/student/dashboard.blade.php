@extends('layouts.app')

@section('title', 'Portal Akademik Mahasiswa - Portal Ijazah')
@section('page-title', 'Portal Akademik Saya')
@section('page-subtitle', 'Pantau status ijazah, kelulusan, dan data akademik Anda.')

@section('content')
@php
    $student = Auth::user()->student;
@endphp

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

@if($errors->any())
<div class="mb-5 p-4 rounded-xl bg-red-50 border border-red-200 text-red-800 text-xs font-semibold">
    <div class="flex items-center mb-2 font-bold">
        <i data-lucide="alert-circle" class="w-4.5 h-4.5 text-red-500 mr-2.5 shrink-0"></i>
        <span>Terjadi beberapa kesalahan saat memproses data:</span>
    </div>
    <ul class="list-disc pl-8 space-y-1">
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<!-- Welcome Student Banner -->
<div class="bg-gradient-to-r from-royal-900 via-royal-800 to-slate-900 rounded-2xl p-5 sm:p-5.5 text-white shadow-md border border-royal-700/20 mb-5 relative overflow-hidden">
    <div class="absolute right-0 top-0 bottom-0 opacity-10 flex items-center pointer-events-none pr-10">
        <i data-lucide="graduation-cap" class="w-36 h-36 text-white"></i>
    </div>
    <div class="max-w-xl relative z-10">
        <h4 class="text-xl sm:text-2xl font-black tracking-tight">Selamat Datang, {{ Auth::user()->name }}!</h4>
        <p class="text-royal-100 text-xs sm:text-sm leading-relaxed mt-1">
            Pantau status penerbitan ijazah dan informasi akademik resmi Anda.
        </p>
    </div>
</div>

@if($student && $student->certificate_status === 'Sudah Cetak')
<!-- Certificate Ready Premium Banner -->
<div class="bg-gradient-to-r from-emerald-500/10 via-teal-500/5 to-transparent border border-emerald-500/25 rounded-2xl p-4.5 mb-5 flex flex-col sm:flex-row justify-between items-center gap-6 shadow-xs">
    <div class="flex items-center space-x-4">
        <div class="w-11 h-11 rounded-xl bg-emerald-600 text-white flex items-center justify-center shrink-0 shadow-md">
            <i data-lucide="award" class="w-5.5 h-5.5 text-gold-400"></i>
        </div>
        <div>
            <h4 class="text-sm font-bold text-slate-900">Ijazah Resmi Anda Telah Diterbitkan!</h4>
            <p class="text-xs text-slate-500 mt-1">Biro Akademik telah menyelesaikan proses pencetakan dan verifikasi data. Salinan digital ijazah presisi tinggi kini dapat Anda akses.</p>
        </div>
    </div>
    <a href="{{ route('student.preview') }}" target="_blank" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-extrabold rounded-xl transition-all flex items-center shrink-0 cursor-pointer shadow-sm">
        <i data-lucide="printer" class="w-4 h-4 mr-1.5 text-gold-300"></i>
        Lihat / Unduh Ijazah
    </a>
</div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-5">
    
    <!-- Left Column (2 Cols): Student Info & Timeline -->
    <div class="lg:col-span-2 space-y-5">
        
        <!-- Academic Information Card -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
            <h3 class="text-sm font-bold text-slate-900 mb-4 flex items-center">
                <i data-lucide="user" class="w-4.5 h-4.5 text-royal-600 mr-2"></i>
                Detail Akademik Resmi
            </h3>
            
            @if($student)
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-3.5 text-sm">
                <div class="pb-2 border-b border-slate-100">
                    <span class="text-slate-650 block text-[10px] font-bold uppercase tracking-wider">Nomor Induk Mahasiswa (NIM)</span>
                    <span class="font-mono font-bold text-slate-900 text-base mt-0.5 block">{{ $student->nim }}</span>
                </div>
                <div class="pb-2 border-b border-slate-100">
                    <span class="text-slate-650 block text-[10px] font-bold uppercase tracking-wider">Nama Lengkap</span>
                    <span class="font-bold text-slate-900 text-base mt-0.5 block">{{ Auth::user()->name }}</span>
                </div>
                <div class="pb-2 border-b border-slate-100">
                    <span class="text-slate-650 block text-[10px] font-bold uppercase tracking-wider">Program Studi</span>
                    <span class="font-semibold text-slate-850 mt-0.5 block">{{ $student->major }}</span>
                </div>
                <div class="pb-2 border-b border-slate-100">
                    <span class="text-slate-650 block text-[10px] font-bold uppercase tracking-wider">Fakultas</span>
                    <span class="font-semibold text-slate-850 mt-0.5 block">{{ $student->faculty }}</span>
                </div>
                <div class="pb-2 border-b border-slate-100">
                    <span class="text-slate-650 block text-[10px] font-bold uppercase tracking-wider">Tahun Masuk</span>
                    <span class="font-semibold text-slate-850 mt-0.5 block">{{ $student->admission_year }}</span>
                </div>
                <div class="pb-2 border-b border-slate-100">
                    <span class="text-slate-650 block text-[10px] font-bold uppercase tracking-wider">Gelar Akademik Target</span>
                    <span class="font-semibold text-royal-600 mt-0.5 block">{{ $student->degree ?: 'Belum Diatur' }}</span>
                </div>
                @if($student->graduation_date)
                <div class="pb-2 border-b border-slate-100">
                    <span class="text-slate-650 block text-[10px] font-bold uppercase tracking-wider">Tanggal Lulus</span>
                    <span class="font-semibold text-slate-850 mt-0.5 block">{{ $student->graduation_date->format('d M Y') }}</span>
                </div>
                @endif
                @if($student->certificate_number)
                <div class="pb-2 border-b border-slate-100">
                    <span class="text-slate-650 block text-[10px] font-bold uppercase tracking-wider">Nomor Ijazah Resmi</span>
                    <span class="font-mono text-slate-900 font-bold mt-0.5 block">{{ $student->certificate_number }}</span>
                </div>
                @endif
            </div>
            @else
            <div class="py-8 text-center text-slate-400 text-sm">
                <i data-lucide="help-circle" class="w-8 h-8 text-slate-300 mx-auto mb-2"></i>
                Detail data akademik belum dimasukkan oleh Admin / Biro Akademik.
            </div>
            @endif
        </div>

        <!-- Dynamic Timeline Process Card -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
            <h3 class="text-sm font-bold text-slate-900 mb-4 flex items-center">
                <i data-lucide="activity" class="w-4.5 h-4.5 text-royal-600 mr-2"></i>
                Proses Penerbitan Ijazah
            </h3>

            @if($student)
                @php
                    $status = $student->certificate_status;
                    
                    $steps = [
                        [
                            'title' => 'Verifikasi Data Akademik',
                            'description' => 'Seluruh berkas nilai & biodata akademik telah disetujui Biro Akademik.',
                            'status' => 'completed',
                            'date' => $student->created_at ? $student->created_at->format('d M Y') : null,
                        ],
                        [
                            'title' => 'Validasi Fakultas',
                            'description' => 'Verifikasi keselarasan data kelulusan oleh tingkat Fakultas.',
                            'status' => ($status === 'Antrean Cetak' || $status === 'Sudah Cetak') ? 'completed' : 'active',
                            'date' => ($status === 'Antrean Cetak' || $status === 'Sudah Cetak') ? ($student->updated_at ? $student->updated_at->format('d M Y') : null) : null,
                        ],
                        [
                            'title' => 'Persetujuan Administrasi',
                            'description' => 'Pengecekan kelengkapan administratif dan bebas pinjaman pustaka/biaya.',
                            'status' => ($status === 'Sudah Cetak') ? 'completed' : (($status === 'Antrean Cetak') ? 'active' : 'pending'),
                            'date' => ($status === 'Sudah Cetak') ? ($student->updated_at ? $student->updated_at->format('d M Y') : null) : null,
                        ],
                        [
                            'title' => 'Proses Penerbitan Ijazah',
                            'description' => 'Pencetakan fisik ijazah presisi tinggi dan aktivasi QR Code publik resmi.',
                            'status' => ($status === 'Sudah Cetak') ? 'completed' : 'pending',
                            'date' => ($status === 'Sudah Cetak') ? ($student->graduation_date ? $student->graduation_date->format('d M Y') : ($student->updated_at ? $student->updated_at->format('d M Y') : null)) : null,
                        ]
                    ];
                @endphp

                <div class="relative pl-6 space-y-4">
                    <!-- Progress Connection Line -->
                    <div class="absolute left-2.5 top-1.5 bottom-1.5 w-0.5 bg-slate-200"></div>
                    
                    @foreach($steps as $index => $step)
                        <div class="relative flex items-start group p-2 -mx-2 rounded-xl {{ $step['status'] === 'active' ? 'bg-royal-50/40 border border-royal-100/50 shadow-xxs' : 'border border-transparent' }}">
                            <!-- Step Dot Indicator -->
                            <div class="absolute -left-[19px] mt-2 flex items-center justify-center">
                                @if($step['status'] === 'completed')
                                    <div class="w-[18px] h-[18px] rounded-full bg-emerald-500 text-white flex items-center justify-center shadow-xs ring-4 ring-emerald-50 border border-emerald-400">
                                        <i data-lucide="check" class="w-2.5 h-2.5"></i>
                                    </div>
                                @elseif($step['status'] === 'active')
                                    <div class="w-[18px] h-[18px] rounded-full bg-royal-650 text-white flex items-center justify-center shadow-xs ring-4 ring-royal-50 border border-royal-500 animate-pulse">
                                        <div class="w-1.5 h-1.5 rounded-full bg-white"></div>
                                    </div>
                                @else
                                    <div class="w-[18px] h-[18px] rounded-full bg-slate-100 text-slate-400 flex items-center justify-center ring-4 ring-slate-50 border border-slate-200">
                                        <div class="w-1.5 h-1.5 rounded-full bg-slate-350"></div>
                                    </div>
                                @endif
                            </div>

                            <!-- Step Content -->
                            <div class="ml-4 flex-1 flex flex-col sm:flex-row sm:items-start justify-between gap-2">
                                <div class="space-y-0.5">
                                    <h4 class="text-xs font-bold @if($step['status'] === 'completed') text-slate-800 @elseif($step['status'] === 'active') text-royal-700 font-extrabold @else text-slate-400 @endif">
                                        {{ $step['title'] }}
                                    </h4>
                                    <p class="text-[11px] text-slate-450 leading-relaxed max-w-lg">
                                        {{ $step['description'] }}
                                    </p>
                                </div>
                                
                                <div class="flex items-center space-x-2 shrink-0 mt-0.5 sm:mt-0">
                                    <!-- Status Badge -->
                                    @if($step['status'] === 'completed')
                                        <span class="px-2 py-0.5 rounded bg-emerald-50 text-emerald-700 text-[9px] font-bold border border-emerald-100/50">Selesai</span>
                                    @elseif($step['status'] === 'active')
                                        <span class="px-2 py-0.5 rounded bg-royal-50 text-royal-700 text-[9px] font-bold border border-royal-100/50 animate-pulse">Diproses</span>
                                    @else
                                        <span class="px-2 py-0.5 rounded bg-slate-50 text-slate-400 text-[9px] font-bold border border-slate-100">Menunggu</span>
                                    @endif

                                    <!-- Date label -->
                                    @if($step['date'])
                                        <span class="text-[9px] text-slate-400 font-bold font-mono">{{ $step['date'] }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

    </div>

    <!-- Right Column (1 Col): GPA Predicate & Security change password -->
    <div class="space-y-5">
        
        <!-- GPA Predicate Card -->
        @if($student)
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs relative overflow-hidden">
            <!-- Subtle Watermark Icon -->
            <div class="absolute right-4 top-4 opacity-10 pointer-events-none">
                <i data-lucide="award" class="w-12 h-12 text-royal-650"></i>
            </div>
            
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-1.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-royal-600"></span>
                    <span class="text-xxs font-bold uppercase tracking-wider text-slate-500">Indeks Prestasi Kumulatif</span>
                </div>
                <!-- Verification Tag -->
                <div class="flex items-center space-x-1 bg-emerald-50/70 text-emerald-700 px-2 py-0.5 rounded-md border border-emerald-100/40">
                    <span class="w-1 h-1 rounded-full bg-emerald-500"></span>
                    <span class="text-[9px] font-bold uppercase tracking-wider">Status: Terverifikasi</span>
                </div>
            </div>
            
            <div class="mt-4 flex items-baseline space-x-2">
                <span class="text-4xl font-extrabold text-slate-900 tracking-tight">{{ number_format($student->gpa, 2) }}</span>
                <span class="text-xs text-slate-400">/ 4.00</span>
            </div>
            
            <div class="mt-3 pt-3 border-t border-slate-100 flex items-center justify-between">
                <div>
                    <span class="text-[10px] text-slate-455 uppercase font-bold tracking-wider block">Predikat Kelulusan</span>
                    <span class="text-xs font-bold text-slate-800 uppercase tracking-wide">{{ $student->predicate }}</span>
                </div>
                @if($student->gpa >= 3.51)
                <span class="px-2 py-0.5 bg-emerald-50 text-emerald-700 border border-emerald-250 text-[10px] font-bold rounded-md uppercase tracking-wider">Cum Laude</span>
                @endif
            </div>
        </div>
        @endif

        <!-- Security change password card -->
        <div id="change-password-section" class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs" x-data="{ showForm: new URLSearchParams(window.location.search).has('change_password') }">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <i data-lucide="lock" class="w-4 h-4 text-slate-500"></i>
                    <h3 class="text-xs font-bold text-slate-700">Keamanan Akun</h3>
                </div>
                <button @click="showForm = !showForm" 
                        class="text-xs font-semibold text-royal-650 hover:text-royal-800 transition-colors flex items-center gap-1 cursor-pointer">
                    <span x-text="showForm ? 'Batal' : 'Ubah Sandi'"></span>
                    <i data-lucide="chevron-down" class="w-3.5 h-3.5 transition-transform duration-200" :class="showForm ? 'rotate-180' : ''"></i>
                </button>
            </div>
            
            <div x-show="showForm" 
                 x-collapse
                 x-transition:enter="transition ease-out duration-250"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 class="mt-4 pt-4 border-t border-slate-100"
                 style="display: none;">
                
                <form action="{{ route('profile.change-password') }}" method="POST" class="space-y-3.5">
                    @csrf
                    <div>
                        <label class="block text-[10px] font-bold text-slate-455 uppercase tracking-wider mb-1">Sandi Saat Ini</label>
                        <input type="password" 
                               name="current_password" 
                               required
                               class="w-full px-3 py-1.5 border border-slate-200 rounded-lg text-xs bg-slate-50 focus:bg-white focus:ring-2 focus:ring-royal-500/20 focus:border-royal-500 focus:outline-hidden transition-all">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-455 uppercase tracking-wider mb-1">Sandi Baru</label>
                        <input type="password" 
                               name="new_password" 
                               required
                               class="w-full px-3 py-1.5 border border-slate-200 rounded-lg text-xs bg-slate-50 focus:bg-white focus:ring-2 focus:ring-royal-500/20 focus:border-royal-500 focus:outline-hidden transition-all">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-455 uppercase tracking-wider mb-1">Konfirmasi Sandi Baru</label>
                        <input type="password" 
                               name="new_password_confirmation" 
                               required
                               class="w-full px-3 py-1.5 border border-slate-200 rounded-lg text-xs bg-slate-50 focus:bg-white focus:ring-2 focus:ring-royal-500/20 focus:border-royal-500 focus:outline-hidden transition-all">
                    </div>
                    <button type="submit" 
                            class="w-full py-2 bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs rounded-lg transition-colors cursor-pointer flex items-center justify-center shadow-xs mt-1">
                        <i data-lucide="key-round" class="w-3.5 h-3.5 mr-1.5 text-gold-400"></i>
                        Perbarui Kata Sandi
                    </button>
                </form>
            </div>
        </div>

    </div>

</div>
@endsection
