@extends('layouts.app')

@section('title', 'Riwayat Cetak - Royal Academic Print Suite')
@section('page-title', 'Riwayat Pencetakan Ijazah')
@section('page-subtitle', 'Daftar log komprehensif penerbitan ijazah dan pencetakan resmi mahasiswa.')

@section('content')
<!-- Summary Metric Cards -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <!-- Today Prints -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-xs flex items-center space-x-4 transition-all hover:shadow-md hover:border-slate-300">
        <div class="p-3 bg-royal-50 rounded-xl text-royal-600">
            <i data-lucide="calendar-range" class="w-6 h-6 text-royal-600"></i>
        </div>
        <div>
            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Dicetak Hari Ini</p>
            <h3 class="text-2xl font-black text-slate-900 mt-1">{{ $totalToday }}</h3>
        </div>
    </div>

    <!-- This Month Prints -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-xs flex items-center space-x-4 transition-all hover:shadow-md hover:border-slate-300">
        <div class="p-3 bg-gold-50 rounded-xl text-gold-600">
            <i data-lucide="printer" class="w-6 h-6 text-gold-600"></i>
        </div>
        <div>
            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Dicetak Bulan Ini</p>
            <h3 class="text-2xl font-black text-slate-900 mt-1">{{ $totalThisMonth }}</h3>
        </div>
    </div>

    <!-- Total Prints -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-xs flex items-center space-x-4 transition-all hover:shadow-md hover:border-slate-300">
        <div class="p-3 bg-emerald-50 rounded-xl text-emerald-600">
            <i data-lucide="award" class="w-6 h-6 text-emerald-600"></i>
        </div>
        <div>
            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Sepanjang Waktu</p>
            <h3 class="text-2xl font-black text-slate-900 mt-1">{{ $totalAllTime }}</h3>
        </div>
    </div>
</div>

<!-- Search and Date Filter Panel -->
<div class="bg-white rounded-2xl border border-slate-200 shadow-xs p-6 mb-8">
    <form action="{{ route('admin.history') }}" method="GET" class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Search Input -->
            <div class="md:col-span-2">
                <label for="search" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Cari Data</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <i data-lucide="search" class="w-4 h-4 text-slate-400"></i>
                    </span>
                    <input type="text" 
                           id="search"
                           name="search" 
                           value="{{ request('search') }}"
                           placeholder="Cari Nomor Ijazah, NIM, atau Nama Mahasiswa..." 
                           class="w-full pl-10 pr-4 py-2.5 border border-slate-200 rounded-lg text-sm bg-slate-50 focus:bg-white focus:outline-hidden focus:ring-2 focus:ring-royal-500/20 focus:border-royal-500 transition-all">
                </div>
            </div>

            <!-- Start Date -->
            <div>
                <label for="start_date" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Tanggal Mulai</label>
                <input type="date" 
                       id="start_date"
                       name="start_date" 
                       value="{{ request('start_date') }}"
                       class="w-full px-3 py-2.5 border border-slate-200 rounded-lg text-sm bg-slate-50 focus:bg-white focus:outline-hidden focus:ring-2 focus:ring-royal-500/20 focus:border-royal-500 transition-all text-slate-700">
            </div>

            <!-- End Date -->
            <div>
                <label for="end_date" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Tanggal Selesai</label>
                <input type="date" 
                       id="end_date"
                       name="end_date" 
                       value="{{ request('end_date') }}"
                       class="w-full px-3 py-2.5 border border-slate-200 rounded-lg text-sm bg-slate-50 focus:bg-white focus:outline-hidden focus:ring-2 focus:ring-royal-500/20 focus:border-royal-500 transition-all text-slate-700">
            </div>
        </div>

        <div class="flex items-center justify-between pt-2 border-t border-slate-100">
            <span class="text-xs text-slate-400">
                Menampilkan hasil pencarian untuk: <strong class="text-slate-600">{{ request('search') ? '"'.request('search').'"' : 'Semua Data' }}</strong>
            </span>
            <div class="flex space-x-3">
                @if(request()->anyFilled(['search', 'start_date', 'end_date']))
                    <a href="{{ route('admin.history') }}" class="px-4 py-2 border border-slate-200 hover:bg-slate-50 text-slate-700 hover:text-slate-900 font-bold text-xs rounded-lg transition-colors inline-flex items-center">
                        <i data-lucide="refresh-cw" class="w-3.5 h-3.5 mr-1.5"></i>
                        Reset Filter
                    </a>
                @endif
                <button type="submit" class="px-5 py-2 bg-royal-600 hover:bg-royal-700 text-white font-bold text-xs rounded-lg transition-colors inline-flex items-center shadow-xs">
                    <i data-lucide="filter" class="w-3.5 h-3.5 mr-1.5"></i>
                    Terapkan Filter
                </button>
            </div>
        </div>
    </form>
