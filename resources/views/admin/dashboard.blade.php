@extends('layouts.app')

@section('title', 'Admin Dashboard - Royal Academic Document System')
@section('page-title', 'Dashboard Utama')
@section('page-subtitle', 'Ringkasan aktivitas sistem pencetakan ijazah Universitas Royal.')

@section('content')
<!-- Dynamic Stat Cards Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5 mb-6">
    
    <!-- Stat Card 1: Total Mahasiswa -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-xs flex items-center space-x-4 transition-all hover:shadow-md hover:border-slate-300">
        <div class="p-3 bg-royal-50 rounded-xl text-royal-600">
            <i data-lucide="users" class="w-6 h-6"></i>
        </div>
        <div>
            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Mahasiswa</p>
            <h3 class="text-2xl font-black text-slate-900 mt-1">{{ $totalStudents }}</h3>
        </div>
    </div>

    <!-- Stat Card 2: Ijazah Dicetak -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-xs flex items-center space-x-4 transition-all hover:shadow-md hover:border-slate-300">
        <div class="p-3 bg-emerald-50 rounded-xl text-emerald-600">
            <i data-lucide="printer" class="w-6 h-6"></i>
        </div>
        <div>
            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Ijazah Dicetak</p>
            <h3 class="text-2xl font-black text-slate-900 mt-1">{{ $printedCertificates }}</h3>
        </div>
    </div>

    <!-- Stat Card 3: Antrean Cetak -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-xs flex items-center space-x-4 transition-all hover:shadow-md hover:border-slate-300">
        <div class="p-3 bg-amber-50 rounded-xl text-amber-600">
            <i data-lucide="clock" class="w-6 h-6"></i>
        </div>
        <div>
            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Antrean Cetak</p>
            <h3 class="text-2xl font-black text-slate-900 mt-1">{{ $queueCertificates }}</h3>
        </div>
    </div>

    <!-- Stat Card 4: Template Aktif -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-xs flex items-center space-x-4 transition-all hover:shadow-md hover:border-slate-300">
        <div class="p-3 bg-gold-50 rounded-xl text-gold-600">
            <i data-lucide="file-sliders" class="w-6 h-6"></i>
        </div>
        <div>
            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Template Aktif</p>
            <h3 class="text-2xl font-black text-slate-900 mt-1">4</h3>
        </div>
    </div>

</div>

<!-- Welcome Notification Alert banner -->
<div class="bg-gradient-to-r from-royal-600 to-royal-800 rounded-2xl p-5 sm:p-6 text-white shadow-md border border-royal-700/20 mb-6 relative overflow-hidden">
    <div class="absolute right-0 top-0 bottom-0 opacity-15 flex items-center pointer-events-none pr-8">
        <i data-lucide="graduation-cap" class="w-32 h-32 text-white"></i>
    </div>
    <div class="max-w-2xl relative z-10">
        <span class="px-2 py-0.5 text-[10px] font-bold uppercase tracking-widest bg-gold-500 text-slate-950 rounded-full">Status Operasional</span>
        <h4 class="text-lg font-bold tracking-tight mt-2">Sistem Autentikasi & Database Mahasiswa Aktif</h4>
        <p class="text-royal-100 text-xs sm:text-sm leading-relaxed mt-1.5">
            Sistem pencetakan ijazah berjalan normal. Pantau antrean cetak, data mahasiswa, dan aktivitas administrasi terbaru melalui dashboard ini.
        </p>
        <div class="mt-4 flex space-x-3">
            <a href="{{ route('students.index') }}" class="px-3.5 py-1.5 bg-white text-royal-600 hover:bg-slate-50 font-bold text-xs rounded-lg transition-colors inline-flex items-center">
                Mulai Kelola Mahasiswa
                <i data-lucide="arrow-right" class="w-3.5 h-3.5 ml-1.5"></i>
            </a>
        </div>
    </div>
</div>

