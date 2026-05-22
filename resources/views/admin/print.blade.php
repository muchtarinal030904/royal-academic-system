@extends('layouts.app')

@section('title', 'Cetak Ijazah - Royal Academic Print Suite')
@section('page-title', 'Antrean Penerbitan Ijazah')
@section('page-subtitle', 'Pilih mahasiswa lulus, verifikasi data secara visual, lalu cetak/ekspor ijazah secara presisi.')

@section('content')
<div x-data="{
    selectedIds: [],
    selectAll: false,
    
    toggleSelectAll() {
        if (this.selectAll) {
            this.selectedIds = [];
            const checkboxes = document.querySelectorAll('.student-checkbox');
            checkboxes.forEach(cb => {
                this.selectedIds.push(parseInt(cb.value));
            });
        } else {
            this.selectedIds = [];
        }
    },
    
    toggleStudent(id) {
        const index = this.selectedIds.indexOf(id);
        if (index > -1) {
            this.selectedIds.splice(index, 1);
            this.selectAll = false;
        } else {
            this.selectedIds.push(id);
            // If all are selected
            const totalOnPage = document.querySelectorAll('.student-checkbox').length;
            if (this.selectedIds.length === totalOnPage) {
                this.selectAll = true;
            }
        }
    },

    printSelected() {
        if (this.selectedIds.length === 0) return;
        window.location.href = `/admin/certificates/print-batch?ids=` + this.selectedIds.join(',');
    }
}" class="space-y-6">

    <!-- Global Alerts -->
    @if(session('success'))
    <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-center text-sm font-medium">
        <i data-lucide="check-circle" class="w-5 h-5 text-emerald-500 mr-3 shrink-0"></i>
        <span>{{ session('success') }}</span>
    </div>
    @endif

    @if(session('error'))
    <div class="p-4 rounded-xl bg-red-50 border border-red-200 text-red-800 flex items-center text-sm font-medium">
        <i data-lucide="alert-circle" class="w-5 h-5 text-red-500 mr-3 shrink-0"></i>
        <span>{{ session('error') }}</span>
    </div>
    @endif

    <!-- Active Template Alert / Status Bar -->
    @if($activeTemplate)
    <div class="p-4 rounded-2xl bg-amber-50/60 border border-amber-200 text-amber-900 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div class="flex items-center">
            <div class="w-10 h-10 rounded-lg bg-amber-500 text-white flex items-center justify-center mr-3 shadow-md shadow-amber-500/20 shrink-0">
                <i data-lucide="award" class="w-5 h-5"></i>
            </div>
            <div>
                <p class="text-xs font-black uppercase tracking-wider text-amber-800">Template Cetak Aktif</p>
                <p class="text-sm font-extrabold text-amber-950">{{ $activeTemplate->name }} <span class="font-normal text-amber-600 text-xs">({{ $activeTemplate->canvas_width }} x {{ $activeTemplate->canvas_height }}px - A4 Landscape)</span></p>
            </div>
        </div>
        <a href="{{ route('admin.templates') }}" class="px-3.5 py-1.5 bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs rounded-lg transition-colors shadow-sm flex items-center shrink-0">
            <i data-lucide="sliders" class="w-3.5 h-3.5 mr-1.5"></i>
            Kelola / Ganti Template
        </a>
    </div>
    @else
    <div class="p-5 rounded-2xl bg-red-50 border border-red-200 text-red-900 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 animate-pulse">
        <div class="flex items-center">
            <div class="w-10 h-10 rounded-lg bg-red-500 text-white flex items-center justify-center mr-3 shadow-md shrink-0">
                <i data-lucide="alert-triangle" class="w-5 h-5"></i>
            </div>
            <div>
                <p class="text-xs font-black uppercase tracking-wider text-red-800">Peringatan Kritis</p>
                <p class="text-sm font-bold text-red-950">Belum ada template ijazah aktif dalam sistem.</p>
                <p class="text-xs text-red-600 mt-0.5">Silakan pilih atau unggah template ijazah terlebih dahulu agar sistem pencetakan dapat berjalan.</p>
            </div>
        </div>
        <a href="{{ route('admin.templates') }}" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-bold text-xs rounded-lg transition-colors shadow-sm flex items-center shrink-0">
            <i data-lucide="plus-circle" class="w-4 h-4 mr-1.5 text-gold-400"></i>
            Kelola Template
        </a>
    </div>
    @endif

    <!-- Search & Filter Card -->
    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
        <form method="GET" action="{{ route('admin.print') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
            <!-- Search Text -->
            <div class="md:col-span-2">
                <label class="block text-xxs font-bold text-slate-400 uppercase tracking-wider mb-1.5">Pencarian NIM / Nama</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <i data-lucide="search" class="w-4 h-4 text-slate-400"></i>
                    </span>
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}"
                           placeholder="Ketik NIM atau nama lengkap..." 
                           class="w-full pl-10 pr-4 py-2 border border-slate-200 rounded-lg text-xs bg-slate-50 focus:bg-white focus:outline-hidden focus:ring-2 focus:ring-royal-500/20 focus:border-royal-500 transition-all">
                </div>
            </div>

            <!-- Filter Program Studi -->
            <div>
                <label class="block text-xxs font-bold text-slate-400 uppercase tracking-wider mb-1.5">Program Studi</label>
                <select name="major" 
                        class="w-full px-3 py-2 border border-slate-200 rounded-lg text-xs bg-slate-50 focus:bg-white focus:outline-hidden focus:ring-2 focus:ring-royal-500/20 focus:border-royal-500 transition-all select-none">
                    <option value="">Semua Program Studi</option>
                    @foreach($majors as $major)
                    <option value="{{ $major }}" {{ request('major') === $major ? 'selected' : '' }}>{{ $major }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Filter Status Cetak -->
            <div>
                <label class="block text-xxs font-bold text-slate-400 uppercase tracking-wider mb-1.5">Status Ijazah</label>
                <select name="certificate_status" 
                        class="w-full px-3 py-2 border border-slate-200 rounded-lg text-xs bg-slate-50 focus:bg-white focus:outline-hidden focus:ring-2 focus:ring-royal-500/20 focus:border-royal-500 transition-all select-none">
                    <option value="">Semua Status</option>
                    <option value="Belum Cetak" {{ request('certificate_status') === 'Belum Cetak' ? 'selected' : '' }}>Belum Cetak</option>
                    <option value="Antrean Cetak" {{ request('certificate_status') === 'Antrean Cetak' ? 'selected' : '' }}>Antrean Cetak</option>
                    <option value="Sudah Cetak" {{ request('certificate_status') === 'Sudah Cetak' ? 'selected' : '' }}>Sudah Cetak</option>
                </select>
            </div>

            <!-- Action Buttons -->
            <div class="md:col-span-4 flex justify-end space-x-2 pt-2 border-t border-slate-100">
                @if(request()->anyFilled(['search', 'major', 'certificate_status']))
                <a href="{{ route('admin.print') }}" class="py-2 px-4 bg-slate-100 hover:bg-slate-255 text-slate-600 font-bold text-xs rounded-lg transition-colors flex items-center">
                    <i data-lucide="rotate-ccw" class="w-3.5 h-3.5 mr-1.5"></i>
                    Reset Filter
                </a>
                @endif
                <button type="submit" class="py-2 px-5 bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs rounded-lg transition-colors flex items-center cursor-pointer">
                    <i data-lucide="sliders-horizontal" class="w-3.5 h-3.5 mr-1.5"></i>
                    Terapkan Penyaringan
                </button>
            </div>
        </form>
    </div>

    <!-- Antrean Table Card -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        
        <!-- Header Actions: Batch Selection Button -->
        <div class="px-6 py-4 border-b border-slate-150 bg-slate-50/50 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <span class="text-xs font-bold text-slate-500">Menampilkan {{ $students->firstItem() ?? 0 }}-{{ $students->lastItem() ?? 0 }} dari {{ $students->total() }} Mahasiswa Lulus</span>
            
            <button @click="printSelected()" 
                    :disabled="selectedIds.length === 0" 
                    :class="selectedIds.length === 0 ? 'bg-slate-100 border border-slate-200 text-slate-400 cursor-not-allowed' : 'bg-royal-600 hover:bg-royal-700 text-white shadow-sm shadow-royal-950/10 cursor-pointer'"
                    class="w-full sm:w-auto px-4 py-2 font-bold text-xs rounded-lg transition-all flex items-center justify-center">
                <i data-lucide="printer" class="w-4 h-4 mr-1.5 text-gold-400"></i>
                Cetak Massal Terpilih (<span x-text="selectedIds.length"></span>)
            </button>
        </div>

        <!-- Grid Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-700">
                <thead>
                    <tr class="bg-slate-50/80 text-slate-400 uppercase text-xxs font-bold tracking-wider border-b border-slate-200">
                        <th class="px-6 py-4 w-10 text-center">
                            <input type="checkbox" 
                                   x-model="selectAll" 
                                   @change="toggleSelectAll()" 
                                   class="w-4 h-4 border border-slate-300 rounded-sm text-royal-600 focus:ring-royal-500/20 focus:outline-hidden cursor-pointer select-none">
                        </th>
                        <th class="px-6 py-4">NIM</th>
                        <th class="px-6 py-4">Nama Lengkap</th>
                        <th class="px-6 py-4">Program Studi</th>
                        <th class="px-6 py-4">IPK</th>
                        <th class="px-6 py-4">Nomor Ijazah</th>
                        <th class="px-6 py-4">Status Cetak</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($students as $st)
                    <tr class="hover:bg-slate-50/40 transition-colors">
                        <td class="px-6 py-4 text-center">
                            <input type="checkbox" 
                                   value="{{ $st->id }}" 
                                   x-model="selectedIds"
                                   @change="toggleStudent({{ $st->id }})"
                                   class="student-checkbox w-4 h-4 border border-slate-300 rounded-sm text-royal-600 focus:ring-royal-500/20 focus:outline-hidden cursor-pointer select-none">
                        </td>
                        <td class="px-6 py-4 font-mono text-xs font-bold text-slate-900">{{ $st->nim }}</td>
                        <td class="px-6 py-4 font-bold text-slate-950">{{ $st->user->name ?? '-' }}</td>
                        <td class="px-6 py-4 text-xs">
                            <span class="font-medium text-slate-800">{{ $st->major }}</span>
                            <p class="text-xxs text-slate-400 mt-0.5">{{ $st->faculty }}</p>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-extrabold bg-royal-50 text-royal-700 border border-royal-100">
                                {{ number_format($st->gpa, 2) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-xs font-mono text-slate-600">
                            {{ $st->certificate_number ?: '-' }}
                        </td>
                        <td class="px-6 py-4">
                            @if($st->certificate_status === 'Sudah Cetak')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xxs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-250">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5"></span>
                                    Sudah Cetak
                                </span>
                            @elseif($st->certificate_status === 'Antrean Cetak')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xxs font-semibold bg-amber-50 text-amber-700 border border-amber-250">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 mr-1.5"></span>
                                    Antrean Cetak
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xxs font-semibold bg-slate-100 text-slate-600 border border-slate-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-slate-400 mr-1.5"></span>
                                    Belum Cetak
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            @if($activeTemplate)
                            <a href="{{ route('admin.certificates.preview', $st->id) }}" 
                               class="inline-flex items-center px-3 py-1.5 bg-slate-900 hover:bg-slate-800 text-white font-bold text-xxs rounded-lg transition-colors cursor-pointer">
                                <i data-lucide="eye" class="w-3.5 h-3.5 mr-1 text-gold-400"></i>
                                Preview & Cetak
                            </a>
                            @else
                            <button disabled 
                                    class="inline-flex items-center px-3 py-1.5 bg-slate-100 text-slate-400 font-bold text-xxs rounded-lg border border-slate-200 cursor-not-allowed">
                                <i data-lucide="eye-off" class="w-3.5 h-3.5 mr-1"></i>
                                Preview & Cetak
                            </button>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center text-slate-400 text-xs">
                            <i data-lucide="folder-open" class="w-10 h-10 text-slate-350 mx-auto mb-3"></i>
                            Tidak ditemukan data mahasiswa yang cocok dengan kriteria antrean cetak.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination Footer -->
        @if($students->hasPages())
        <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/30">
            {{ $students->links() }}
        </div>
        @endif

    </div>

</div>
@endsection
