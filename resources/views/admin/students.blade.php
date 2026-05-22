@extends('layouts.app')

@section('title', 'Kelola Mahasiswa - Royal Academic Print Suite')
@section('page-title', 'Manajemen Data Mahasiswa')
@section('page-subtitle', 'Kelola, cari, saring data akademik, dan impor massal mahasiswa Universitas Royal.')

@section('page-actions')
<div x-data>
    <button @click="$dispatch('open-import-modal')" class="px-4 py-2 border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 font-bold text-xs rounded-lg transition-colors cursor-pointer inline-flex items-center">
        <i data-lucide="upload" class="w-4 h-4 mr-1.5 text-slate-400"></i>
        Impor CSV Massal
    </button>
    <button @click="$dispatch('open-add-modal')" class="px-4 py-2 bg-royal-600 hover:bg-royal-700 text-white font-bold text-xs rounded-lg transition-colors shadow-sm shadow-royal-900/10 cursor-pointer inline-flex items-center ml-2">
        <i data-lucide="user-plus" class="w-4 h-4 mr-1.5"></i>
        Tambah Mahasiswa
    </button>
</div>
@endsection

@section('content')
<div x-data="{ 
    addModalOpen: false, 
    editModalOpen: false, 
    deleteModalOpen: false, 
    importModalOpen: false,
    currentStudent: { user: {} }
}"
@open-add-modal.window="addModalOpen = true"
@open-import-modal.window="importModalOpen = true"
class="space-y-6">

    <!-- Global Alerts (Success/Errors) -->
    @if(session('success'))
    <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-center text-sm font-medium">
        <i data-lucide="check-circle" class="w-5 h-5 text-emerald-500 mr-3 shrink-0"></i>
        <span>{{ session('success') }}</span>
    </div>
    @endif

    @if(session('error'))
    <div class="p-4 rounded-xl bg-red-50 border border-red-200 text-red-800 text-sm font-medium">
        <div class="flex items-center mb-2 font-bold">
            <i data-lucide="alert-circle" class="w-5 h-5 text-red-500 mr-3 shrink-0"></i>
            <span>{{ session('error') }}</span>
        </div>
        @if(session('import_errors'))
        <div class="mt-2 max-h-48 overflow-y-auto bg-white/50 p-3 rounded-lg border border-red-100 text-xs text-red-700 space-y-1 custom-scrollbar">
            @foreach(session('import_errors') as $impErr)
                <p class="font-medium">• {{ $impErr }}</p>
            @endforeach
        </div>
        @endif
    </div>
    @endif

    <!-- Search & Filter Section -->
    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
        <form method="GET" action="{{ route('students.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
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

            <!-- Filter Status Ijazah -->
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
            <div class="flex space-x-2">
                <button type="submit" class="flex-1 py-2 bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs rounded-lg transition-colors cursor-pointer text-center flex items-center justify-center">
                    <i data-lucide="sliders-horizontal" class="w-3.5 h-3.5 mr-1.5"></i>
                    Terapkan
                </button>
                @if(request()->anyFilled(['search', 'certificate_status']))
                <a href="{{ route('students.index') }}" class="py-2 px-3.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-lg transition-colors text-center flex items-center justify-center">
                    <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Main Student Data Table Card -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        
        <!-- Table Header Info bar -->
        <div class="px-6 py-4 border-b border-slate-150 bg-slate-50/50 flex items-center justify-between">
            <span class="text-xs font-bold text-slate-500">Menampilkan {{ $students->firstItem() ?? 0 }}-{{ $students->lastItem() ?? 0 }} dari {{ $students->total() }} Mahasiswa</span>
        </div>

        <!-- Table Grid -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-700">
                <thead>
                    <tr class="bg-slate-50/80 text-slate-400 uppercase text-xxs font-bold tracking-wider border-b border-slate-200">
                        <th class="px-6 py-4">NIM</th>
                        <th class="px-6 py-4">Nama Lengkap</th>
                        <th class="px-6 py-4">Program Studi</th>
                        <th class="px-6 py-4">IPK</th>
                        <th class="px-6 py-4">Status Kelulusan</th>
                        <th class="px-6 py-4">Status Ijazah</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($students as $student)
                    <tr class="hover:bg-slate-50/40 transition-colors">
                        <td class="px-6 py-4 font-mono text-xs font-bold text-slate-900">{{ $student->nim }}</td>
                        <td class="px-6 py-4 font-bold text-slate-950">{{ $student->user->name ?? '-' }}</td>
                        <td class="px-6 py-4 text-xs">
                            <span class="font-medium text-slate-800">{{ $student->major }}</span>
                            <p class="text-xxs text-slate-400 mt-0.5">{{ $student->faculty }}</p>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-extrabold bg-royal-50 text-royal-700 border border-royal-100">
                                {{ number_format($student->gpa, 2) }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            @if($student->status === 'Lulus')
                                <span class="inline-flex items-center px-2 py-0.5 rounded bg-emerald-50 text-emerald-700 text-xxs font-bold border border-emerald-100">Lulus</span>
                            @elseif($student->status === 'Cuti')
                                <span class="inline-flex items-center px-2 py-0.5 rounded bg-amber-50 text-amber-700 text-xxs font-bold border border-amber-100">Cuti</span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded bg-royal-50 text-royal-700 text-xxs font-bold border border-royal-100">Aktif</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($student->certificate_status === 'Sudah Cetak')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xxs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-250">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5"></span>
                                    Sudah Cetak
                                </span>
                            @elseif($student->certificate_status === 'Antrean Cetak')
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
                        <td class="px-6 py-4 text-right space-x-1.5">
                            <!-- Edit Button -->
                            <button @click="currentStudent = { 
                                        id: '{{ $student->id }}', 
                                        nim: '{{ $student->nim }}', 
                                        name: '{{ $student->user->name ?? '' }}', 
                                        email: '{{ $student->user->email ?? '' }}', 
                                        major: '{{ $student->major }}', 
                                        faculty: '{{ $student->faculty }}', 
                                        admission_year: '{{ $student->admission_year }}', 
                                        gpa: '{{ $student->gpa }}', 
                                        status: '{{ $student->status }}', 
                                        certificate_status: '{{ $student->certificate_status }}', 
                                        certificate_number: '{{ $student->certificate_number ?? '' }}', 
                                        graduation_date: '{{ $student->graduation_date ? $student->graduation_date->format('Y-m-d') : '' }}', 
                                        degree: '{{ $student->degree ?? '' }}' 
                                    }; editModalOpen = true" 
                                    class="p-1 text-slate-450 hover:text-slate-900 hover:bg-slate-100 rounded-md transition-colors cursor-pointer inline-flex items-center">
                                <i data-lucide="edit-3" class="w-4 h-4"></i>
                            </button>

                            <!-- Delete Button -->
                            <button @click="currentStudent = { 
                                        id: '{{ $student->id }}', 
                                        name: '{{ $student->user->name ?? '' }}' 
                                    }; deleteModalOpen = true"
                                    class="p-1 text-slate-450 hover:text-red-650 hover:bg-red-50 rounded-md transition-colors cursor-pointer inline-flex items-center">
                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-slate-400 text-xs">
                            <i data-lucide="folder-open" class="w-10 h-10 text-slate-350 mx-auto mb-3"></i>
                            Tidak ditemukan data mahasiswa yang cocok dengan kriteria pencarian.
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

    <!-- ==================== MODAL 1: ADD MAHASISWA ==================== -->
    <div x-show="addModalOpen" 
         class="fixed inset-0 z-50 overflow-y-auto" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         style="display: none;">
        
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs" @click="addModalOpen = false"></div>
        
        <!-- Content Container -->
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="relative bg-white rounded-2xl border border-slate-200 w-full max-w-lg shadow-xl overflow-hidden p-6 z-10"
                 x-transition:enter="transition ease-out duration-300 transform scale-95"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-200 transform scale-100"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95">
                
                <div class="flex justify-between items-center pb-4 border-b border-slate-100">
                    <h3 class="text-base font-bold text-slate-900 flex items-center">
                        <i data-lucide="user-plus" class="w-5 h-5 text-royal-600 mr-2"></i>
                        Tambah Mahasiswa Baru
                    </h3>
                    <button @click="addModalOpen = false" class="p-1 rounded-lg text-slate-400 hover:bg-slate-50 hover:text-slate-700">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>

                <form method="POST" action="{{ route('students.store') }}" class="space-y-4 mt-4">
                    @csrf
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xxs font-bold text-slate-400 uppercase tracking-wider mb-1">NIM *</label>
                            <input type="text" name="nim" required placeholder="e.g. 23220465"
                                   class="w-full px-3 py-2 border border-slate-200 rounded-lg text-xs focus:ring-2 focus:ring-royal-500/20 focus:border-royal-500 focus:outline-hidden">
                        </div>
                        <div>
                            <label class="block text-xxs font-bold text-slate-400 uppercase tracking-wider mb-1">Nama Lengkap *</label>
                            <input type="text" name="name" required placeholder="Nama lengkap..."
                                   class="w-full px-3 py-2 border border-slate-200 rounded-lg text-xs focus:ring-2 focus:ring-royal-500/20 focus:border-royal-500 focus:outline-hidden">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xxs font-bold text-slate-400 uppercase tracking-wider mb-1">Email *</label>
                            <input type="email" name="email" placeholder="e.g. mhs@royal.ac.id"
                                   class="w-full px-3 py-2 border border-slate-200 rounded-lg text-xs focus:ring-2 focus:ring-royal-500/20 focus:border-royal-500 focus:outline-hidden">
                        </div>
                        <div>
                            <label class="block text-xxs font-bold text-slate-400 uppercase tracking-wider mb-1">IPK (GPA) *</label>
                            <input type="number" step="0.01" min="0" max="4.00" name="gpa" required placeholder="e.g. 3.80"
                                   class="w-full px-3 py-2 border border-slate-200 rounded-lg text-xs focus:ring-2 focus:ring-royal-500/20 focus:border-royal-500 focus:outline-hidden">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xxs font-bold text-slate-400 uppercase tracking-wider mb-1">Program Studi *</label>
                            <input type="text" name="major" value="Sistem Informasi" required
                                   class="w-full px-3 py-2 border border-slate-200 rounded-lg text-xs focus:ring-2 focus:ring-royal-500/20 focus:border-royal-500 focus:outline-hidden">
                        </div>
                        <div>
                            <label class="block text-xxs font-bold text-slate-400 uppercase tracking-wider mb-1">Fakultas *</label>
                            <input type="text" name="faculty" value="Fakultas Ilmu Komputer" required
                                   class="w-full px-3 py-2 border border-slate-200 rounded-lg text-xs focus:ring-2 focus:ring-royal-500/20 focus:border-royal-500 focus:outline-hidden">
                        </div>
                    </div>

                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xxs font-bold text-slate-400 uppercase tracking-wider mb-1">Tahun Masuk *</label>
                            <input type="number" name="admission_year" value="2023" required
                                   class="w-full px-3 py-2 border border-slate-200 rounded-lg text-xs focus:ring-2 focus:ring-royal-500/20 focus:border-royal-500 focus:outline-hidden">
                        </div>
                        <div>
                            <label class="block text-xxs font-bold text-slate-400 uppercase tracking-wider mb-1">Status Akademik *</label>
                            <select name="status" required
                                    class="w-full px-2 py-2 border border-slate-200 rounded-lg text-xs focus:ring-2 focus:ring-royal-500/20 focus:border-royal-500 focus:outline-hidden">
                                <option value="Aktif">Aktif</option>
                                <option value="Lulus">Lulus</option>
                                <option value="Cuti">Cuti</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xxs font-bold text-slate-400 uppercase tracking-wider mb-1">Status Ijazah *</label>
                            <select name="certificate_status" required
                                    class="w-full px-2 py-2 border border-slate-200 rounded-lg text-xs focus:ring-2 focus:ring-royal-500/20 focus:border-royal-500 focus:outline-hidden">
                                <option value="Belum Cetak">Belum Cetak</option>
                                <option value="Antrean Cetak">Antrean Cetak</option>
                                <option value="Sudah Cetak">Sudah Cetak</option>
                            </select>
                        </div>
                    </div>

                    <div class="border-t border-slate-100 my-2 pt-3 space-y-3">
                        <p class="text-slate-400 text-xxs font-bold uppercase tracking-widest">Detail Pencetakan Ijazah (Opsional)</p>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xxs font-bold text-slate-400 uppercase tracking-wider mb-1">Gelar Akademik</label>
                                <input type="text" name="degree" value="S.Kom." placeholder="e.g. S.Kom."
                                       class="w-full px-3 py-2 border border-slate-200 rounded-lg text-xs focus:ring-2 focus:ring-royal-500/20 focus:border-royal-500 focus:outline-hidden">
                            </div>
                            <div>
                                <label class="block text-xxs font-bold text-slate-400 uppercase tracking-wider mb-1">Tanggal Lulus</label>
                                <input type="date" name="graduation_date"
                                       class="w-full px-3 py-2 border border-slate-200 rounded-lg text-xs focus:ring-2 focus:ring-royal-500/20 focus:border-royal-500 focus:outline-hidden">
                            </div>
                        </div>
                        <div>
                            <label class="block text-xxs font-bold text-slate-400 uppercase tracking-wider mb-1">Nomor Ijazah</label>
                            <input type="text" name="certificate_number" placeholder="e.g. 102/UNROY/SI/S1/2027"
                                   class="w-full px-3 py-2 border border-slate-200 rounded-lg text-xs focus:ring-2 focus:ring-royal-500/20 focus:border-royal-500 focus:outline-hidden font-mono">
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3 pt-4 border-t border-slate-100">
                        <button type="button" @click="addModalOpen = false" class="px-4 py-2 border border-slate-200 hover:bg-slate-50 text-slate-700 text-xs font-bold rounded-lg transition-colors cursor-pointer">
                            Batalkan
                        </button>
                        <button type="submit" class="px-4 py-2 bg-royal-600 hover:bg-royal-700 text-white text-xs font-bold rounded-lg transition-colors cursor-pointer">
                            Simpan Mahasiswa
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ==================== MODAL 2: EDIT MAHASISWA ==================== -->
    <div x-show="editModalOpen" 
         class="fixed inset-0 z-50 overflow-y-auto" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         style="display: none;">
        
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs" @click="editModalOpen = false"></div>
        
        <!-- Content Container -->
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="relative bg-white rounded-2xl border border-slate-200 w-full max-w-lg shadow-xl overflow-hidden p-6 z-10"
                 x-transition:enter="transition ease-out duration-300 transform scale-95"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-200 transform scale-100"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95">
                
                <div class="flex justify-between items-center pb-4 border-b border-slate-100">
                    <h3 class="text-base font-bold text-slate-900 flex items-center">
                        <i data-lucide="edit-3" class="w-5 h-5 text-royal-600 mr-2"></i>
                        Ubah Data Mahasiswa
                    </h3>
                    <button @click="editModalOpen = false" class="p-1 rounded-lg text-slate-400 hover:bg-slate-50 hover:text-slate-700">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>

                <!-- Action route dynamically binded via Alpine -->
                <form method="POST" :action="'/admin/students/' + currentStudent.id" class="space-y-4 mt-4">
                    @csrf
                    @method('PUT')
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xxs font-bold text-slate-400 uppercase tracking-wider mb-1">NIM (Tidak dapat diubah)</label>
                            <input type="text" x-model="currentStudent.nim" disabled
                                   class="w-full px-3 py-2 border border-slate-200 bg-slate-50 text-slate-400 rounded-lg text-xs focus:outline-hidden font-mono">
                        </div>
                        <div>
                            <label class="block text-xxs font-bold text-slate-400 uppercase tracking-wider mb-1">Nama Lengkap *</label>
                            <input type="text" name="name" x-model="currentStudent.name" required
                                   class="w-full px-3 py-2 border border-slate-200 rounded-lg text-xs focus:ring-2 focus:ring-royal-500/20 focus:border-royal-500 focus:outline-hidden">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xxs font-bold text-slate-400 uppercase tracking-wider mb-1">Email *</label>
                            <input type="email" name="email" x-model="currentStudent.email" required
                                   class="w-full px-3 py-2 border border-slate-200 rounded-lg text-xs focus:ring-2 focus:ring-royal-500/20 focus:border-royal-500 focus:outline-hidden">
                        </div>
                        <div>
                            <label class="block text-xxs font-bold text-slate-400 uppercase tracking-wider mb-1">IPK (GPA) *</label>
                            <input type="number" step="0.01" min="0" max="4.00" name="gpa" x-model="currentStudent.gpa" required
                                   class="w-full px-3 py-2 border border-slate-200 rounded-lg text-xs focus:ring-2 focus:ring-royal-500/20 focus:border-royal-500 focus:outline-hidden">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xxs font-bold text-slate-400 uppercase tracking-wider mb-1">Program Studi *</label>
                            <input type="text" name="major" x-model="currentStudent.major" required
                                   class="w-full px-3 py-2 border border-slate-200 rounded-lg text-xs focus:ring-2 focus:ring-royal-500/20 focus:border-royal-500 focus:outline-hidden">
                        </div>
                        <div>
                            <label class="block text-xxs font-bold text-slate-400 uppercase tracking-wider mb-1">Fakultas *</label>
                            <input type="text" name="faculty" x-model="currentStudent.faculty" required
                                   class="w-full px-3 py-2 border border-slate-200 rounded-lg text-xs focus:ring-2 focus:ring-royal-500/20 focus:border-royal-500 focus:outline-hidden">
                        </div>
                    </div>

                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xxs font-bold text-slate-400 uppercase tracking-wider mb-1">Tahun Masuk *</label>
                            <input type="number" name="admission_year" x-model="currentStudent.admission_year" required
                                   class="w-full px-3 py-2 border border-slate-200 rounded-lg text-xs focus:ring-2 focus:ring-royal-500/20 focus:border-royal-500 focus:outline-hidden">
                        </div>
                        <div>
                            <label class="block text-xxs font-bold text-slate-400 uppercase tracking-wider mb-1">Status Akademik *</label>
                            <select name="status" x-model="currentStudent.status" required
                                    class="w-full px-2 py-2 border border-slate-200 rounded-lg text-xs focus:ring-2 focus:ring-royal-500/20 focus:border-royal-500 focus:outline-hidden">
                                <option value="Aktif">Aktif</option>
                                <option value="Lulus">Lulus</option>
                                <option value="Cuti">Cuti</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xxs font-bold text-slate-400 uppercase tracking-wider mb-1">Status Ijazah *</label>
                            <select name="certificate_status" x-model="currentStudent.certificate_status" required
                                    class="w-full px-2 py-2 border border-slate-200 rounded-lg text-xs focus:ring-2 focus:ring-royal-500/20 focus:border-royal-500 focus:outline-hidden">
                                <option value="Belum Cetak">Belum Cetak</option>
                                <option value="Antrean Cetak">Antrean Cetak</option>
                                <option value="Sudah Cetak">Sudah Cetak</option>
                            </select>
                        </div>
                    </div>

                    <div class="border-t border-slate-100 my-2 pt-3 space-y-3">
                        <p class="text-slate-400 text-xxs font-bold uppercase tracking-widest">Detail Pencetakan Ijazah (Opsional)</p>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xxs font-bold text-slate-400 uppercase tracking-wider mb-1">Gelar Akademik</label>
                                <input type="text" name="degree" x-model="currentStudent.degree" placeholder="e.g. S.Kom."
                                       class="w-full px-3 py-2 border border-slate-200 rounded-lg text-xs focus:ring-2 focus:ring-royal-500/20 focus:border-royal-500 focus:outline-hidden">
                            </div>
                            <div>
                                <label class="block text-xxs font-bold text-slate-400 uppercase tracking-wider mb-1">Tanggal Lulus</label>
                                <input type="date" name="graduation_date" x-model="currentStudent.graduation_date"
                                       class="w-full px-3 py-2 border border-slate-200 rounded-lg text-xs focus:ring-2 focus:ring-royal-500/20 focus:border-royal-500 focus:outline-hidden">
                            </div>
                        </div>
                        <div>
                            <label class="block text-xxs font-bold text-slate-400 uppercase tracking-wider mb-1">Nomor Ijazah</label>
                            <input type="text" name="certificate_number" x-model="currentStudent.certificate_number" placeholder="e.g. 102/UNROY/SI/S1/2027"
                                   class="w-full px-3 py-2 border border-slate-200 rounded-lg text-xs focus:ring-2 focus:ring-royal-500/20 focus:border-royal-500 focus:outline-hidden font-mono">
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3 pt-4 border-t border-slate-100">
                        <button type="button" @click="editModalOpen = false" class="px-4 py-2 border border-slate-200 hover:bg-slate-50 text-slate-700 text-xs font-bold rounded-lg transition-colors cursor-pointer">
                            Batalkan
                        </button>
                        <button type="submit" class="px-4 py-2 bg-royal-600 hover:bg-royal-700 text-white text-xs font-bold rounded-lg transition-colors cursor-pointer">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ==================== MODAL 3: HAPUS MAHASISWA (CONFIRMATION) ==================== -->
    <div x-show="deleteModalOpen" 
         class="fixed inset-0 z-50 overflow-y-auto" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         style="display: none;">
        
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs" @click="deleteModalOpen = false"></div>
        
        <!-- Content Container -->
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="relative bg-white rounded-2xl border border-slate-200 w-full max-w-sm shadow-xl overflow-hidden p-6 z-10 text-center"
                 x-transition:enter="transition ease-out duration-300 transform scale-95"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-200 transform scale-100"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95">
                
                <div class="w-12 h-12 rounded-full bg-red-50 text-red-600 flex items-center justify-center mx-auto mb-4 border border-red-100">
                    <i data-lucide="trash-2" class="w-6 h-6"></i>
                </div>

                <h3 class="text-base font-bold text-slate-900">Hapus Data Mahasiswa?</h3>
                <p class="text-xs text-slate-400 leading-relaxed mt-2">
                    Apakah Anda yakin ingin menghapus mahasiswa bernama <strong class="text-slate-800" x-text="currentStudent.name text-bold"></strong> beserta seluruh akun akses portal akademiknya secara permanen? Tindakan ini tidak dapat dibatalkan.
                </p>

                <form method="POST" :action="'/admin/students/' + currentStudent.id" class="mt-6 flex space-x-3">
                    @csrf
                    @method('DELETE')
                    <button type="button" @click="deleteModalOpen = false" class="flex-1 py-2 border border-slate-200 hover:bg-slate-50 text-slate-700 text-xs font-bold rounded-lg transition-colors cursor-pointer">
                        Batalkan
                    </button>
                    <button type="submit" class="flex-1 py-2 bg-red-650 hover:bg-red-700 text-white text-xs font-bold rounded-lg transition-colors cursor-pointer">
                        Hapus Permanen
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- ==================== MODAL 4: IMPOR CSV MASSAL ==================== -->
    <div x-show="importModalOpen" 
         class="fixed inset-0 z-50 overflow-y-auto" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         style="display: none;">
        
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs" @click="importModalOpen = false"></div>
        
        <!-- Content Container -->
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="relative bg-white rounded-2xl border border-slate-200 w-full max-w-md shadow-xl overflow-hidden p-6 z-10"
                 x-transition:enter="transition ease-out duration-300 transform scale-95"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-200 transform scale-100"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95">
                
                <div class="flex justify-between items-center pb-4 border-b border-slate-100">
                    <h3 class="text-base font-bold text-slate-900 flex items-center">
                        <i data-lucide="upload" class="w-5 h-5 text-royal-600 mr-2"></i>
                        Impor Mahasiswa via CSV
                    </h3>
                    <button @click="importModalOpen = false" class="p-1 rounded-lg text-slate-400 hover:bg-slate-50 hover:text-slate-700">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>

                <form method="POST" action="{{ route('students.import') }}" enctype="multipart/form-data" class="space-y-4 mt-4">
                    @csrf
                    
                    <div class="p-4 bg-royal-50/50 rounded-xl border border-royal-100 text-xxs text-royal-800 leading-relaxed space-y-2">
                        <p class="font-bold flex items-center">
                            <i data-lucide="info" class="w-3.5 h-3.5 mr-1 text-royal-600"></i>
                            Panduan Struktur Unggahan CSV:
                        </p>
                        <ul class="list-disc pl-4 space-y-1">
                            <li>Format kolom header pertama wajib: <code class="font-mono text-slate-950 font-bold">nim, nama, program_studi, fakultas, tahun_masuk, ipk</code></li>
                            <li>Kolom opsional tambahan: <code class="font-mono text-slate-950">status_mahasiswa, status_ijazah, nomor_ijazah, tanggal_lulus, gelar</code></li>
                            <li>Pencocokan NIM bersifat unik; NIM duplikat akan membatalkan seluruh proses impor (aman transaksional).</li>
                        </ul>
                        <div class="pt-2">
                            <a href="{{ route('students.template') }}" class="inline-flex items-center text-xs font-bold text-royal-700 hover:underline">
                                <i data-lucide="download" class="w-3.5 h-3.5 mr-1"></i>
                                Unduh Template CSV Contoh (.csv)
                            </a>
                        </div>
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-xxs font-bold text-slate-400 uppercase tracking-wider">Pilih File CSV *</label>
                        <div class="flex items-center justify-center w-full">
                            <label class="flex flex-col items-center justify-center w-full h-32 border-2 border-slate-200 border-dashed rounded-xl cursor-pointer hover:bg-slate-50 hover:border-slate-350 transition-all p-4">
                                <div class="flex flex-col items-center justify-center pt-5 pb-6 text-center">
                                    <i data-lucide="file-text" class="w-8 h-8 text-slate-400 mb-2"></i>
                                    <p class="text-xs text-slate-600 font-bold">Pilih file CSV</p>
                                    <p class="text-xxs text-slate-400 mt-1">Hanya mendukung format .csv / .txt</p>
                                </div>
                                <input type="file" name="csv_file" required accept=".csv,.txt" class="hidden">
                            </label>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3 pt-4 border-t border-slate-100">
                        <button type="button" @click="importModalOpen = false" class="px-4 py-2 border border-slate-200 hover:bg-slate-50 text-slate-700 text-xs font-bold rounded-lg transition-colors cursor-pointer">
                            Batalkan
                        </button>
                        <button type="submit" class="px-4 py-2 bg-royal-600 hover:bg-royal-700 text-white text-xs font-bold rounded-lg transition-colors cursor-pointer">
                            Unggah & Proses Impor
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection
