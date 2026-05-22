<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Royal Academic Document System')</title>

    <!-- Meta Tags for SEO & Professional Styling -->
    <meta name="description" content="Sistem Manajemen Pencetakan Ijazah Modern Universitas Royal">
    <meta name="author" content="Universitas Royal">

    <!-- Styles and Scripts via Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Lucide Icons for clean aesthetic -->
    <script src="https://unpkg.com/lucide@latest"></script>
    
    <!-- Google Fonts Bunny Fallback loaded via Vite font preloading -->
</head>
<body class="h-full font-sans antialiased text-slate-800" 
      x-data="{ sidebarOpen: false, sidebarCollapsed: localStorage.getItem('sidebar_collapsed') !== 'false', sidebarHovered: false, isDesktop: window.innerWidth >= 1024 }"
      @resize.window="isDesktop = window.innerWidth >= 1024">

    <!-- Mobile Sidebar Drawer Overlay -->
    <div x-show="sidebarOpen" 
         x-transition:enter="transition-opacity ease-linear duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-40 bg-slate-900/60 backdrop-blur-xs lg:hidden"
         @click="sidebarOpen = false" 
         aria-hidden="true"
         style="display: none;"></div>

    <!-- Sidebar Navigation -->
    <aside class="fixed inset-y-0 left-0 z-50 flex flex-col bg-slate-900 border-r border-slate-800 text-slate-300 transition-[width,transform] duration-300 ease-in-out"
           @mouseenter="sidebarHovered = true"
           @mouseleave="sidebarHovered = false"
           :class="[(sidebarCollapsed && !sidebarHovered && isDesktop) ? 'w-20 overflow-x-hidden' : 'w-72 shadow-2xl shadow-slate-950/60', sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0']">
        
        <!-- Sidebar Brand Logo & Header -->
        @php
            $logoPath = \App\Models\SystemSetting::get('logo_path');
            $logoUrl = ($logoPath && file_exists(public_path($logoPath))) ? asset($logoPath) : asset('images/logo-univ.png');
            $univName = \App\Models\SystemSetting::get('university_name', 'Universitas Royal');
            $userRole = Auth::user()->role ?? 'admin';
        @endphp
        <div class="flex items-center h-20 border-b border-slate-800 bg-slate-950 transition-all duration-300" :class="(sidebarCollapsed && !sidebarHovered && isDesktop) ? 'px-4 justify-center' : 'px-6 justify-between'">
            <a href="#" class="flex items-center">
                <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-slate-850 p-1 border border-slate-700/50 shadow-inner shrink-0 transition-transform duration-300" :class="(sidebarCollapsed && !sidebarHovered && isDesktop) ? 'scale-105' : ''">
                    <img src="{{ $logoUrl }}" alt="Logo {{ $univName }}" class="w-full h-full object-contain">
                </div>
                <div :class="(sidebarCollapsed && !sidebarHovered && isDesktop) ? 'hidden' : 'ml-3'" class="transition-all duration-300">
                    <h1 class="text-sm font-bold text-white tracking-wide uppercase truncate max-w-[150px] leading-tight">{{ $univName }}</h1>
                    <span class="text-[10px] font-semibold text-gold-500 uppercase tracking-widest block -mt-0.5">
                        {{ $userRole === 'mahasiswa' ? 'PORTAL IJAZAH' : 'ACADEMIC DOCUMENT SYSTEM' }}
                    </span>
                </div>
            </a>
            <button @click="sidebarOpen = false" class="p-1 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800 lg:hidden">
                <i data-lucide="x" class="w-6 h-6"></i>
            </button>
        </div>


        <!-- Sidebar Navigation Menu Links -->
        <nav class="flex-1 py-6 space-y-1.5 overflow-y-auto custom-scrollbar transition-all duration-300" :class="(sidebarCollapsed && !sidebarHovered && isDesktop) ? 'px-2' : 'px-4'">
            @php
                $role = Auth::user()->role ?? 'admin';
                $currentRoute = Route::currentRouteName();
            @endphp

            @if($role === 'admin')
                <!-- ADMIN MENU SECTIONS -->
                <div :class="(sidebarCollapsed && !sidebarHovered && isDesktop) ? 'hidden' : 'opacity-100 mb-2'" class="px-3 text-xxs font-bold text-slate-500 uppercase tracking-wider transition-all duration-300">Utama</div>
                
                <a href="{{ route('admin.dashboard') }}" 
                   class="flex items-center py-2.5 rounded-lg text-sm font-medium transition-all duration-300 group {{ $currentRoute == 'admin.dashboard' ? 'bg-royal-600 text-white shadow-sm shadow-royal-600/10' : 'text-slate-400 hover:bg-slate-800/50 hover:text-white' }}"
                   :class="(sidebarCollapsed && !sidebarHovered && isDesktop) ? 'justify-center px-0' : 'px-3.5'">
                    <i data-lucide="layout-dashboard" class="w-5 h-5 transition-transform group-hover:scale-105 shrink-0 duration-300" :class="(sidebarCollapsed && !sidebarHovered && isDesktop) ? 'mr-0' : 'mr-3'"></i>
                    <span :class="(sidebarCollapsed && !sidebarHovered && isDesktop) ? 'hidden' : ''" class="transition-all duration-300">Dashboard</span>
                </a>

                <div :class="(sidebarCollapsed && !sidebarHovered && isDesktop) ? 'hidden' : 'opacity-100 mt-6 mb-2'" class="px-3 text-xxs font-bold text-slate-500 uppercase tracking-wider transition-all duration-300">Akademik</div>
                
                <a href="{{ route('students.index') }}" 
                   class="flex items-center py-2.5 rounded-lg text-sm font-medium transition-all duration-300 group {{ str_starts_with($currentRoute, 'students.') ? 'bg-royal-600 text-white shadow-sm' : 'text-slate-400 hover:bg-slate-800/50 hover:text-white' }}"
                   :class="(sidebarCollapsed && !sidebarHovered && isDesktop) ? 'justify-center px-0' : 'px-3.5'">
                    <i data-lucide="users" class="w-5 h-5 transition-transform group-hover:scale-105 shrink-0 duration-300" :class="(sidebarCollapsed && !sidebarHovered && isDesktop) ? 'mr-0' : 'mr-3'"></i>
                    <span :class="(sidebarCollapsed && !sidebarHovered && isDesktop) ? 'hidden' : ''" class="transition-all duration-300">Data Mahasiswa</span>
                </a>

                <a href="{{ route('admin.templates') }}" 
                   class="flex items-center py-2.5 rounded-lg text-sm font-medium transition-all duration-300 group {{ $currentRoute == 'admin.templates' ? 'bg-royal-600 text-white shadow-sm' : 'text-slate-400 hover:bg-slate-800/50 hover:text-white' }}"
                   :class="(sidebarCollapsed && !sidebarHovered && isDesktop) ? 'justify-center px-0' : 'px-3.5'">
                    <i data-lucide="file-sliders" class="w-5 h-5 transition-transform group-hover:scale-105 shrink-0 duration-300" :class="(sidebarCollapsed && !sidebarHovered && isDesktop) ? 'mr-0' : 'mr-3'"></i>
                    <span :class="(sidebarCollapsed && !sidebarHovered && isDesktop) ? 'hidden' : ''" class="transition-all duration-300">Template Ijazah</span>
                </a>

                <div :class="(sidebarCollapsed && !sidebarHovered && isDesktop) ? 'hidden' : 'opacity-100 mt-6 mb-2'" class="px-3 text-xxs font-bold text-slate-500 uppercase tracking-wider transition-all duration-300">Pencetakan</div>

                <a href="{{ route('admin.print') }}" 
                   class="flex items-center py-2.5 rounded-lg text-sm font-medium transition-all duration-300 group {{ $currentRoute == 'admin.print' ? 'bg-royal-600 text-white shadow-sm' : 'text-slate-400 hover:bg-slate-800/50 hover:text-white' }}"
                   :class="(sidebarCollapsed && !sidebarHovered && isDesktop) ? 'justify-center px-0' : 'px-3.5'">
                    <i data-lucide="printer" class="w-5 h-5 transition-transform group-hover:scale-105 shrink-0 duration-300" :class="(sidebarCollapsed && !sidebarHovered && isDesktop) ? 'mr-0' : 'mr-3'"></i>
                    <span :class="(sidebarCollapsed && !sidebarHovered && isDesktop) ? 'hidden' : ''" class="transition-all duration-300">Cetak Ijazah</span>
                </a>

                <a href="{{ route('admin.history') }}" 
                   class="flex items-center py-2.5 rounded-lg text-sm font-medium transition-all duration-300 group {{ $currentRoute == 'admin.history' ? 'bg-royal-600 text-white shadow-sm' : 'text-slate-400 hover:bg-slate-800/50 hover:text-white' }}"
                   :class="(sidebarCollapsed && !sidebarHovered && isDesktop) ? 'justify-center px-0' : 'px-3.5'">
                    <i data-lucide="history" class="w-5 h-5 transition-transform group-hover:scale-105 shrink-0 duration-300" :class="(sidebarCollapsed && !sidebarHovered && isDesktop) ? 'mr-0' : 'mr-3'"></i>
                    <span :class="(sidebarCollapsed && !sidebarHovered && isDesktop) ? 'hidden' : ''" class="transition-all duration-300">Riwayat Cetak</span>
                </a>

                <div :class="(sidebarCollapsed && !sidebarHovered && isDesktop) ? 'hidden' : 'opacity-100 mt-6 mb-2'" class="px-3 text-xxs font-bold text-slate-500 uppercase tracking-wider transition-all duration-300">Keamanan & Sistem</div>

                <a href="{{ route('admin.logs') }}" 
                   class="flex items-center py-2.5 rounded-lg text-sm font-medium transition-all duration-300 group {{ $currentRoute == 'admin.logs' ? 'bg-royal-600 text-white shadow-sm' : 'text-slate-400 hover:bg-slate-800/50 hover:text-white' }}"
                   :class="(sidebarCollapsed && !sidebarHovered && isDesktop) ? 'justify-center px-0' : 'px-3.5'">
                    <i data-lucide="shield-alert" class="w-5 h-5 transition-transform group-hover:scale-105 shrink-0 duration-300" :class="(sidebarCollapsed && !sidebarHovered && isDesktop) ? 'mr-0' : 'mr-3'"></i>
                    <span :class="(sidebarCollapsed && !sidebarHovered && isDesktop) ? 'hidden' : ''" class="transition-all duration-300">Activity Logs</span>
                </a>

                <a href="{{ route('admin.settings') }}" 
                   class="flex items-center py-2.5 rounded-lg text-sm font-medium transition-all duration-300 group {{ $currentRoute == 'admin.settings' ? 'bg-royal-600 text-white shadow-sm' : 'text-slate-400 hover:bg-slate-800/50 hover:text-white' }}"
                   :class="(sidebarCollapsed && !sidebarHovered && isDesktop) ? 'justify-center px-0' : 'px-3.5'">
                    <i data-lucide="settings" class="w-5 h-5 transition-transform group-hover:scale-105 shrink-0 duration-300" :class="(sidebarCollapsed && !sidebarHovered && isDesktop) ? 'mr-0' : 'mr-3'"></i>
                    <span :class="(sidebarCollapsed && !sidebarHovered && isDesktop) ? 'hidden' : ''" class="transition-all duration-300">Pengaturan</span>
                </a>
            @else
                <!-- STUDENT MENU SECTIONS -->
                <div :class="(sidebarCollapsed && !sidebarHovered && isDesktop) ? 'hidden' : 'opacity-100 mb-2'" class="px-3 text-xxs font-bold text-slate-500 uppercase tracking-wider transition-all duration-300">Mahasiswa</div>
                
                <a href="{{ route('student.dashboard') }}" 
                   class="flex items-center py-2.5 rounded-lg text-sm font-medium transition-all duration-300 group {{ $currentRoute == 'student.dashboard' ? 'bg-royal-600 text-white shadow-sm' : 'text-slate-400 hover:bg-slate-800/50 hover:text-white' }}"
                   :class="(sidebarCollapsed && !sidebarHovered && isDesktop) ? 'justify-center px-0' : 'px-3.5'">
                    <i data-lucide="layout-dashboard" class="w-5 h-5 transition-transform group-hover:scale-105 shrink-0 duration-300" :class="(sidebarCollapsed && !sidebarHovered && isDesktop) ? 'mr-0' : 'mr-3'"></i>
                    <span :class="(sidebarCollapsed && !sidebarHovered && isDesktop) ? 'hidden' : ''" class="transition-all duration-300">Dashboard Saya</span>
                </a>

                <a href="{{ route('student.preview') }}" 
                   class="flex items-center py-2.5 rounded-lg text-sm font-medium transition-all duration-300 group {{ $currentRoute == 'student.preview' ? 'bg-royal-600 text-white shadow-sm' : 'text-slate-400 hover:bg-slate-800/50 hover:text-white' }}"
                   :class="(sidebarCollapsed && !sidebarHovered && isDesktop) ? 'justify-center px-0' : 'px-3.5'">
                    <i data-lucide="eye" class="w-5 h-5 transition-transform group-hover:scale-105 shrink-0 duration-300" :class="(sidebarCollapsed && !sidebarHovered && isDesktop) ? 'mr-0' : 'mr-3'"></i>
                    <span :class="(sidebarCollapsed && !sidebarHovered && isDesktop) ? 'hidden' : ''" class="transition-all duration-300">Preview Ijazah</span>
                </a>

                <a href="{{ route('student.verification') }}" 
                   class="flex items-center py-2.5 rounded-lg text-sm font-medium transition-all duration-300 group {{ $currentRoute == 'student.verification' ? 'bg-royal-600 text-white shadow-sm' : 'text-slate-400 hover:bg-slate-800/50 hover:text-white' }}"
                   :class="(sidebarCollapsed && !sidebarHovered && isDesktop) ? 'justify-center px-0' : 'px-3.5'">
                    <i data-lucide="qr-code" class="w-5 h-5 transition-transform group-hover:scale-105 shrink-0 duration-300" :class="(sidebarCollapsed && !sidebarHovered && isDesktop) ? 'mr-0' : 'mr-3'"></i>
                    <span :class="(sidebarCollapsed && !sidebarHovered && isDesktop) ? 'hidden' : ''" class="transition-all duration-300">QR Verifikasi</span>
                </a>
            @endif
        </nav>

        <!-- Sidebar Footer Action (Logout) & Toggle Collapse -->
        <div class="border-t border-slate-800/80 bg-slate-950/20 flex flex-col transition-all duration-300">
            <!-- Logout Button -->
            <div class="transition-all duration-300" :class="(sidebarCollapsed && !sidebarHovered && isDesktop) ? 'p-2' : 'p-4'">
                <a href="#" 
                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                   class="flex items-center py-3 rounded-lg text-sm font-medium text-slate-400 hover:bg-red-950/40 hover:text-red-400 transition-all duration-300 group"
                   :class="(sidebarCollapsed && !sidebarHovered && isDesktop) ? 'justify-center px-0' : 'px-4'">
                    <i data-lucide="log-out" class="w-5 h-5 transition-transform group-hover:translate-x-0.5 shrink-0 duration-300" :class="(sidebarCollapsed && !sidebarHovered && isDesktop) ? 'mr-0' : 'mr-3'"></i>
                    <span :class="(sidebarCollapsed && !sidebarHovered && isDesktop) ? 'hidden' : ''" class="transition-all duration-300">Keluar</span>
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                    @csrf
                </form>
            </div>

            <!-- Toggle Collapse Button Row -->
            <div class="border-t border-slate-800/60 p-2 bg-slate-950/40 flex justify-center">
                <button @click="sidebarCollapsed = !sidebarCollapsed; localStorage.setItem('sidebar_collapsed', sidebarCollapsed)" 
                        class="w-full flex items-center justify-center py-2 rounded-lg text-slate-500 hover:text-slate-300 hover:bg-slate-800/50 transition-all duration-300 focus:outline-hidden cursor-pointer"
                        :title="sidebarCollapsed ? 'Sematkan Sidebar (Lock)' : 'Lepas Sematan (Collapse)'"
                        :class="(sidebarCollapsed && !sidebarHovered && isDesktop) ? 'px-0' : 'px-4'">
                    <i data-lucide="chevrons-left" class="w-5 h-5 transition-transform duration-300" :class="sidebarCollapsed ? 'rotate-180 text-gold-500' : ''"></i>
                    <span :class="(sidebarCollapsed && !sidebarHovered && isDesktop) ? 'hidden' : 'ml-2'" class="text-xs font-semibold uppercase tracking-wider transition-all duration-300 truncate">Sematkan Sidebar</span>
                </button>
            </div>
        </div>
    </aside>

    <!-- Main Content Area Wrapper -->
    <div class="flex flex-col min-h-screen transition-all duration-300 ease-in-out"
         :class="(sidebarCollapsed && isDesktop) ? 'lg:pl-20' : 'lg:pl-72'">
        
        <!-- Header / Top Bar Navbar -->
        <header class="flex items-center justify-between h-20 px-6 sm:px-8 bg-white border-b border-slate-200 sticky top-0 z-30 shadow-xs">
            
            <!-- Mobile Toggle Menu Button -->
            <button @click="sidebarOpen = true" class="p-2 rounded-lg text-slate-600 hover:bg-slate-100 hover:text-slate-900 lg:hidden">
                <i data-lucide="menu" class="w-6 h-6"></i>
            </button>

            <!-- Search Bar placeholder or title -->
            <div class="hidden sm:flex items-center">
                @if(($userRole ?? (Auth::user()->role ?? 'admin')) === 'mahasiswa')
                    <div class="flex items-center space-x-3 text-sm font-medium">
                        <div class="flex items-center space-x-1.5 bg-emerald-50 text-emerald-700 px-3 py-1 border border-emerald-100/50 shadow-xxs rounded-full">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            <span class="text-[11px] font-bold tracking-wider uppercase">Status Akademik: Aktif</span>
                        </div>
                        <div class="flex items-center space-x-1.5 bg-royal-50 text-royal-700 px-3 py-1 border border-royal-100/50 shadow-xxs rounded-full">
                            <i data-lucide="calendar" class="w-3.5 h-3.5 text-royal-600"></i>
                            <span class="text-[11px] font-bold tracking-wider uppercase">Semester Genap 2026/2027</span>
                        </div>
                    </div>
                @else
                    <div class="relative w-64">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <i data-lucide="search" class="w-4 h-4 text-slate-400"></i>
                        </span>
                        <input type="text" 
                               placeholder="Cari mahasiswa, NIM, atau dokumen..." 
                               class="w-full pl-10 pr-4 py-2 border border-slate-200 rounded-lg text-sm bg-slate-50 focus:bg-white focus:outline-hidden focus:ring-2 focus:ring-royal-500/20 focus:border-royal-500 transition-all">
                    </div>
                @endif
            </div>

            <!-- Header Right Menu Controls -->
            <div class="flex items-center space-x-4">
                
                <!-- Notification Bell Indicator with Dropdown -->
                @if(Auth::user() && Auth::user()->role === 'admin')
                    @php
                        $recentAlerts = [];
                        if (class_exists(\App\Models\ActivityLog::class)) {
                            $recentAlerts = \App\Models\ActivityLog::latest()->take(4)->get();
                        }
                        $alertCount = count($recentAlerts);
                    @endphp
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" @click.away="open = false" class="relative p-2 rounded-full text-slate-500 hover:bg-slate-50 hover:text-slate-800 transition-colors focus:outline-hidden cursor-pointer">
                            <span class="sr-only">Notifications</span>
                            <i data-lucide="bell" class="w-5.5 h-5.5"></i>
                            @if($alertCount > 0)
                                <span class="absolute top-1.5 right-1.5 block h-2 w-2 rounded-full bg-gold-500 ring-2 ring-white"></span>
                            @endif
                        </button>

                        <!-- Notification Dropdown Panel -->
                        <div x-show="open"
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="transform opacity-0 scale-95"
                             x-transition:enter-end="transform opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="transform opacity-100 scale-100"
                             x-transition:leave-end="transform opacity-0 scale-95"
                             class="absolute right-0 mt-2.5 w-80 rounded-xl bg-white border border-slate-200 shadow-lg py-1.5 z-50 overflow-hidden"
                             style="display: none;">
                            
                            <div class="px-4 py-2 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                                <span class="text-xs font-bold text-slate-900">Notifikasi Sistem</span>
                                @if($alertCount > 0)
                                    <span class="px-1.5 py-0.5 bg-royal-50 text-royal-700 text-xxs font-bold rounded-md">{{ $alertCount }} Baru</span>
                                @endif
                            </div>

                            <div class="divide-y divide-slate-100 max-h-72 overflow-y-auto">
                                @forelse($recentAlerts as $alert)
                                    <div class="p-3 hover:bg-slate-50 transition-colors flex space-x-2.5">
                                        <div class="shrink-0 mt-0.5">
                                            @if(in_array($alert->action, ['FAILED_LOGIN', 'PURGE_LOGS', 'DELETE_TEMPLATE']))
                                                <span class="flex items-center justify-center w-6 h-6 rounded-full bg-rose-50 text-rose-600">
                                                    <i data-lucide="alert-triangle" class="w-3.5 h-3.5"></i>
                                                </span>
                                            @elseif(in_array($alert->action, ['CHANGE_PASSWORD', 'UPDATE_SETTINGS', 'UPDATE_TEMPLATE']))
                                                <span class="flex items-center justify-center w-6 h-6 rounded-full bg-amber-50 text-amber-600">
                                                    <i data-lucide="settings" class="w-3.5 h-3.5"></i>
                                                </span>
                                            @else
                                                <span class="flex items-center justify-center w-6 h-6 rounded-full bg-blue-50 text-royal-600">
                                                    <i data-lucide="info" class="w-3.5 h-3.5"></i>
                                                </span>
                                            @endif
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-xxs font-semibold text-slate-900 truncate">{{ $alert->action }}</p>
                                            <p class="text-xxs text-slate-500 mt-0.5 line-clamp-2 leading-relaxed">{{ $alert->description }}</p>
                                            <p class="text-[9px] text-slate-400 mt-1 flex items-center">
                                                <i data-lucide="clock" class="w-2.5 h-2.5 mr-0.5"></i>
                                                {{ $alert->created_at->diffForHumans() }}
                                            </p>
                                        </div>
                                    </div>
                                @empty
                                    <div class="px-4 py-6 text-center">
                                        <i data-lucide="bell-off" class="w-8 h-8 text-slate-300 mx-auto mb-2"></i>
                                        <p class="text-xxs text-slate-400">Tidak ada notifikasi baru</p>
                                    </div>
                                @endforelse
                            </div>

                            <div class="p-2 border-t border-slate-100 bg-slate-50/50">
                                <a href="{{ route('admin.logs') }}" class="block text-center text-xxs font-bold text-royal-600 hover:text-royal-800 transition-colors py-1">
                                    Lihat Semua Log Audit
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Vertical Divider Line -->
                    <div class="h-6 w-px bg-slate-200"></div>
                @endif

                <!-- Profile Dropdown Component -->
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" @click.away="open = false" class="flex items-center space-x-2 focus:outline-hidden">
                        <img class="w-9 h-9 rounded-full object-cover ring-2 ring-royal-100/50 bg-royal-50" 
                             src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name ?? 'Biro Akademik') }}&background=1d4ed8&color=ffffff&bold=true" 
                             alt="Avatar">
                        <span class="hidden md:block text-sm font-semibold text-slate-700 hover:text-slate-900 transition-colors select-none">
                            {{ Auth::user()->name ?? 'Biro Akademik' }}
                        </span>
                        <i data-lucide="chevron-down" class="w-4 h-4 text-slate-400 transition-transform duration-200" :class="open ? 'rotate-180' : ''"></i>
                    </button>

                    <!-- Dropdown Panel -->
                    <div x-show="open"
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="transform opacity-0 scale-95"
                         x-transition:enter-end="transform opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="transform opacity-100 scale-100"
                         x-transition:leave-end="transform opacity-0 scale-95"
                         class="absolute right-0 mt-2.5 w-48 rounded-xl bg-white border border-slate-200 shadow-lg py-1.5 text-sm text-slate-700 z-50"
                         style="display: none;">
                        
                        <div class="px-4 py-2 border-b border-slate-100">
                            <p class="font-medium text-slate-950 truncate">{{ Auth::user()->email ?? 'admin@royal.ac.id' }}</p>
                            <p class="text-xs text-slate-400 mt-0.5 truncate">{{ Auth::user()->name ?? 'Administrator' }}</p>
                        </div>

                        @if(($userRole ?? (Auth::user()->role ?? 'admin')) === 'mahasiswa')
                            <a href="{{ route('student.dashboard') }}" class="block px-4 py-2 hover:bg-slate-50 hover:text-slate-900 transition-colors">
                                Profil Saya
                            </a>
                            <a href="{{ route('student.dashboard') }}?change_password=1#change-password-section" class="block px-4 py-2 hover:bg-slate-50 hover:text-slate-900 transition-colors">
                                Ubah Sandi
                            </a>
                        @else
                            <a href="{{ route('admin.settings') }}?tab=security" class="block px-4 py-2 hover:bg-slate-50 hover:text-slate-900 transition-colors">
                                Ubah Sandi
                            </a>
                        @endif
                        
                        <div class="border-t border-slate-100 my-1"></div>
                        
                        <a href="#" 
                           onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                           class="block px-4 py-2 text-red-600 hover:bg-slate-50 transition-colors font-medium">
                            Keluar
                        </a>
                    </div>
                </div>

            </div>
        </header>

        <!-- Main Page View Content -->
        <main class="flex-1 p-6 sm:p-8 md:p-10">
            <!-- Dynamic Page Title and Action Buttons -->
            <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8 space-y-4 md:space-y-0">
                <div>
                    <h2 class="text-2xl font-bold text-slate-950 tracking-tight">@yield('page-title', 'Dashboard')</h2>
                    <p class="text-slate-500 text-sm mt-1">@yield('page-subtitle', 'Selamat datang kembali di panel administrasi Anda.')</p>
                </div>
                <div class="flex items-center space-x-3">
                    @yield('page-actions')
                </div>
            </div>

            <!-- Page Specific Main Cards/Containers -->
            <div class="fade-in">
                @yield('content')
            </div>
        </main>

        <!-- Academic Institutional Footer -->
        <footer class="py-6 px-6 bg-white border-t border-slate-200 text-center text-xs text-slate-400">
            <p>&copy; {{ date('Y') }} <strong>Universitas Royal</strong>. Royal Academic Document System v1.0. Seluruh Hak Cipta Dilindungi.</p>
        </footer>

    </div>

    <!-- Initialize Lucide Icons at the end of the load -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            lucide.createIcons();
        });
    </script>
    @yield('scripts')
</body>
</html>
