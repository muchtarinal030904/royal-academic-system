@extends('layouts.app')

@section('title', 'Activity Logs - Royal Academic Print Suite')
@section('page-title', 'Audit Trail Keamanan')
@section('page-subtitle', 'Pantau aktivitas administratif, audit keamanan pencetakan, dan jejak sistem secara real-time.')

@section('content')
<!-- Success / Error Banners -->
@if(session('success'))
    <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl flex items-center space-x-3 shadow-2xs">
        <i data-lucide="check-circle" class="w-5 h-5 text-emerald-600 shrink-0"></i>
        <div class="text-sm font-semibold">{{ session('success') }}</div>
    </div>
@endif

@if(session('error'))
    <div class="mb-6 p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-xl flex items-center space-x-3 shadow-2xs">
        <i data-lucide="alert-triangle" class="w-5 h-5 text-rose-600 shrink-0"></i>
        <div class="text-sm font-semibold">{{ session('error') }}</div>
    </div>
@endif

<!-- Audit Logs Page Wrapper with Alpine JS modal state -->
<div x-data="{ showPurgeModal: false }">

    <!-- Filters and Purge Actions Panel -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs p-6 mb-8">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
            <!-- Search & Action Filters Form -->
            <form action="{{ route('admin.logs') }}" method="GET" class="flex-1 grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Search description -->
                <div class="md:col-span-2 relative">
                    <label for="search" class="sr-only">Cari Log</label>
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <i data-lucide="search" class="w-4 h-4 text-slate-400"></i>
                    </span>
                    <input type="text" 
                           id="search"
                           name="search" 
                           value="{{ request('search') }}"
                           placeholder="Cari deskripsi aksi atau nama admin..." 
                           class="w-full pl-10 pr-4 py-2.5 border border-slate-200 rounded-lg text-sm bg-slate-50 focus:bg-white focus:outline-hidden focus:ring-2 focus:ring-royal-500/20 focus:border-royal-500 transition-all">
                </div>

                <!-- Action Type Filter -->
                <div class="flex space-x-2">
                    <select name="action" 
                            class="flex-1 px-3 py-2.5 border border-slate-200 rounded-lg text-sm bg-slate-50 focus:bg-white focus:outline-hidden focus:ring-2 focus:ring-royal-500/20 focus:border-royal-500 transition-all text-slate-700">
                        <option value="">Semua Tipe Aksi</option>
                        @foreach($actions as $act)
                            <option value="{{ $act }}" {{ request('action') == $act ? 'selected' : '' }}>{{ $act }}</option>
                        @endforeach
                    </select>

                    <button type="submit" class="px-4 py-2.5 bg-royal-600 hover:bg-royal-700 text-white font-bold text-xs rounded-lg transition-colors inline-flex items-center shadow-xs">
                        <i data-lucide="filter" class="w-3.5 h-3.5 mr-1.5"></i>
                        Filter
                    </button>
                </div>
            </form>

            <!-- Reset Filters & Purge Controls -->
            <div class="flex items-center space-x-3 shrink-0">
                @if(request()->anyFilled(['search', 'action']))
                    <a href="{{ route('admin.logs') }}" class="px-4 py-2.5 border border-slate-200 hover:bg-slate-50 text-slate-700 font-bold text-xs rounded-lg transition-colors inline-flex items-center">
                        <i data-lucide="refresh-cw" class="w-3.5 h-3.5 mr-1.5"></i>
                        Reset
                    </a>
                @endif

                <button @click="showPurgeModal = true" class="px-4 py-2.5 bg-rose-50 border border-rose-200 text-rose-700 hover:bg-rose-100 font-bold text-xs rounded-lg transition-colors inline-flex items-center">
                    <i data-lucide="trash-2" class="w-3.5 h-3.5 mr-1.5"></i>
                    Bersihkan Log Audit
                </button>
            </div>
        </div>
    </div>

    <!-- Timeline / List Feed Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50 flex justify-between items-center">
            <h3 class="text-base font-bold text-slate-900">Jejak Rekam Aktivitas (Logs)</h3>
            <span class="text-xs px-2.5 py-1 bg-royal-50 border border-royal-100 text-royal-700 rounded-full font-semibold">
                Total Rekaman: {{ $logs->total() }}
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-200 bg-slate-50 text-xxs font-bold text-slate-400 uppercase tracking-widest">
                        <th class="py-4 px-6">Waktu Kejadian</th>
                        <th class="py-4 px-6">Aksi Keamanan</th>
                        <th class="py-4 px-6">Pelaku (User)</th>
                        <th class="py-4 px-6">Deskripsi Aktivitas</th>
                        <th class="py-4 px-6">Alamat IP / Browser</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @php
                        $badgeColors = [
                            'LOGIN' => 'bg-emerald-50 border-emerald-200 text-emerald-700',
                            'LOGOUT' => 'bg-slate-100 border-slate-200 text-slate-600',
                            'FAILED_LOGIN' => 'bg-red-50 border-red-200 text-red-700 font-bold',
                            'PURGE_LOGS' => 'bg-rose-100 border-rose-200 text-rose-800 font-extrabold border-2',
                            'UPDATE_SETTINGS' => 'bg-amber-50 border-amber-200 text-amber-800 font-bold',
                            'UPDATE_TEMPLATE' => 'bg-indigo-50 border-indigo-200 text-indigo-700',
                            'DELETE_TEMPLATE' => 'bg-red-50 border-red-200 text-red-700',
                            'PRINT_SINGLE' => 'bg-royal-50 border-royal-200 text-royal-700',
                            'PRINT_BATCH' => 'bg-royal-50 border-royal-200 text-royal-700 font-bold',
                            'CHANGE_PASSWORD' => 'bg-purple-50 border-purple-200 text-purple-700',
                        ];
                    @endphp

                    @forelse($logs as $log)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <!-- Timestamp -->
                            <td class="py-4 px-6 whitespace-nowrap">
                                <div class="flex flex-col">
                                    <span class="font-semibold text-slate-900">
                                        {{ \Carbon\Carbon::parse($log->created_at)->translatedFormat('d M Y') }}
                                    </span>
                                    <span class="text-xs text-slate-400 font-mono mt-0.5">
                                        {{ \Carbon\Carbon::parse($log->created_at)->format('H:i:s') }} WIB
                                    </span>
                                </div>
                            </td>

                            <!-- Action Badge -->
                            <td class="py-4 px-6">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xxs font-mono border {{ $badgeColors[$log->action] ?? 'bg-slate-50 border-slate-200 text-slate-700' }}">
                                    {{ $log->action }}
                                </span>
                            </td>

                            <!-- User Username -->
                            <td class="py-4 px-6 whitespace-nowrap">
                                <div class="flex items-center space-x-2">
                                    <div class="w-7 h-7 rounded-full bg-slate-100 text-slate-700 flex items-center justify-center font-bold text-xs border border-slate-200">
                                        {{ strtoupper(substr($log->username ?? 'G', 0, 1)) }}
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="font-semibold text-slate-800">{{ $log->username }}</span>
                                        @if($log->user)
                                            <span class="text-xxs text-slate-400">{{ $log->user->email }}</span>
                                        @else
                                            <span class="text-xxs text-slate-400 italic">Pengunjung Luar</span>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <!-- Description -->
                            <td class="py-4 px-6 max-w-xs md:max-w-md">
                                <p class="text-slate-700 font-medium leading-relaxed">{{ $log->description }}</p>
                            </td>

                            <!-- Technical metadata -->
                            <td class="py-4 px-6 text-xs text-slate-500 whitespace-nowrap">
                                <div class="space-y-1">
                                    <div class="flex items-center space-x-1.5">
                                        <i data-lucide="globe" class="w-3.5 h-3.5 text-slate-400"></i>
                                        <span class="font-mono text-slate-700 bg-slate-100 px-1.5 py-0.5 rounded border border-slate-200">{{ $log->ip_address }}</span>
                                    </div>
                                    <div class="flex items-center space-x-1.5" title="{{ $log->user_agent }}">
                                        <i data-lucide="laptop" class="w-3.5 h-3.5 text-slate-400 shrink-0"></i>
                                        <span class="truncate max-w-[130px] text-slate-400 block">
                                            {{ $log->user_agent }}
                                        </span>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-16 text-center text-slate-400 bg-white">
                                <div class="max-w-md mx-auto">
                                    <div class="w-16 h-16 bg-slate-50 border border-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                        <i data-lucide="shield-alert" class="w-8 h-8 text-slate-300"></i>
                                    </div>
                                    <h4 class="text-base font-bold text-slate-700">Audit Trail Kosong</h4>
                                    <p class="text-xs text-slate-400 mt-1.5 px-4">Belum ada transaksi log yang tercatat atau tidak ada data yang sesuai dengan filter pencarian Anda.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination Footer -->
        @if($logs->hasPages())
            <div class="px-6 py-4 bg-slate-50 border-t border-slate-100">
                {{ $logs->links() }}
            </div>
        @endif
    </div>

    <!-- PURGE AUDIT LOGS MODAL (Using Alpine JS) -->
    <div x-show="showPurgeModal" 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         style="display: none;">
        
        <div class="bg-white rounded-2xl max-w-md w-full shadow-2xl border border-slate-200 overflow-hidden" @click.away="showPurgeModal = false">
            <!-- Modal Header -->
            <div class="px-6 py-5 bg-rose-50 border-b border-rose-100 flex items-center space-x-3 text-rose-800">
                <div class="p-2 bg-rose-100 rounded-lg">
                    <i data-lucide="shield-alert" class="w-6 h-6 text-rose-700"></i>
                </div>
                <div>
                    <h3 class="text-lg font-bold">Pembersihan Log Audit</h3>
                    <p class="text-xs text-rose-600 mt-0.5">Tindakan ini permanen dan tidak dapat dibatalkan!</p>
                </div>
            </div>

            <!-- Modal Body / Form -->
            <form action="{{ route('admin.logs.purge') }}" method="POST" class="p-6 space-y-4">
                @csrf
                <p class="text-slate-600 text-sm leading-relaxed">
                    Untuk menjamin kinerja server dan menghemat penyimpanan database, Anda dapat membersihkan data log yang sudah tua. 
                    Tindakan ini akan menyisakan log kejadian hari ini atau hari yang Anda pilih, dan akan menulis satu rekaman pembersihan log ke audit trail demi akuntabilitas data.
                </p>

                <div>
                    <label for="retention_days" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Batas Hari Penyimpanan (Retensi)</label>
                    <div class="relative rounded-md shadow-2xs">
                        <input type="number" 
                               name="retention_days" 
                               id="retention_days" 
                               value="30"
                               min="0"
                               max="365"
                               class="w-full pl-3 pr-16 py-2.5 border border-slate-200 rounded-lg text-sm bg-slate-50 focus:bg-white focus:outline-hidden focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition-all font-mono font-bold text-slate-900" 
                               required>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                            <span class="text-xs font-semibold text-slate-400 uppercase">Hari</span>
                        </div>
                    </div>
                    <p class="text-xxs text-slate-400 mt-1.5 leading-relaxed">
                        Masukkan <strong class="text-rose-600 font-bold">0</strong> untuk menghapus seluruh riwayat log aktivitas keamanan secara total tanpa sisa.
                    </p>
                </div>

                <!-- Warning Panel -->
                <div class="p-3.5 bg-amber-50 border border-amber-200 rounded-lg flex items-start space-x-2.5 text-amber-800 text-xs">
                    <i data-lucide="alert-circle" class="w-4 h-4 text-amber-600 shrink-0 mt-0.5"></i>
                    <div>
                        <strong class="font-bold">Perhatian:</strong> Tindakan pembersihan ini akan langsung tercatat sebagai log audit bertipe <code class="font-mono bg-amber-100 px-1 py-0.2 rounded font-bold">PURGE_LOGS</code> yang mencatat nama Anda, alamat IP, dan jumlah baris log yang dihapus.
                    </div>
                </div>

                <!-- Modal Actions -->
                <div class="flex items-center justify-end space-x-3 pt-3 border-t border-slate-100">
                    <button type="button" 
                            @click="showPurgeModal = false" 
                            class="px-4 py-2 border border-slate-200 hover:bg-slate-50 text-slate-700 font-bold text-xs rounded-lg transition-colors">
                        Batal
                    </button>
                    <button type="submit" 
                            class="px-5 py-2 bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs rounded-lg transition-colors inline-flex items-center shadow-xs">
                        <i data-lucide="trash-2" class="w-3.5 h-3.5 mr-1.5"></i>
                        Ya, Bersihkan Sekarang
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