</div>

<!-- History Table Section -->
<div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
    <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50 flex justify-between items-center">
        <h3 class="text-base font-bold text-slate-900">Histori Transaksi Penerbitan</h3>
        <span class="text-xs px-2.5 py-1 bg-royal-50 border border-royal-100 text-royal-700 rounded-full font-semibold">
            Total Rekaman: {{ $histories->total() }}
        </span>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b border-slate-200 bg-slate-50 text-xxs font-bold text-slate-400 uppercase tracking-widest">
                    <th class="py-4 px-6">Mahasiswa</th>
                    <th class="py-4 px-6">Nomor Ijazah</th>
                    <th class="py-4 px-6">Dicetak Oleh</th>
                    <th class="py-4 px-6 text-center">Waktu Cetak</th>
                    <th class="py-4 px-6">Metadata Keamanan</th>
                    <th class="py-4 px-6 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-sm">
                @forelse($histories as $history)
                    <tr class="hover:bg-slate-50/60 transition-colors">
                        <!-- Student details -->
                        <td class="py-4 px-6">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 rounded-full bg-royal-50 text-royal-700 flex items-center justify-center font-bold border border-royal-100 shadow-2xs">
                                    {{ isset($history->student->user) ? strtoupper(substr($history->student->user->name, 0, 2)) : 'M' }}
                                </div>
                                <div>
                                    <h4 class="font-bold text-slate-900">{{ $history->student->user->name ?? 'Mahasiswa' }}</h4>
                                    <p class="text-xs text-slate-400 mt-0.5">NIM: <span class="font-mono">{{ $history->student->nim ?? '-' }}</span></p>
                                </div>
                            </div>
                        </td>

                        <!-- Certificate number with copy action -->
                        <td class="py-4 px-6">
                            <div class="flex items-center space-x-2">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-mono font-bold bg-slate-100 text-slate-800 border border-slate-200 select-all" id="cert-{{ $history->id }}">
                                    {{ $history->certificate_number }}
                                </span>
                                <button onclick="copyToClipboard('{{ $history->certificate_number }}', this)" 
                                        class="p-1 rounded-md text-slate-400 hover:text-royal-600 hover:bg-royal-50 transition-all"
                                        title="Salin Nomor Ijazah">
                                    <i data-lucide="copy" class="w-4 h-4"></i>
                                </button>
                            </div>
                        </td>

                        <!-- Printer Admin user details -->
                        <td class="py-4 px-6">
                            <div class="flex flex-col">
                                <span class="font-semibold text-slate-800">{{ $history->user->name ?? 'Sistem' }}</span>
                                <span class="text-xs text-slate-400 mt-0.5">{{ $history->user->email ?? 'admin@royal.ac.id' }}</span>
                            </div>
                        </td>

                        <!-- Date printed at -->
                        <td class="py-4 px-6 text-center">
                            <div class="inline-flex flex-col items-center">
                                <span class="font-semibold text-slate-800">
                                    {{ \Carbon\Carbon::parse($history->printed_at)->translatedFormat('d M Y') }}
                                </span>
                                <span class="text-xs text-slate-400 font-mono mt-0.5">
                                    {{ \Carbon\Carbon::parse($history->printed_at)->format('H:i:s') }} WIB
                                </span>
                            </div>
                        </td>

                        <!-- Security metadata (IP & User-Agent) -->
                        <td class="py-4 px-6 text-xs text-slate-500">
                            <div class="space-y-1">
                                <div class="flex items-center space-x-1.5">
                                    <i data-lucide="globe" class="w-3.5 h-3.5 text-slate-400"></i>
                                    <span class="font-mono text-slate-700 bg-slate-100 px-1.5 py-0.5 rounded border border-slate-200">{{ $history->ip_address }}</span>
                                </div>
                                <div class="flex items-center space-x-1.5 group relative" title="{{ $history->user_agent }}">
                                    <i data-lucide="laptop" class="w-3.5 h-3.5 text-slate-400 shrink-0"></i>
                                    <span class="truncate max-w-[180px] text-slate-400 block" title="{{ $history->user_agent }}">
                                        {{ $history->user_agent }}
                                    </span>
                                </div>
                            </div>
                        </td>

                        <!-- Action controls -->
                        <td class="py-4 px-6 text-center">
                            <div class="flex items-center justify-center space-x-2">
                                @if(isset($history->student))
                                    <a href="{{ route('admin.certificates.preview', $history->student->id) }}" 
                                       target="_blank"
                                       class="p-2 rounded-lg bg-royal-50 border border-royal-100 text-royal-600 hover:bg-royal-100 hover:text-royal-700 transition-colors inline-flex items-center justify-center"
                                       title="Pratinjau Salinan Digital">
                                        <i data-lucide="eye" class="w-4 h-4"></i>
                                    </a>
                                    <a href="{{ route('verification.verify', $history->student->nim) }}" 
                                       target="_blank"
                                       class="p-2 rounded-lg bg-gold-50 border border-gold-100 text-gold-700 hover:bg-gold-100 hover:text-gold-800 transition-colors inline-flex items-center justify-center"
                                       title="Periksa Tautan Verifikasi Publik">
                                        <i data-lucide="qr-code" class="w-4 h-4"></i>
                                    </a>
                                @else
                                    <span class="text-xs text-slate-400 italic">Siswa Dihapus</span>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-16 text-center text-slate-400 bg-white">
                            <div class="max-w-md mx-auto">
                                <div class="w-16 h-16 bg-slate-50 border border-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <i data-lucide="history" class="w-8 h-8 text-slate-300"></i>
                                </div>
                                <h4 class="text-base font-bold text-slate-700">Belum Ada Riwayat Pencetakan</h4>
                                <p class="text-xs text-slate-400 mt-1.5 px-4">Tidak ditemukan rekaman penerbitan ijazah yang cocok dengan filter pencarian Anda saat ini.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination Footer -->
    @if($histories->hasPages())
        <div class="px-6 py-4 bg-slate-50 border-t border-slate-100">
            {{ $histories->links() }}
        </div>
    @endif