<!-- Main Section: Charts and Sidebars -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    
    <!-- Left Column: ApexChart Printing Trend -->
    <div class="lg:col-span-2 bg-white p-6 rounded-2xl border border-slate-200 shadow-xs flex flex-col justify-between">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-base font-bold text-slate-900">Tren Pencetakan Ijazah</h3>
                <p class="text-xs text-slate-400">Statistik visual berkala pencetakan ijazah Universitas Royal.</p>
            </div>
            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-slate-100 text-slate-800 border border-slate-200">
                <i data-lucide="calendar" class="w-3.5 h-3.5 mr-1 text-slate-500"></i>
                Tahun {{ date('Y') }}
            </span>
        </div>
        <!-- Chart Canvas Container -->
        <div class="w-full mt-auto overflow-hidden">
            <div id="printing-trend-chart" class="w-full h-[220px]"></div>
        </div>
    </div>

    <!-- Right Column: Quick Actions & System Info & Recent Activity -->
    <div class="space-y-6">

        <!-- Quick Actions Panel -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-xs">
            <h3 class="text-base font-bold text-slate-900 mb-4">Aksi Cepat</h3>
            <div class="grid grid-cols-2 gap-4">
                <a href="{{ route('students.index') }}" class="flex flex-col items-center justify-center p-4 bg-royal-600 hover:bg-royal-700 text-white rounded-xl shadow-xs hover:shadow-md transition-all text-center group">
                    <i data-lucide="user-plus" class="w-7 h-7 text-royal-100 group-hover:scale-105 transition-all mb-2" stroke-width="2.75"></i>
                    <span class="text-xs font-bold text-white">Tambah Mhs</span>
                </a>
                <a href="{{ route('admin.print') }}" class="flex flex-col items-center justify-center p-4 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl shadow-xs hover:shadow-md transition-all text-center group">
                    <i data-lucide="printer" class="w-7 h-7 text-emerald-100 group-hover:scale-105 transition-all mb-2" stroke-width="2.75"></i>
                    <span class="text-xs font-bold text-white">Cetak Ijazah</span>
                </a>
                <a href="{{ route('admin.templates') }}" class="flex flex-col items-center justify-center p-4 bg-slate-50 border border-slate-200 rounded-xl hover:bg-slate-100 hover:border-slate-300 transition-all text-center group">
                    <i data-lucide="file-sliders" class="w-7 h-7 text-slate-400 group-hover:text-slate-600 transition-colors mb-2" stroke-width="2.75"></i>
                    <span class="text-xs font-bold text-slate-600 group-hover:text-slate-800">Atur Template</span>
                </a>
                <a href="{{ route('admin.logs') }}" class="flex flex-col items-center justify-center p-4 bg-slate-50 border border-slate-200 rounded-xl hover:bg-slate-100 hover:border-slate-300 transition-all text-center group">
                    <i data-lucide="shield-alert" class="w-7 h-7 text-slate-400 group-hover:text-slate-600 transition-colors mb-2" stroke-width="2.75"></i>
                    <span class="text-xs font-bold text-slate-600 group-hover:text-slate-800">Log Sistem</span>
                </a>
            </div>
        </div>

        <!-- System Operational Information -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-xs">
            <h3 class="text-base font-bold text-slate-900 mb-4">Informasi Operasional</h3>
            <div class="space-y-3">
                <div class="flex items-center justify-between text-xs pb-2.5 border-b border-slate-100">
                    <span class="text-slate-400">Status Sistem</span>
                    <span class="inline-flex items-center font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-md">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5 animate-pulse"></span>
                        Normal
                    </span>
                </div>
                <div class="flex items-center justify-between text-xs pb-2.5 border-b border-slate-100">
                    <span class="text-slate-400">Template Aktif</span>
                    <span class="font-bold text-slate-700 bg-slate-100 px-2 py-0.5 rounded-md">4 Desain</span>
                </div>
                <div class="flex items-center justify-between text-xs pb-2.5 border-b border-slate-100">
                    <span class="text-slate-400">Verifikasi QR</span>
                    <span class="inline-flex items-center font-bold text-royal-600 bg-royal-50 px-2 py-0.5 rounded-md">
                        <i data-lucide="shield-check" class="w-3 h-3 mr-1"></i>
                        Aktif
                    </span>
                </div>
                <div class="flex items-center justify-between text-xs pb-2.5 border-b border-slate-100">
                    <span class="text-slate-400">Antrean Cetak</span>
                    <span class="font-bold text-amber-600 bg-amber-50 px-2 py-0.5 rounded-md">{{ $queueCertificates }} Mahasiswa</span>
                </div>
                <div class="flex items-center justify-between text-xs">
                    <span class="text-slate-400">Koneksi Printer</span>
                    <span class="inline-flex items-center font-bold text-emerald-600">
                        <i data-lucide="printer" class="w-3.5 h-3.5 mr-1 text-emerald-500"></i>
                        Ready / Online
                    </span>
                </div>
            </div>
        </div>

        <!-- Recent Activities Feed -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-xs">
            <h3 class="text-base font-bold text-slate-900 mb-4">Aktivitas Terbaru</h3>
            @php
                $recentActivities = [];
                if (class_exists(\App\Models\ActivityLog::class)) {
                    $recentActivities = \App\Models\ActivityLog::latest()->take(4)->get();
                }
            @endphp
            <div class="flow-root">
                <ul role="list" class="-mb-8">
                    @forelse($recentActivities as $actIdx => $activity)
                    <li>
                        <div class="relative pb-6">
                            @if($actIdx < count($recentActivities) - 1)
                            <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-slate-200" aria-hidden="true"></span>
                            @endif
                            <div class="relative flex space-x-3">
                                <div class="shrink-0">
                                    @if(in_array($activity->action, ['FAILED_LOGIN', 'PURGE_LOGS', 'DELETE_TEMPLATE']))
                                        <span class="h-8 w-8 rounded-full bg-rose-50 flex items-center justify-center ring-8 ring-white text-rose-500">
                                            <i data-lucide="alert-triangle" class="w-4 h-4"></i>
                                        </span>
                                    @elseif(in_array($activity->action, ['CHANGE_PASSWORD', 'UPDATE_SETTINGS', 'UPDATE_TEMPLATE']))
                                        <span class="h-8 w-8 rounded-full bg-amber-50 flex items-center justify-center ring-8 ring-white text-amber-500">
                                            <i data-lucide="settings" class="w-4 h-4"></i>
                                        </span>
                                    @elseif(str_contains(strtolower($activity->description), 'cetak') || str_contains(strtolower($activity->description), 'print'))
                                        <span class="h-8 w-8 rounded-full bg-emerald-50 flex items-center justify-center ring-8 ring-white text-emerald-500">
                                            <i data-lucide="printer" class="w-4 h-4"></i>
                                        </span>
                                    @else
                                        <span class="h-8 w-8 rounded-full bg-blue-50 flex items-center justify-center ring-8 ring-white text-royal-600">
                                            <i data-lucide="info" class="w-4 h-4"></i>
                                        </span>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0 pt-1.5 flex justify-between space-x-4">
                                    <div class="min-w-0 flex-1">
                                        <p class="text-xs text-slate-800 font-medium leading-normal break-words">
                                            {{ $activity->description }}
                                        </p>
                                    </div>
                                    <div class="text-right text-[10px] whitespace-nowrap text-slate-400 font-medium shrink-0">
                                        <time datetime="{{ $activity->created_at }}">{{ $activity->created_at->diffForHumans(null, true) }}</time>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </li>
                    @empty
                    <li class="py-4 text-center text-xs text-slate-400">
                        <i data-lucide="activity" class="w-8 h-8 text-slate-300 mx-auto mb-2"></i>
                        Belum ada riwayat aktivitas terbaru.
                    </li>
                    @endforelse
                </ul>
            </div>
        </div>

    </div>

