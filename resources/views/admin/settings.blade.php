@extends('layouts.app')

@section('title', 'Pengaturan Sistem - Royal Academic Print Suite')
@section('page-title', 'Pengaturan Sistem')
@section('page-subtitle', 'Konfigurasi identitas institusi, penandatangan ijazah resmi, dan parameter log audit.')

@section('content')
<!-- Success / Error Feedback Alerts -->
@if(session('success'))
    <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl flex items-center space-x-3 shadow-2xs animate-fade-in">
        <i data-lucide="check-circle" class="w-5 h-5 text-emerald-600 shrink-0"></i>
        <div class="text-sm font-semibold">{{ session('success') }}</div>
    </div>
@endif

@if($errors->any())
    <div class="mb-6 p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-xl shadow-2xs">
        <div class="flex items-center space-x-3 mb-2">
            <i data-lucide="alert-triangle" class="w-5 h-5 text-rose-600 shrink-0"></i>
            <div class="text-sm font-bold">Terjadi Kesalahan Validasi:</div>
        </div>
        <ul class="list-disc list-inside text-xs space-y-1 text-rose-700 pl-2">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<!-- Settings Panel with Alpine JS Tab state -->
<div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden" x-data="{ activeTab: new URLSearchParams(window.location.search).get('tab') || 'institution' }">
    
    <!-- Settings Tab Navigation Header -->
    <div class="border-b border-slate-200 bg-slate-50/50 flex flex-wrap px-6">
        <button @click="activeTab = 'institution'" 
                :class="activeTab === 'institution' ? 'border-royal-600 text-royal-600 font-extrabold' : 'border-transparent text-slate-500 hover:text-slate-800 hover:border-slate-300 font-semibold'"
                class="py-4 px-5 border-b-2 text-sm transition-all focus:outline-hidden inline-flex items-center space-x-2">
            <i data-lucide="graduation-cap" class="w-4 h-4"></i>
            <span>Identitas & Pejabat Akademik</span>
        </button>
        <button @click="activeTab = 'system'" 
                :class="activeTab === 'system' ? 'border-royal-600 text-royal-600 font-extrabold' : 'border-transparent text-slate-500 hover:text-slate-800 hover:border-slate-300 font-semibold'"
                class="py-4 px-5 border-b-2 text-sm transition-all focus:outline-hidden inline-flex items-center space-x-2">
            <i data-lucide="shield-check" class="w-4 h-4"></i>
            <span>Kebijakan Log & Keamanan</span>
        </button>
        <button @click="activeTab = 'security'" 
                :class="activeTab === 'security' ? 'border-royal-600 text-royal-600 font-extrabold' : 'border-transparent text-slate-500 hover:text-slate-800 hover:border-slate-300 font-semibold'"
                class="py-4 px-5 border-b-2 text-sm transition-all focus:outline-hidden inline-flex items-center space-x-2">
            <i data-lucide="key-round" class="w-4 h-4"></i>
            <span>Ubah Kata Sandi</span>
        </button>
    </div>

    <!-- Main Settings Form -->
    <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data" class="p-6 sm:p-8 space-y-6">
        @csrf

        <!-- TAB 1: INSTITUTION & OFFICIALS CONFIGURATION -->
        <div x-show="activeTab === 'institution'" class="space-y-6" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0">
            
            <div class="border-b border-slate-100 pb-4">
                <h3 class="text-base font-bold text-slate-900">Identitas Universitas</h3>
                <p class="text-xs text-slate-400 mt-1">Konfigurasi dasar nama institusi dan logo universitas resmi yang akan tercetak di ijazah.</p>
            </div>

            <!-- Logo Upload & Institution Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- University Logo Upload Widget -->
                <div class="bg-slate-50 p-6 rounded-2xl border border-slate-200/80 flex flex-col items-center justify-center text-center">
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-4">Logo Institusi Resmi</label>
                    
                    <div class="relative w-28 h-28 bg-white border border-slate-200 rounded-2xl flex items-center justify-center overflow-hidden shadow-xs group mb-4">
                        @php
                            $logoPath = \App\Models\SystemSetting::get('logo_path');
                            $logoUrl = ($logoPath && file_exists(public_path($logoPath))) ? asset($logoPath) : (file_exists(public_path('images/logo-univ.png')) ? asset('images/logo-univ.png') : null);
                        @endphp
                        
                        @if($logoUrl)
                            <img id="logo-preview" src="{{ $logoUrl }}" alt="Logo Universitas" class="w-full h-full object-contain p-2">
                            <div id="logo-placeholder" class="text-slate-400 flex flex-col items-center hidden">
                                <i data-lucide="image" class="w-8 h-8 text-slate-300"></i>
                                <span class="text-xxs mt-1">Belum Ada</span>
                            </div>
                        @else
                            <div id="logo-placeholder" class="text-slate-400 flex flex-col items-center">
                                <i data-lucide="image" class="w-8 h-8 text-slate-300"></i>
                                <span class="text-xxs mt-1">Belum Ada</span>
                            </div>
                            <img id="logo-preview" class="w-full h-full object-contain p-2 hidden">
                        @endif
                    </div>

                    <!-- File input custom trigger -->
                    <div class="w-full">
                        <label for="logo-input" class="w-full px-4 py-2 border border-slate-200 hover:bg-slate-100 text-slate-700 hover:text-slate-900 font-bold text-xs rounded-lg transition-colors inline-flex items-center justify-center cursor-pointer">
                            <i data-lucide="upload-cloud" class="w-3.5 h-3.5 mr-1.5 text-slate-500"></i>
                            Pilih Logo Baru
                        </label>
                        <input id="logo-input" type="file" name="logo" class="hidden" accept="image/png, image/jpg, image/jpeg" onchange="previewImage(this)">
                        <p class="text-xxs text-slate-400 mt-2">Mendukung PNG, JPG, JPEG. Maksimal 2MB.</p>
                    </div>
                </div>

                <!-- Text Field Inputs (Institution Details) -->
                <div class="md:col-span-2 space-y-4">
                    <!-- Institution Name Input -->
                    <div>
                        <label for="institution_name" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2 flex items-center">
                            <i data-lucide="building" class="w-4 h-4 mr-1.5 text-slate-400"></i>
                            Nama Institusi / Universitas
                        </label>
                        <input type="text" 
                               id="institution_name"
                               name="institution_name" 
                               value="{{ old('institution_name', \App\Models\SystemSetting::get('institution_name', 'Universitas Royal')) }}"
                               placeholder="Contoh: Universitas Royal Sumatra"
                               class="w-full px-3 py-2.5 border border-slate-200 rounded-lg text-sm bg-slate-50 focus:bg-white focus:outline-hidden focus:ring-2 focus:ring-royal-500/20 focus:border-royal-500 transition-all font-semibold text-slate-900" 
                               required>
                    </div>

                    <!-- Informational Callout -->
                    <div class="p-4 bg-royal-50 border border-royal-100 rounded-xl flex items-start space-x-3 text-royal-800 text-xs">
                        <i data-lucide="info" class="w-4 h-4 text-royal-600 shrink-0 mt-0.5"></i>
                        <div class="leading-relaxed">
                            <strong class="font-bold">Info Integrasi:</strong> Data universitas dan nama pejabat di bawah ini akan diinjeksikan secara dinamis pada template ijazah digital dan sistem generator PDF saat Biro Akademik mencetak dokumen ijazah mahasiswa secara resmi.
                        </div>
                    </div>
                </div>
            </div>

            <!-- Academic Officials Signatories -->
            <div class="border-b border-slate-100 pb-4 pt-4">
                <h3 class="text-base font-bold text-slate-900">Pejabat Penandatangan Ijazah</h3>
                <p class="text-xs text-slate-400 mt-1">Konfigurasi nama lengkap beserta Nomor Induk Pegawai (NIP) Rektor dan Dekan yang bertanda tangan resmi.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Rector Column Info -->
                <div class="bg-slate-50/50 p-5 rounded-2xl border border-slate-200/60 space-y-4">
                    <div class="flex items-center space-x-2 text-royal-800 border-b border-slate-200/50 pb-2.5">
                        <i data-lucide="award" class="w-5 h-5 text-royal-600"></i>
                        <h4 class="text-sm font-bold">Rektor Universitas</h4>
                    </div>

                    <!-- Rector Name -->
                    <div>
                        <label for="rector_name" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Nama Lengkap Rektor (dengan Gelar)</label>
                        <input type="text" 
                               id="rector_name"
                               name="rector_name" 
                               value="{{ old('rector_name', \App\Models\SystemSetting::get('rector_name', 'Prof. Dr. Muchtarinal Choiri, M.T.')) }}"
                               placeholder="Contoh: Prof. Dr. Ir. H. Ahmad Wijaya, M.Sc."
                               class="w-full px-3 py-2.5 border border-slate-200 rounded-lg text-sm bg-white focus:outline-hidden focus:ring-2 focus:ring-royal-500/20 focus:border-royal-500 transition-all font-semibold text-slate-900" 
                               required>
                    </div>

                    <!-- Rector NIP -->
                    <div>
                        <label for="rector_nip" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">NIP Rektor</label>
                        <input type="text" 
                               id="rector_nip"
                               name="rector_nip" 
                               value="{{ old('rector_nip', \App\Models\SystemSetting::get('rector_nip', '197508212002121001')) }}"
                               placeholder="Masukkan NIP Rektor..."
                               class="w-full px-3 py-2.5 border border-slate-200 rounded-lg text-sm bg-white focus:outline-hidden focus:ring-2 focus:ring-royal-500/20 focus:border-royal-500 transition-all font-mono text-slate-900" 
                               required>
                    </div>
                </div>

                <!-- Dean Column Info -->
                <div class="bg-slate-50/50 p-5 rounded-2xl border border-slate-200/60 space-y-4">
                    <div class="flex items-center space-x-2 text-royal-800 border-b border-slate-200/50 pb-2.5">
                        <i data-lucide="book-open" class="w-5 h-5 text-royal-600"></i>
                        <h4 class="text-sm font-bold">Dekan Fakultas</h4>
                    </div>

                    <!-- Dean Name -->
                    <div>
                        <label for="dean_name" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Nama Lengkap Dekan (dengan Gelar)</label>
                        <input type="text" 
                               id="dean_name"
                               name="dean_name" 
                               value="{{ old('dean_name', \App\Models\SystemSetting::get('dean_name', 'Dr. Ir. Rina Kartika, M.Kom.')) }}"
                               placeholder="Contoh: Dr. H. Faisal Rahman, M.Si."
                               class="w-full px-3 py-2.5 border border-slate-200 rounded-lg text-sm bg-white focus:outline-hidden focus:ring-2 focus:ring-royal-500/20 focus:border-royal-500 transition-all font-semibold text-slate-900" 
                               required>
                    </div>

                    <!-- Dean NIP -->
                    <div>
                        <label for="dean_nip" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">NIP Dekan</label>
                        <input type="text" 
                               id="dean_nip"
                               name="dean_nip" 
                               value="{{ old('dean_nip', \App\Models\SystemSetting::get('dean_nip', '198104152008012002')) }}"
                               placeholder="Masukkan NIP Dekan..."
                               class="w-full px-3 py-2.5 border border-slate-200 rounded-lg text-sm bg-white focus:outline-hidden focus:ring-2 focus:ring-royal-500/20 focus:border-royal-500 transition-all font-mono text-slate-900" 
                               required>
                    </div>
                </div>
            </div>

        </div>

        <!-- TAB 2: SYSTEM RETENTION & SECURITY CONFIGURATION -->
        <div x-show="activeTab === 'system'" class="space-y-6" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" style="display: none;">
            
            <div class="border-b border-slate-100 pb-4">
                <h3 class="text-base font-bold text-slate-900">Kebijakan Retensi Data & Log Keamanan</h3>
                <p class="text-xs text-slate-400 mt-1">Konfigurasi batas waktu penyimpanan log audit keamanan sebelum dihapus secara otomatis demi efisiensi sistem.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Retention Days Config Card -->
                <div class="md:col-span-2 space-y-4">
                    <div>
                        <label for="log_retention_days" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2 flex items-center">
                            <i data-lucide="clock" class="w-4 h-4 mr-1.5 text-slate-400"></i>
                            Batas Penyimpanan Log Aktivitas (Hari)
                        </label>
                        <div class="relative max-w-xs rounded-md shadow-2xs">
                            <input type="number" 
                                   id="log_retention_days"
                                   name="log_retention_days" 
                                   value="{{ old('log_retention_days', \App\Models\SystemSetting::get('log_retention_days', 90)) }}"
                                   min="7"
                                   max="365"
                                   class="w-full pl-3 pr-16 py-2.5 border border-slate-200 rounded-lg text-sm bg-slate-50 focus:bg-white focus:outline-hidden focus:ring-2 focus:ring-royal-500/20 focus:border-royal-500 transition-all font-mono font-bold text-slate-900" 
                                   required>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <span class="text-xs font-semibold text-slate-400 uppercase">Hari</span>
                            </div>
                        </div>
                        <p class="text-xxs text-slate-400 mt-1.5 leading-relaxed">
                            Log keamanan akan otomatis disimpan selama jumlah hari ini. Batas minimal adalah <strong class="text-royal-600 font-bold">7 hari</strong> dan maksimal <strong class="text-royal-600 font-bold">365 hari</strong>.
                        </p>
                    </div>

                    <!-- Warning Security callout -->
                    <div class="p-4 bg-amber-50 border border-amber-200 rounded-xl flex items-start space-x-3 text-amber-800 text-xs">
                        <i data-lucide="shield-alert" class="w-4 h-4 text-amber-600 shrink-0 mt-0.5"></i>
                        <div class="leading-relaxed">
                            <strong class="font-bold">Rekomendasi Keamanan:</strong> Berdasarkan kebijakan audit standar, log aktivitas administratif disarankan disimpan setidaknya selama <strong class="font-bold">90 hari</strong> sebelum dilakukan siklus pembersihan manual atau otomatis untuk menjaga pertanggungjawaban data.
                        </div>
                    </div>
                </div>

                <!-- Database stats widget -->
                <div class="bg-slate-50 p-5 rounded-2xl border border-slate-200/80 space-y-4">
                    <h4 class="text-xs font-bold text-slate-500 uppercase tracking-wider border-b border-slate-200 pb-2 flex items-center">
                        <i data-lucide="database" class="w-4 h-4 mr-1 text-slate-400"></i>
                        Statistik Penyimpanan
                    </h4>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center text-xs">
                            <span class="text-slate-400">Total Baris Log:</span>
                            <strong class="text-slate-800 font-mono">{{ \App\Models\ActivityLog::count() }}</strong>
                        </div>
                        <div class="flex justify-between items-center text-xs">
                            <span class="text-slate-400">Riwayat Cetak:</span>
                            <strong class="text-slate-800 font-mono">{{ \App\Models\PrintHistory::count() }}</strong>
                        </div>
                        <div class="flex justify-between items-center text-xs border-t border-slate-200/60 pt-2">
                            <span class="text-slate-400">Kesehatan Database:</span>
                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xxs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 uppercase tracking-wider">Optimal</span>
                        </div>
                    </div>

                    <div class="pt-2">
                        <a href="{{ route('admin.logs') }}" class="w-full px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 hover:text-slate-900 font-bold text-xs rounded-lg transition-all inline-flex items-center justify-center border border-slate-200">
                            <i data-lucide="external-link" class="w-3.5 h-3.5 mr-1 text-slate-500"></i>
                            Buka Audit Trail
                        </a>
                    </div>
                </div>
            </div>

        </div>

        <!-- Form Submission footer button -->
        <div class="flex items-center justify-end pt-5 border-t border-slate-100">
            <button type="submit" class="px-6 py-3 bg-royal-600 hover:bg-royal-700 text-white font-extrabold text-xs rounded-xl transition-all inline-flex items-center shadow-md shadow-royal-900/10 hover:shadow-lg">
                <i data-lucide="save" class="w-4 h-4 mr-2"></i>
                Simpan Seluruh Pengaturan
            </button>
        </div>

    </form>

    <!-- TAB 3: ADMIN SECURITY / CHANGE PASSWORD FORM -->
    <form action="{{ route('profile.change-password') }}" method="POST" x-show="activeTab === 'security'" class="p-6 sm:p-8 space-y-6" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" style="display: none;">
        @csrf
        
        <div class="border-b border-slate-100 pb-4">
            <h3 class="text-base font-bold text-slate-900">Ubah Kata Sandi Akun</h3>
            <p class="text-xs text-slate-400 mt-1">Perbarui kata sandi akun administratif Anda secara berkala untuk menjaga pertahanan keamanan sistem.</p>
        </div>

        <div class="max-w-xl space-y-4">
            <!-- Current Password -->
            <div>
                <label for="current_password" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Kata Sandi Saat Ini</label>
                <input type="password" 
                       id="current_password"
                       name="current_password" 
                       required
                       class="w-full px-3 py-2.5 border border-slate-200 rounded-lg text-sm bg-slate-50 focus:bg-white focus:outline-hidden focus:ring-2 focus:ring-royal-500/20 focus:border-royal-500 transition-all font-semibold text-slate-900">
            </div>

            <!-- New Password -->
            <div>
                <label for="new_password" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Kata Sandi Baru</label>
                <input type="password" 
                       id="new_password"
                       name="new_password" 
                       required
                       class="w-full px-3 py-2.5 border border-slate-200 rounded-lg text-sm bg-slate-50 focus:bg-white focus:outline-hidden focus:ring-2 focus:ring-royal-500/20 focus:border-royal-500 transition-all font-semibold text-slate-900">
            </div>

            <!-- New Password Confirmation -->
            <div>
                <label for="new_password_confirmation" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Konfirmasi Kata Sandi Baru</label>
                <input type="password" 
                       id="new_password_confirmation"
                       name="new_password_confirmation" 
                       required
                       class="w-full px-3 py-2.5 border border-slate-200 rounded-lg text-sm bg-slate-50 focus:bg-white focus:outline-hidden focus:ring-2 focus:ring-royal-500/20 focus:border-royal-500 transition-all font-semibold text-slate-900">
            </div>
        </div>

        <!-- Form Submission footer button -->
        <div class="flex items-center justify-end pt-5 border-t border-slate-100">
            <button type="submit" class="px-6 py-3 bg-slate-900 hover:bg-slate-800 text-white font-extrabold text-xs rounded-xl transition-all inline-flex items-center shadow-md shadow-slate-900/10 hover:shadow-lg cursor-pointer">
                <i data-lucide="key-round" class="w-4 h-4 mr-2 text-gold-400"></i>
                Perbarui Kata Sandi
            </button>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
    // Live preview of uploaded logo
    function previewImage(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                const preview = document.getElementById('logo-preview');
                const placeholder = document.getElementById('logo-placeholder');
                
                preview.src = e.target.result;
                preview.classList.remove('hidden');
                
                if (placeholder) {
                    placeholder.classList.add('hidden');
                }
            }
            
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endsection