</div>

<!-- Toast notification element -->
<div id="copy-toast" class="fixed bottom-5 right-5 z-50 transform translate-y-10 opacity-0 transition-all duration-300 pointer-events-none">
    <div class="bg-slate-900 text-white px-4 py-3 rounded-xl shadow-lg flex items-center space-x-2 border border-slate-800 text-sm">
        <i data-lucide="check-circle" class="w-5 h-5 text-emerald-400"></i>
        <span>Nomor ijazah berhasil disalin!</span>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function copyToClipboard(text, button) {
        navigator.clipboard.writeText(text).then(() => {
            const toast = document.getElementById('copy-toast');
            
            // Show toast
            toast.classList.remove('translate-y-10', 'opacity-0');
            toast.classList.add('translate-y-0', 'opacity-100');
            
            // Animation flash icon
            const originalIcon = button.innerHTML;
            button.innerHTML = '<i data-lucide="check" class="w-4 h-4 text-emerald-500"></i>';
            lucide.createIcons();
            
            setTimeout(() => {
                // Hide toast
                toast.classList.remove('translate-y-0', 'opacity-100');
                toast.classList.add('translate-y-10', 'opacity-0');
                
                // Restore button icon
                button.innerHTML = originalIcon;
                lucide.createIcons();
            }, 2500);
        }).catch(err => {
            console.error('Gagal menyalin teks: ', err);
        });
    }
</script>
@endsection
