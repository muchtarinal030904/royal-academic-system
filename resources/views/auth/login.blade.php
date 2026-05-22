@extends('layouts.guest')

@section('title', 'Login - Cetak Ijazah Universitas Royal')

@section('content')
@php
    $logoPath = \App\Models\SystemSetting::get('logo_path');
    $logoUrl = ($logoPath && file_exists(public_path($logoPath))) ? asset($logoPath) : asset('images/logo-univ.png');
    $univName = \App\Models\SystemSetting::get('university_name', \App\Models\SystemSetting::get('institution_name', 'Universitas Royal'));
@endphp

<div class="min-h-screen flex">
    
    <!-- LEFT PANEL: LOGIN FORM (60% on large screens or centered) -->
    <div class="w-full lg:w-5/12 flex flex-col justify-center px-6 sm:px-12 md:px-20 lg:px-16 xl:px-24 bg-white relative z-10">
        
        <!-- Top Bar mobile header branding -->
        <div class="absolute top-8 left-8 flex items-center space-x-3 lg:hidden">
            <div class="flex items-center justify-center w-9 h-9 rounded-xl bg-slate-50 border border-slate-200/80 p-1.5 shadow-xs">
                <img src="{{ $logoUrl }}" alt="Logo {{ $univName }}" class="w-full h-full object-contain">
            </div>
            <span class="text-sm font-bold text-slate-900 uppercase tracking-wider">{{ $univName }}</span>
        </div>

        <!-- Form Card Container -->
        <div class="w-full max-w-md mx-auto">
            
            <!-- Logo & Title -->
            <div class="mb-10 text-center lg:text-left">
                <!-- Desktop brand logo -->
                <div class="hidden lg:flex items-center space-x-4 mb-6">
                    <div class="flex items-center justify-center w-14 h-14 rounded-2xl bg-slate-50 border border-slate-200/80 p-2 shadow-xs shrink-0">
                        <img src="{{ $logoUrl }}" alt="Logo {{ $univName }}" class="w-full h-full object-contain">
                    </div>
                    <div>
                        <h2 class="text-lg font-extrabold text-slate-950 uppercase tracking-wide leading-tight">{{ $univName }}</h2>
                        <span class="text-xs font-semibold text-gold-600 uppercase tracking-widest block">Cetak Ijazah</span>
                    </div>
                </div>

                <h1 class="text-3xl font-extrabold text-slate-950 tracking-tight">Selamat Datang</h1>
                <p class="text-slate-500 mt-2 text-sm">Gunakan akun akademik resmi untuk mengakses sistem pencetakan dokumen.</p>
            </div>

            <!-- Login Form -->
            <form action="{{ route('login') }}" method="POST" class="space-y-6">
                @csrf

                <!-- Username (NIM / ID) -->
                <div>
                    <label for="username" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">NIM / ID Akademik</label>
                    <div class="relative rounded-xl shadow-xs">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <i data-lucide="user" class="h-5 w-5 text-slate-400"></i>
                        </div>
                        <input type="text" 
                               name="username" 
                               id="username" 
                               value="{{ old('username') }}"
                               placeholder="Masukkan NIM / ID Akademik"
                               class="block w-full pl-11 pr-4 py-3.5 border rounded-xl text-slate-900 text-sm focus:outline-none focus:ring-4 transition-all {{ $errors->has('username') ? 'border-red-300 bg-red-50/20 focus:ring-red-500/10 focus:border-red-500' : 'border-slate-200 bg-slate-50/50 focus:ring-royal-500/10 focus:border-royal-500' }}"
                               required 
                               autofocus>
                    </div>
                    @error('username')
                        <p class="mt-2 text-xs font-medium text-red-600 flex items-center">
                            <i data-lucide="alert-circle" class="w-4 h-4 mr-1"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Password -->
                <div>
                    <div class="flex justify-between items-center mb-2">
                        <label for="password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Kata Sandi</label>
                    </div>
                    <div class="relative rounded-xl shadow-xs">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <i data-lucide="lock" class="h-5 w-5 text-slate-400"></i>
                        </div>
                        <input type="password" 
                               name="password" 
                               id="password" 
                               placeholder="Masukkan kata sandi"
                               class="block w-full pl-11 pr-4 py-3.5 border rounded-xl text-slate-900 text-sm focus:outline-none focus:ring-4 transition-all {{ $errors->has('password') ? 'border-red-300 bg-red-50/20 focus:ring-red-500/10 focus:border-red-500' : 'border-slate-200 bg-slate-50/50 focus:ring-royal-500/10 focus:border-royal-500' }}"
                               required>
                    </div>
                    @error('password')
                        <p class="mt-2 text-xs font-medium text-red-600 flex items-center">
                            <i data-lucide="alert-circle" class="w-4 h-4 mr-1"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Remember Me & Extra Info -->
                <div class="flex items-center justify-between py-1">
                    <div class="flex items-center">
                        <input id="remember" 
                               name="remember" 
                               type="checkbox" 
                               class="h-4 w-4 text-royal-600 border-slate-300 rounded-md focus:ring-royal-500/30 transition-colors">
                        <label for="remember" class="ml-2.5 block text-sm font-medium text-slate-600 select-none">
                            Ingat Saya
                        </label>
                    </div>
                    <span class="text-xs text-slate-400 cursor-help hover:text-slate-600 transition-colors flex items-center" 
                          title="Hubungi Biro Akademik jika Anda lupa password atau akun Anda terkunci.">
                        <i data-lucide="help-circle" class="w-4 h-4 mr-1"></i>
                        Bantuan
                    </span>
                </div>

                <!-- Submit Button -->
                <div>
                    <button type="submit" 
                            class="group w-full flex justify-center items-center py-4 px-5 border border-transparent rounded-xl text-sm font-bold text-white bg-royal-600 hover:bg-royal-700 focus:outline-none focus:ring-4 focus:ring-royal-500/30 active:scale-[0.98] shadow-lg shadow-royal-600/10 hover:shadow-royal-700/20 transition-all duration-200">
                        Masuk ke Akun
                        <i data-lucide="arrow-right" class="w-4 h-4 ml-2 transition-transform duration-200 group-hover:translate-x-1"></i>
                    </button>
                </div>

                <!-- Informational Notice -->
                <div class="mt-5 flex items-start space-x-2 p-3 bg-slate-50 rounded-xl border border-slate-100/80">
                    <i data-lucide="info" class="w-4 h-4 text-slate-400 shrink-0 mt-0.5"></i>
                    <p class="text-xxs text-slate-400 leading-normal font-medium">
                        Pastikan data mahasiswa telah diverifikasi sebelum proses pencetakan dokumen dilakukan.
                    </p>
                </div>
            </form>

            <!-- Institutional Disclaimer Footer for Mobile -->
            <div class="mt-16 text-center text-xs text-slate-400 lg:hidden">
                <p>&copy; {{ date('Y') }} Universitas Royal. All rights reserved.</p>
            </div>

        </div>
    </div>

    <!-- RIGHT PANEL: FULLSCREEN BRANDING (7/12 width, hidden on mobile) -->
    <div class="hidden lg:block lg:w-7/12 relative bg-slate-950 overflow-hidden">
        
        <!-- High-quality university academic building image -->
        <img class="absolute inset-0 w-full h-full object-cover opacity-60 scale-105 hover:scale-100 transition-transform duration-[10000ms] ease-out" 
             src="https://images.unsplash.com/photo-1541339907198-e08756dedf3f?auto=format&fit=crop&q=80&w=1200" 
             alt="Universitas Royal Campus">
        
        <!-- Gradient Overlay matching Brand Colors -->
        <div class="absolute inset-0 bg-gradient-to-tr from-slate-950 via-slate-900/90 to-royal-950/60 mix-blend-multiply"></div>
        
        <!-- Grid/Ring Graphic Overlay for premium look -->
        <div class="absolute inset-0 flex items-center justify-center opacity-15">
            <div class="w-[600px] h-[600px] rounded-full border border-gold-500 animate-spin" style="animation-duration: 120s;"></div>
            <div class="absolute w-[450px] h-[450px] rounded-full border border-dashed border-royal-500 animate-spin" style="animation-duration: 90s; animation-direction: reverse;"></div>
        </div>

        <!-- Branding Text & Motivational Tagline -->
        <div class="absolute inset-0 flex flex-col justify-between p-16 xl:p-24 z-10 text-white">
            
            <!-- Top brand text header -->
            <div class="flex items-center space-x-3 self-start">
                <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-royal-600/30 border border-royal-400/20 backdrop-blur-xs text-white">
                    <i data-lucide="graduation-cap" class="w-5 h-5 text-gold-400"></i>
                </div>
                <span class="text-sm font-semibold tracking-widest uppercase">Portal Resmi Universitas Royal</span>
            </div>

            <!-- Bottom content info -->
            <div class="max-w-xl">
                <div class="inline-flex items-center px-3 py-1 rounded-full text-xxs font-bold bg-gold-500/10 border border-gold-500/20 text-gold-400 uppercase tracking-widest mb-6">
                    <i data-lucide="award" class="w-3.5 h-3.5 mr-1.5"></i>
                    Portal Administrasi Akademik
                </div>
                <h3 class="text-4xl xl:text-5xl font-black leading-tight tracking-tight">Sistem Manajemen Cetak Ijazah & Dokumen Akademik</h3>
                <p class="text-slate-300 mt-6 text-base xl:text-lg leading-relaxed">
                    Platform resmi Universitas Royal untuk pengelolaan, validasi, dan pencetakan dokumen akademik secara aman dan terstruktur.
                </p>
            </div>

            <!-- Institutional Footer -->
            <div class="flex justify-between items-center text-xs text-slate-400 border-t border-slate-800/80 pt-6">
                <span>&copy; {{ date('Y') }} Universitas Royal. Seluruh Hak Cipta Dilindungi.</span>
                <span class="flex items-center space-x-2">
                    <span class="h-2 w-2 rounded-full bg-emerald-500 inline-block animate-pulse"></span>
                    <span>Layanan Sistem Aktif</span>
                </span>
            </div>

        </div>

    </div>

</div>
@endsection