</div>

<!-- Bottom Section: Recent Registered Students -->
<div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-xs">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 space-y-3 sm:space-y-0">
        <div>
            <h3 class="text-base font-bold text-slate-900">Pendaftaran Mahasiswa Terbaru</h3>
            <p class="text-xs text-slate-400">Daftar mahasiswa yang baru saja dimasukkan ke dalam database.</p>
        </div>
        <a href="{{ route('students.index') }}" class="px-3.5 py-1.5 border border-slate-200 rounded-lg text-xs font-bold text-slate-600 hover:bg-slate-50 transition-colors flex items-center">
            Kelola Semua Mahasiswa
            <i data-lucide="arrow-up-right" class="w-3.5 h-3.5 ml-1"></i>
        </a>
    </div>

    <!-- Responsive Table -->
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-slate-700">
            <thead>
                <tr class="bg-slate-50 text-slate-400 uppercase text-xxs font-bold tracking-wider border-b border-slate-200">
                    <th class="px-6 py-4">NIM</th>
                    <th class="px-6 py-4">Nama Lengkap</th>
                    <th class="px-6 py-4">Program Studi</th>
                    <th class="px-6 py-4">IPK</th>
                    <th class="px-6 py-4">Status Ijazah</th>
                    <th class="px-6 py-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($recentStudents as $student)
                <tr class="hover:bg-slate-50/50 transition-colors">
                    <td class="px-6 py-4 font-mono text-xs font-bold text-slate-900">{{ $student->nim }}</td>
                    <td class="px-6 py-4 font-bold text-slate-950">{{ $student->user->name ?? '-' }}</td>
                    <td class="px-6 py-4 text-xs">
                        <span class="font-medium text-slate-800">{{ $student->major }}</span>
                        <p class="text-xxs text-slate-400">{{ $student->faculty }}</p>
                    </td>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-extrabold bg-royal-50 text-royal-700 border border-royal-100">
                            {{ number_format($student->gpa, 2) }}
                        </span>
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
                    <td class="px-6 py-4 text-right">
                        <div class="inline-flex items-center space-x-1 justify-end">
                            <a href="{{ route('students.index', ['search' => $student->nim]) }}" class="p-1.5 text-slate-500 hover:text-royal-600 hover:bg-slate-100 rounded-lg transition-all" title="Detail / Edit">
                                <i data-lucide="edit-3" class="w-4 h-4"></i>
                            </a>
                            @if($student->status === 'Lulus')
                            <a href="{{ route('admin.certificates.preview', $student->id) }}" target="_blank" class="p-1.5 text-slate-500 hover:text-amber-600 hover:bg-slate-100 rounded-lg transition-all" title="Preview Ijazah">
                                <i data-lucide="eye" class="w-4 h-4"></i>
                            </a>
                            @else
                            <button class="p-1.5 text-slate-300 cursor-not-allowed" disabled title="Mahasiswa Belum Lulus">
                                <i data-lucide="eye" class="w-4 h-4"></i>
                            </button>
                            @endif
                            <a href="{{ route('admin.print', ['search' => $student->nim]) }}" class="p-1.5 text-slate-500 hover:text-emerald-600 hover:bg-slate-100 rounded-lg transition-all" title="Cetak Ijazah">
                                <i data-lucide="printer" class="w-4 h-4"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-8 text-center text-slate-400 text-xs">
                        <i data-lucide="folder-open" class="w-8 h-8 text-slate-300 mx-auto mb-2"></i>
                        Belum ada data mahasiswa terdaftar.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@section('scripts')
