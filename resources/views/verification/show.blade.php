<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Ijazah Resmi - Universitas Royal</title>
    
    <!-- Premium Fonts -->
    <link href="https://fonts.bunny.net/css?family=eb-garamond:400,500,600,700,800|outfit:300,400,500,600,700,800" rel="stylesheet" />
    
    <!-- Tailwind CSS Play CDN for beautiful aesthetics -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        royal: {
                            50: '#f0f4ff',
                            100: '#e1ebff',
                            200: '#cbe0ff',
                            300: '#a8cafe',
                            400: '#7eabfc',
                            500: '#5685fa',
                            600: '#345df1',
                            700: '#1d4ed8', // Brand primary
                            800: '#1e3fa7',
                            900: '#1e3884',
                            950: '#111e50',
                        },
                        gold: {
                            50: '#fefce8',
                            100: '#fef9c3',
                            200: '#fef08a',
                            300: '#fde047',
                            400: '#facc15',
                            500: '#eab308', // Accent
                            600: '#ca8a04',
                            700: '#a16207',
                            850: '#854d0e',
                            900: '#713f12',
                        }
                    },
                    fontFamily: {
                        sans: ['Outfit', 'sans-serif'],
                        serif: ['EB Garamond', 'serif'],
                    }
                }
            }
        }
    </script>
    
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <style>
        .verified-glow {
            box-shadow: 0 0 40px -10px rgba(16, 185, 129, 0.25);
        }
        .unverified-glow {
            box-shadow: 0 0 40px -10px rgba(239, 68, 68, 0.25);
        }
        .stamp-rotation {
            transform: rotate(-12deg);
        }
    </style>
</head>
<body class="h-full font-sans antialiased text-slate-800 flex flex-col justify-between">

    <!-- Top Branding Navbar -->
    <header class="bg-slate-900 border-b border-slate-800 text-white shadow-md z-10 py-5">
        <div class="max-w-4xl mx-auto px-6 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-royal-700 text-white border border-royal-500/20">
                    <i data-lucide="graduation-cap" class="w-6 h-6 text-gold-400"></i>
                </div>
                <div>
                    <h1 class="text-sm font-bold tracking-wide uppercase">Sistem Verifikasi Ijazah</h1>
                    <span class="text-xxs font-extrabold text-gold-500 uppercase tracking-widest block mt-0.5">Universitas Royal</span>
                </div>
            </div>
            <div class="text-right hidden sm:block">
                <span class="px-3 py-1 bg-slate-800 border border-slate-700 text-slate-300 rounded-full text-xxs font-semibold flex items-center">
                    <span class="h-2 w-2 rounded-full bg-emerald-500 mr-2"></span>
                    Sistem Valid 2026
                </span>
            </div>
        </div>
    </header>

    <!-- Main Content Container -->
    <main class="flex-1 flex items-center justify-center py-12 px-6">
        <div class="max-w-xl w-full">
            
            @if($isValid)
                <!-- VERIFIED SUCCESS CARD -->
                <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xl overflow-hidden verified-glow transition-all duration-300">
                    <!-- Premium Success Header -->
                    <div class="bg-gradient-to-r from-emerald-600 to-teal-600 p-8 text-center text-white relative">
                        <div class="absolute right-4 top-4 opacity-10 pointer-events-none">
                            <i data-lucide="shield-check" class="w-36 h-36"></i>
                        </div>
                        <div class="w-16 h-16 rounded-full bg-white/10 backdrop-blur-md border border-white/20 flex items-center justify-center mx-auto mb-4 animate-bounce">
                            <i data-lucide="shield-check" class="w-8 h-8 text-white"></i>
                        </div>
                        <span class="px-3 py-0.5 text-xxs font-extrabold uppercase tracking-widest bg-emerald-950/40 text-emerald-100 rounded-full border border-emerald-450/30">
                            Validasi Akademik Resmi
                        </span>
                        <h2 class="text-xl font-black mt-3 uppercase tracking-wider">Ijazah Terverifikasi Asli</h2>
                        <p class="text-emerald-100 text-xs mt-1">Dokumen ini telah resmi diterbitkan oleh Universitas Royal dan terdaftar di database nasional.</p>
                    </div>

                    <!-- Certified Seal / Stamp Element -->
                    <div class="relative px-8 pt-8 pb-4 flex justify-between items-start border-b border-slate-100 bg-slate-50/50">
                        <div>
                            <span class="text-xxs font-bold text-slate-400 uppercase tracking-widest">Nomor Ijazah</span>
                            <p class="font-mono text-base font-bold text-slate-800 tracking-tight mt-0.5">
                                {{ $student->certificate_number }}
                            </p>
                        </div>
                        <!-- Virtual Seal -->
                        <div class="stamp-rotation border-4 border-emerald-600/30 rounded-full px-3 py-1.5 flex items-center bg-white shadow-sm shrink-0">
                            <i data-lucide="award" class="w-4 h-4 text-emerald-600 mr-1.5 animate-spin-slow"></i>
                            <span class="text-xxs font-black text-emerald-600 tracking-widest uppercase">ROYAL SECURE</span>
                        </div>
                    </div>

                    <!-- Student Credentials Grid -->
                    <div class="p-8 space-y-6">
                        <div class="grid grid-cols-2 gap-x-6 gap-y-5 text-sm">
                            
                            <div class="col-span-2 pb-3 border-b border-slate-150/60">
                                <span class="text-xxs font-bold text-slate-400 uppercase tracking-wider block">Nama Lengkap Mahasiswa</span>
                                <span class="font-serif font-bold text-slate-900 text-lg leading-tight block mt-0.5">
                                    {{ $student->user->name }}
                                </span>
                            </div>

                            <div class="pb-3 border-b border-slate-150/60">
                                <span class="text-xxs font-bold text-slate-400 uppercase tracking-wider block">Nomor Induk Mahasiswa</span>
                                <span class="font-mono font-bold text-slate-800 text-sm block mt-0.5">
                                    {{ $student->nim }}
                                </span>
                            </div>

                            <div class="pb-3 border-b border-slate-150/60">
                                <span class="text-xxs font-bold text-slate-400 uppercase tracking-wider block">Program Studi</span>
                                <span class="font-bold text-slate-800 text-sm block mt-0.5">
                                    {{ $student->major }}
                                </span>
                            </div>

                            <div class="pb-3 border-b border-slate-150/60">
                                <span class="text-xxs font-bold text-slate-400 uppercase tracking-wider block">Fakultas</span>
                                <span class="font-semibold text-slate-800 text-sm block mt-0.5">
                                    {{ $student->faculty }}
                                </span>
                            </div>

                            <div class="pb-3 border-b border-slate-150/60">
                                <span class="text-xxs font-bold text-slate-400 uppercase tracking-wider block">Gelar Akademik</span>
                                <span class="font-bold text-gold-700 text-sm block mt-0.5 uppercase tracking-wide">
                                    Sarjana {{ $student->degree ?: 'Sarjana' }}
                                </span>
                            </div>

                            <div class="pb-3 border-b border-slate-150/60 col-span-2">
                                <div class="flex justify-between items-center">
                                    <div>
                                        <span class="text-xxs font-bold text-slate-400 uppercase tracking-wider block">Indeks Prestasi Kumulatif (IPK)</span>
                                        <span class="font-black text-slate-900 text-lg block mt-0.5">
                                            {{ number_format($student->gpa, 2) }}
                                        </span>
                                    </div>
                                    <div class="text-right">
                                        <span class="text-xxs font-bold text-slate-400 uppercase tracking-wider block">Predikat Kelulusan</span>
                                        <span class="text-xs px-2.5 py-1 bg-gold-50 border border-gold-150 text-gold-700 rounded-full font-bold inline-block mt-0.5 uppercase">
                                            {{ $student->predicate }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-span-2 pb-1 text-center bg-slate-50 rounded-xl p-3.5 border border-slate-100">
                                <span class="text-xxs font-bold text-slate-400 uppercase tracking-wider block">Tanggal Kelulusan Resmi</span>
                                <span class="font-semibold text-slate-800 text-xs block mt-1">
                                    @if($student->graduation_date)
                                        {{ $student->graduation_date->translatedFormat('d F Y') }}
                                    @else
                                        {{ now()->translatedFormat('d F Y') }}
                                    @endif
                                </span>
                            </div>

                        </div>

                        <!-- Footer disclaimer inside card -->
                        <div class="p-4 bg-emerald-50 border border-emerald-100 rounded-2xl flex items-start text-xxs text-emerald-800 leading-relaxed">
                            <i data-lucide="info" class="w-4 h-4 text-emerald-600 mr-2 shrink-0 mt-0.5"></i>
                            <span>Ijazah ini ditandatangani secara elektronik menggunakan infrastruktur kunci publik kriptografi terenkripsi tinggi Universitas Royal. Modifikasi visual apa pun terhadap berkas fisik tanpa rekonsiliasi data ini membatalkan keaslian dokumen.</span>
                        </div>
                    </div>
                </div>

            @else
                <!-- UNVERIFIED FAILED CARD -->
                <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xl overflow-hidden unverified-glow transition-all duration-300">
                    <!-- Red Warning Header -->
                    <div class="bg-gradient-to-r from-red-600 to-rose-600 p-8 text-center text-white relative">
                        <div class="absolute right-4 top-4 opacity-10 pointer-events-none">
                            <i data-lucide="shield-x" class="w-36 h-36"></i>
                        </div>
                        <div class="w-16 h-16 rounded-full bg-white/10 backdrop-blur-md border border-white/20 flex items-center justify-center mx-auto mb-4">
                            <i data-lucide="shield-alert" class="w-8 h-8 text-white"></i>
                        </div>
                        <span class="px-3 py-0.5 text-xxs font-extrabold uppercase tracking-widest bg-red-950/40 text-red-100 rounded-full border border-red-450/30">
                            Validasi Akademik Gagal
                        </span>
                        <h2 class="text-xl font-black mt-3 uppercase tracking-wider">Dokumen Tidak Valid</h2>
                        <p class="text-red-100 text-xs mt-1">Data ijazah yang dicari tidak lolos penapisan keamanan digital otomatis.</p>
                    </div>

                    <!-- Error details -->
                    <div class="p-8 space-y-6 text-center">
                        <div class="p-6 bg-red-50 border border-red-100 rounded-2xl">
                            <span class="text-xxs font-extrabold text-red-600 uppercase tracking-widest block mb-1.5">Alasan Penolakan Sistem</span>
                            <p class="text-sm font-semibold text-slate-800 leading-relaxed">
                                {{ $rejectReason }}
                            </p>
                        </div>

                        <div class="text-xs text-slate-500 leading-relaxed text-left space-y-3 p-2">
                            <h4 class="font-bold text-slate-700 flex items-center">
                                <i data-lucide="shield" class="w-4 h-4 text-royal-600 mr-2"></i>
                                Protokol Penanganan Keamanan Dokumen:
                            </h4>
                            <ul class="list-disc pl-5 space-y-1.5 text-xxs text-slate-400">
                                <li>Pastikan tautan QR Code dipindai langsung dari dokumen ijazah resmi yang dicetak oleh Universitas Royal.</li>
                                <li>Pencetakan ulang tidak sah atau perubahan data fisik/digital secara ilegal akan memicu penolakan verifikasi sistem.</li>
                                <li>Apabila ini merupakan kesalahan teknis sistem pencatatan akademik, harap hubungi Biro Administrasi Akademik & Kemahasiswaan (BAAK) secara langsung untuk rekonsiliasi database.</li>
                            </ul>
                        </div>

                        <!-- Back to Login or Main Portal Link -->
                        <div class="pt-4 border-t border-slate-100">
                            <a href="/" class="inline-flex items-center text-xs font-bold text-royal-600 hover:text-royal-800 transition-colors">
                                <i data-lucide="arrow-left" class="w-4 h-4 mr-1.5"></i>
                                Kembali ke Portal Utama
                            </a>
                        </div>
                    </div>
                </div>
            @endif

        </div>
    </main>

    <!-- Public Institutional Footer -->
    <footer class="py-8 bg-slate-900 text-center text-xs border-t border-slate-850 text-slate-500">
        <div class="max-w-4xl mx-auto px-6 space-y-2">
            <p><strong>Universitas Royal Academic Print Suite (Secure Validation)</strong></p>
            <p class="text-slate-600 text-xxs">Sistem ini mematuhi standar enkripsi tanda tangan elektronik tersertifikasi. Seluruh log pencarian dan pemindaian dipantau secara ketat untuk mencegah tindak penipuan kredensial akademik.</p>
            <p class="pt-2 text-xxs text-slate-750">&copy; {{ date('Y') }} Universitas Royal. Seluruh Hak Cipta Dilindungi.</p>
        </div>
    </footer>

    <!-- Initialize Lucide Icons -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            lucide.createIcons();
        });
    </script>
</body>
</html>