<!-- Load ApexCharts Library -->
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Options for the ApexCharts Printing Trend Area Chart
        var options = {
            chart: {
                type: 'area',
                height: 220,
                fontFamily: 'Inter, sans-serif',
                toolbar: {
                    show: false
                },
                zoom: {
                    enabled: false
                }
            },
            series: [{
                name: 'Ijazah Dicetak',
                data: [5, 12, 10, 18, 22, 28, 45, 38, 55, 48, 62, 75]
            }],
            xaxis: {
                categories: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
                labels: {
                    style: {
                        colors: '#94a3b8',
                        fontSize: '11px'
                    }
                },
                axisBorder: {
                    show: false
                },
                axisTicks: {
                    show: false
                }
            },
            yaxis: {
                labels: {
                    style: {
                        colors: '#94a3b8',
                        fontSize: '11px'
                    }
                }
            },
            colors: ['#1d4ed8'], // Brand Royal Blue
            stroke: {
                curve: 'smooth',
                width: 3.5
            },
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.4,
                    opacityTo: 0.02,
                    stops: [0, 90, 100]
                }
            },
            dataLabels: {
                enabled: false
            },
            grid: {
                borderColor: '#f1f5f9',
                strokeDashArray: 4,
                xaxis: {
                    lines: {
                        show: true
                    }
                },
                yaxis: {
                    lines: {
                        show: true
                    }
                }
            },
            tooltip: {
                theme: 'light',
                y: {
                    formatter: function(val) {
                        return val + " Lembar Ijazah";
                    }
                }
            }
        };

        var chart = new ApexCharts(document.querySelector("#printing-trend-chart"), options);
        chart.render();
    });
</script>
@endsection
