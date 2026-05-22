@extends('layouts.app')

@section('title', 'Template Ijazah - Royal Academic Print Suite')
@section('page-title', 'Pengaturan Template & Tata Letak')
@section('page-subtitle', 'Unggah latar belakang ijazah dan sesuaikan koordinat posisi teks secara visual.')

@section('content')
<div x-data="{
    activeTemplateToEdit: null,
    selectedField: 'name',
    fieldsConfig: {},
    isDragging: false,
    draggedField: null,
    saving: false,
    saveSuccess: false,
    saveError: '',

    initEditor(template) {
        this.activeTemplateToEdit = template;
        // Deep copy the fields config
        this.fieldsConfig = JSON.parse(JSON.stringify(template.fields_config));
        this.selectedField = 'name';
        this.saveSuccess = false;
        this.saveError = '';
        
        // Scroll to editor
        setTimeout(() => {
            document.getElementById('visual-editor-section').scrollIntoView({ behavior: 'smooth' });
        }, 100);
    },

    dragStart(e, field) {
        this.isDragging = true;
        this.draggedField = field;
        this.selectedField = field;
        e.preventDefault();
    },

    dragMove(e) {
        if (!this.isDragging || !this.draggedField) return;
        
        const canvas = this.$refs.canvasContainer;
        const rect = canvas.getBoundingClientRect();
        
        // Handle touch or mouse client coordinates
        let clientX = e.clientX;
        let clientY = e.clientY;
        if (e.touches && e.touches.length > 0) {
            clientX = e.touches[0].clientX;
            clientY = e.touches[0].clientY;
        }
        
        let x = clientX - rect.left;
        let y = clientY - rect.top;
        
        // Convert to percentage of canvas width/height
        let leftPercent = Math.round((x / rect.width) * 100);
        let topPercent = Math.round((y / rect.height) * 100);
        
        // Constrain boundaries (0 - 100)
        leftPercent = Math.max(0, Math.min(100, leftPercent));
        topPercent = Math.max(0, Math.min(100, topPercent));
        
        if (this.draggedField === 'qr_code') {
            this.fieldsConfig[this.draggedField].left = leftPercent;
            this.fieldsConfig[this.draggedField].top = topPercent;
        } else {
            this.fieldsConfig[this.draggedField].left = leftPercent;
            this.fieldsConfig[this.draggedField].top = topPercent;
        }
    },

    dragEnd() {
        this.isDragging = false;
        this.draggedField = null;
    },

    async saveLayout() {
        this.saving = true;
        this.saveSuccess = false;
        this.saveError = '';
        
        try {
            const response = await fetch(`/admin/templates/${this.activeTemplateToEdit.id}/config`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=&quot;csrf-token&quot;]').getAttribute('content')
                },
                body: JSON.stringify({ fields_config: this.fieldsConfig })
            });
            
            const result = await response.json();
            this.saving = false;
            
            if (result.success) {
                this.saveSuccess = true;
                // Update local activeTemplateToEdit data
                this.activeTemplateToEdit.fields_config = JSON.parse(JSON.stringify(this.fieldsConfig));
                
                // Hide alert after 3 seconds
                setTimeout(() => { this.saveSuccess = false; }, 3000);
            } else {
                this.saveError = result.message || 'Gagal menyimpan tata letak.';
            }
        } catch (err) {
            this.saving = false;
            this.saveError = 'Terjadi kesalahan sistem saat menyimpan koordinat.';
        }
    }
}" 
@pointermove.window="dragMove($event)"
@pointerup.window="dragEnd()"
class="space-y-8">

    <!-- Alerts -->
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

    <!-- Upper Section: Upload Form & Current Active Card -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Col 1 & 2: Template List -->
        <div class="lg:col-span-2 space-y-4">
            <h3 class="text-xs font-bold text-slate-400 uppercase tracking-widest">Daftar Template Tersedia</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @forelse($templates as $tpl)
                <div class="bg-white rounded-2xl border transition-all overflow-hidden flex flex-col group relative {{ $tpl->is_active ? 'border-amber-400 ring-2 ring-amber-400/20 shadow-amber-100/40 shadow-lg' : 'border-slate-200 hover:border-slate-300 shadow-xs' }}">
                    
                    <!-- Background image thumbnail wrapper -->
                    <div class="aspect-video bg-slate-50 border-b border-slate-100 overflow-hidden relative">
                        <img src="{{ asset($tpl->background_path) }}" alt="{{ $tpl->name }}" class="w-full h-full object-cover group-hover:scale-[1.02] transition-transform duration-500">
                        @if($tpl->is_active)
                        <span class="absolute top-3 left-3 bg-amber-500 text-white font-black text-xxs px-2.5 py-1 rounded-full uppercase tracking-wider shadow-sm flex items-center">
                            <i data-lucide="award" class="w-3 h-3 mr-1"></i>
                            Template Aktif
                        </span>
                        @endif
                    </div>

                    <!-- Details and actions -->
                    <div class="p-4 flex-1 flex flex-col justify-between space-y-3">
                        <div>
                            <h4 class="font-bold text-slate-900 truncate">{{ $tpl->name }}</h4>
                            <p class="text-xxs text-slate-400 mt-0.5">Dibuat {{ $tpl->created_at->format('d M Y') }} • Ukuran {{ $tpl->canvas_width }}x{{ $tpl->canvas_height }}px</p>
                        </div>

                        <div class="flex flex-wrap gap-1.5 pt-1">
                            @if(!$tpl->is_active)
                            <form action="{{ route('admin.templates.activate', $tpl->id) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="px-2.5 py-1.5 bg-slate-900 hover:bg-slate-800 text-white font-bold text-xxs rounded-lg transition-colors cursor-pointer flex items-center">
                                    <i data-lucide="power" class="w-3.5 h-3.5 mr-1 text-emerald-400"></i>
                                    Aktifkan
                                </button>
                            </form>
                            @endif

                            <button @click="initEditor({{ json_encode($tpl) }})" class="px-2.5 py-1.5 bg-royal-50 hover:bg-royal-100 text-royal-700 font-bold text-xxs rounded-lg transition-all cursor-pointer flex items-center">
                                <i data-lucide="sliders" class="w-3.5 h-3.5 mr-1"></i>
                                Tata Letak
                            </button>

                            <form action="{{ route('admin.templates.destroy', $tpl->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus template ini secara permanen?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-1.5 text-slate-400 hover:text-red-650 hover:bg-red-50 rounded-lg transition-colors cursor-pointer">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                @empty
                <div class="md:col-span-2 bg-white rounded-2xl border border-slate-200 p-8 text-center text-slate-400 text-xs">
                    <i data-lucide="image-off" class="w-12 h-12 text-slate-300 mx-auto mb-3"></i>
                    Belum ada template terunggah. Silakan unggah berkas template ijazah kosong di sebelah kanan.
                </div>
                @endforelse
            </div>
        </div>

        <!-- Col 3: Upload Form -->
        <div class="space-y-4">
            <h3 class="text-xs font-bold text-slate-400 uppercase tracking-widest">Unggah Latar Belakang</h3>
            
            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
                <form action="{{ route('admin.templates.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xxs font-bold text-slate-400 uppercase tracking-wider mb-1.5">Nama Template *</label>
                        <input type="text" name="name" required placeholder="Contoh: Template Utama Universitas" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-xs focus:ring-2 focus:ring-royal-500/20 focus:border-royal-500 focus:outline-hidden">
                    </div>

                    <div>
                        <label class="block text-xxs font-bold text-slate-400 uppercase tracking-wider mb-1.5">Berkas Gambar Latar *</label>
                        <div x-data="{ fileName: '' }" class="relative border-2 border-dashed border-slate-200 hover:border-royal-400 rounded-xl p-6 text-center cursor-pointer transition-colors bg-slate-50/50">
                            <input type="file" name="background" required accept="image/png, image/jpeg, image/jpg" class="absolute inset-0 opacity-0 cursor-pointer" @change="fileName = $event.target.files[0].name">
                            <i data-lucide="cloud-upload" class="w-8 h-8 text-slate-400 mx-auto mb-2"></i>
                            <span class="block text-xxs font-bold text-slate-600" x-text="fileName || 'Klik atau seret file JPG/PNG ke sini'"></span>
                            <span class="block text-[10px] text-slate-400 mt-1">Rekomendasi rasio A4 Landscape (1.414 : 1), max 5MB</span>
                        </div>
                    </div>

                    <button type="submit" class="w-full py-2 bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs rounded-lg transition-colors cursor-pointer flex items-center justify-center">
                        <i data-lucide="plus-circle" class="w-4 h-4 mr-1.5 text-gold-400"></i>
                        Unggah Template
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Active Visual Editor Section Workspace -->
    <div x-show="activeTemplateToEdit" id="visual-editor-section" style="display: none;" class="space-y-4 pt-4 border-t border-slate-200">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h3 class="text-base font-bold text-slate-950 flex items-center">
                    <span class="w-2 h-2 rounded-full bg-royal-500 mr-2.5"></span>
                    Visual Layout Editor: <span x-text="activeTemplateToEdit ? activeTemplateToEdit.name : ''" class="ml-1 text-royal-600"></span>
                </h3>
                <p class="text-xs text-slate-400">Pilih elemen dengan mengeklik langsung pada kanvas ijazah di bawah atau gunakan panel samping, geser menggunakan mouse atau atur presisi dengan slider.</p>
            </div>
            
            <div class="flex items-center space-x-2">
                <button @click="activeTemplateToEdit = null" class="px-3.5 py-1.5 border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 font-bold text-xs rounded-lg transition-colors cursor-pointer flex items-center">
                    Tutup Editor
                </button>
                <button @click="saveLayout()" :disabled="saving" class="px-4 py-1.5 bg-royal-600 hover:bg-royal-700 text-white font-bold text-xs rounded-lg transition-colors shadow-sm shadow-royal-950/10 cursor-pointer flex items-center">
                    <template x-if="saving">
                        <span class="flex items-center"><i data-lucide="loader" class="w-4 h-4 mr-1.5 animate-spin"></i>Menyimpan...</span>
                    </template>
                    <template x-if="!saving">
                        <span class="flex items-center"><i data-lucide="save" class="w-4 h-4 mr-1.5 text-gold-400"></i>Simpan Tata Letak</span>
                    </template>
                </button>
            </div>
        </div>

        <!-- Custom Editor Workspace Container Split Screen -->
        <div class="grid grid-cols-1 xl:grid-cols-4 gap-6 bg-slate-900/5 p-4 rounded-3xl border border-slate-200">
            
            <!-- Col 1 Sidebar Controls (Left) -->
            <div class="xl:col-span-1 bg-white p-5 rounded-2xl border border-slate-200 flex flex-col justify-between space-y-6">
                
                <!-- Selector List -->
                <div class="space-y-4">
                    <div>
                        <label class="block text-xxs font-bold text-slate-400 uppercase tracking-wider mb-2">Pilih Elemen Teks</label>
                        <select x-model="selectedField" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-xs focus:ring-2 focus:ring-royal-500/20 focus:border-royal-500 select-none">
                            <option value="certificate_number">Nomor Ijazah</option>
                            <option value="name">Nama Mahasiswa</option>
                            <option value="nim">NIM</option>
                            <option value="faculty">Fakultas</option>
                            <option value="major">Program Studi</option>
                            <option value="degree">Gelar Akademik</option>
                            <option value="graduation_date">Tanggal Kelulusan</option>
                            <option value="qr_code">QR Code Verifikasi</option>
                        </select>
                    </div>

                    <div class="border-t border-slate-100 pt-4 space-y-4" x-if="selectedField && fieldsConfig[selectedField]">
                        
                        <!-- Visibility Toggle -->
                        <div class="flex items-center justify-between">
                            <span class="text-xxs font-bold text-slate-600 uppercase tracking-wider">Tampilkan Elemen</span>
                            <button @click="fieldsConfig[selectedField].is_visible = !fieldsConfig[selectedField].is_visible" 
                                    class="w-10 h-6 rounded-full p-1 transition-colors duration-200 cursor-pointer"
                                    :class="fieldsConfig[selectedField].is_visible ? 'bg-royal-600' : 'bg-slate-250'">
                                <div class="bg-white w-4 h-4 rounded-full shadow-md transform duration-200" 
                                     :class="fieldsConfig[selectedField].is_visible ? 'translate-x-4' : 'translate-x-0'"></div>
                            </button>
                        </div>

                        <!-- Position Y Slider -->
                        <div class="space-y-1">
                            <div class="flex justify-between items-center">
                                <span class="text-xxs font-bold text-slate-600 uppercase tracking-wider">Posisi Vertikal (Top)</span>
                                <span class="text-xxs font-mono font-bold text-royal-600" x-text="fieldsConfig[selectedField].top + '%'"></span>
                            </div>
                            <input type="range" min="0" max="100" x-model.number="fieldsConfig[selectedField].top" class="w-full h-1 bg-slate-200 rounded-lg appearance-none cursor-pointer accent-royal-600 focus:outline-hidden">
                        </div>

                        <!-- Position X Slider -->
                        <div class="space-y-1">
                            <div class="flex justify-between items-center">
                                <span class="text-xxs font-bold text-slate-600 uppercase tracking-wider">Posisi Horisontal (Left)</span>
                                <span class="text-xxs font-mono font-bold text-royal-600" x-text="fieldsConfig[selectedField].left + '%'"></span>
                            </div>
                            <input type="range" min="0" max="100" x-model.number="fieldsConfig[selectedField].left" class="w-full h-1 bg-slate-200 rounded-lg appearance-none cursor-pointer accent-royal-600 focus:outline-hidden">
                        </div>

                        <!-- Typography Specifics (Not applicable to QR) -->
                        <template x-if="selectedField !== 'qr_code'">
                            <div class="space-y-4 pt-1">
                                <!-- Font Size Slider -->
                                <div class="space-y-1">
                                    <div class="flex justify-between items-center">
                                        <span class="text-xxs font-bold text-slate-600 uppercase tracking-wider">Ukuran Font (Px)</span>
                                        <span class="text-xxs font-mono font-bold text-royal-600" x-text="fieldsConfig[selectedField].font_size + 'px'"></span>
                                    </div>
                                    <input type="range" min="8" max="72" x-model.number="fieldsConfig[selectedField].font_size" class="w-full h-1 bg-slate-200 rounded-lg appearance-none cursor-pointer accent-royal-600 focus:outline-hidden">
                                </div>

                                <!-- Text Align -->
                                <div>
                                    <span class="block text-xxs font-bold text-slate-600 uppercase tracking-wider mb-1.5">Penjajaran Teks</span>
                                    <div class="grid grid-cols-3 gap-1 bg-slate-50 p-0.5 rounded-lg border border-slate-150">
                                        <button @click="fieldsConfig[selectedField].text_align = 'left'" :class="fieldsConfig[selectedField].text_align === 'left' ? 'bg-white text-slate-900 shadow-xs' : 'text-slate-400 hover:text-slate-700'" class="py-1 text-center font-bold text-[10px] rounded transition-all cursor-pointer">Kiri</button>
                                        <button @click="fieldsConfig[selectedField].text_align = 'center'" :class="fieldsConfig[selectedField].text_align === 'center' ? 'bg-white text-slate-900 shadow-xs' : 'text-slate-400 hover:text-slate-700'" class="py-1 text-center font-bold text-[10px] rounded transition-all cursor-pointer">Tengah</button>
                                        <button @click="fieldsConfig[selectedField].text_align = 'right'" :class="fieldsConfig[selectedField].text_align === 'right' ? 'bg-white text-slate-900 shadow-xs' : 'text-slate-400 hover:text-slate-700'" class="py-1 text-center font-bold text-[10px] rounded transition-all cursor-pointer">Kanan</button>
                                    </div>
                                </div>

                                <!-- Bold and Color Row -->
                                <div class="grid grid-cols-2 gap-3 items-end">
                                    <div>
                                        <span class="block text-xxs font-bold text-slate-600 uppercase tracking-wider mb-1.5">Ketebalan</span>
                                        <button @click="fieldsConfig[selectedField].font_weight = (fieldsConfig[selectedField].font_weight === 'bold' ? 'normal' : 'bold')" 
                                                class="w-full py-1.5 px-3 border rounded-lg text-xxs font-extrabold cursor-pointer transition-colors text-center"
                                                :class="fieldsConfig[selectedField].font_weight === 'bold' ? 'border-royal-500 bg-royal-50 text-royal-700' : 'border-slate-200 bg-white text-slate-600 hover:bg-slate-50'">
                                            B - Tebal
                                        </button>
                                    </div>
                                    <div>
                                        <span class="block text-xxs font-bold text-slate-600 uppercase tracking-wider mb-1.5">Warna Font</span>
                                        <div class="flex items-center space-x-1.5">
                                            <input type="color" x-model="fieldsConfig[selectedField].color" class="w-8 h-8 rounded-lg border border-slate-200 cursor-pointer overflow-hidden p-0">
                                            <input type="text" x-model="fieldsConfig[selectedField].color" class="w-full px-2 py-1.5 border border-slate-200 rounded-lg text-xxs font-mono focus:outline-hidden">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>

                        <!-- QR Code Size slider (Only for QR Code) -->
                        <template x-if="selectedField === 'qr_code'">
                            <div class="space-y-2">
                                <div class="flex justify-between items-center">
                                    <span class="text-xxs font-bold text-slate-600 uppercase tracking-wider">Ukuran QR Code (Px)</span>
                                    <span class="text-xxs font-mono font-bold text-royal-600" x-text="fieldsConfig[selectedField].size + 'px'"></span>
                                </div>
                                <input type="range" min="40" max="150" x-model.number="fieldsConfig[selectedField].size" class="w-full h-1 bg-slate-200 rounded-lg appearance-none cursor-pointer accent-royal-600 focus:outline-hidden">
                            </div>
                        </template>

                    </div>
                </div>

                <!-- Alert success/error inside sidebar -->
                <div class="pt-4 border-t border-slate-100">
                    <template x-if="saveSuccess">
                        <div class="p-3 bg-emerald-50 text-emerald-800 rounded-xl text-xxs font-bold border border-emerald-150 flex items-center animate-pulse">
                            <i data-lucide="check" class="w-4 h-4 mr-1.5 text-emerald-500"></i>
                            Koordinat berhasil disimpan!
                        </div>
                    </template>
                    <template x-if="saveError">
                        <div class="p-3 bg-red-50 text-red-800 rounded-xl text-xxs font-bold border border-red-150" x-text="saveError"></div>
                    </template>
                </div>

            </div>

            <!-- Col 2,3,4 Visual Canvas Panel (Right) -->
            <div class="xl:col-span-3 flex items-center justify-center p-4 bg-slate-950/20 rounded-2xl border border-slate-200/50 overflow-hidden min-h-[480px]">
                
                <!-- Scaling / Relative container for Canvas -->
                <div class="relative w-full max-w-[800px] border shadow-2xl rounded-sm aspect-[1.414/1] bg-white overflow-hidden select-none"
                     x-ref="canvasContainer"
                     style="font-family: 'EB Garamond', serif;">
                    
                    <!-- Background Template Image -->
                    <img :src="activeTemplateToEdit ? '/' + activeTemplateToEdit.background_path : ''" class="absolute inset-0 w-full h-full object-cover pointer-events-none select-none">
                    
                    <!-- TEXT ELEMENT OVERLAYS -->
                    
                    <!-- 1. Nomor Ijazah -->
                    <div x-show="fieldsConfig.certificate_number && fieldsConfig.certificate_number.is_visible"
                         @pointerdown="dragStart($event, 'certificate_number')"
                         :class="selectedField === 'certificate_number' ? 'border-2 border-royal-500 ring-2 ring-royal-500/20 bg-white/20' : 'hover:border border-slate-350'"
                         class="absolute z-10 px-2 py-0.5 rounded cursor-move select-none transform -translate-x-1/2 -translate-y-1/2 select-none"
                         :style="{
                             top: fieldsConfig.certificate_number ? fieldsConfig.certificate_number.top + '%' : '15%',
                             left: fieldsConfig.certificate_number ? fieldsConfig.certificate_number.left + '%' : '50%',
                             fontSize: fieldsConfig.certificate_number ? (fieldsConfig.certificate_number.font_size / 800 * 100) + 'cw' : '1.8%',
                             fontWeight: fieldsConfig.certificate_number ? fieldsConfig.certificate_number.font_weight : 'normal',
                             textAlign: fieldsConfig.certificate_number ? fieldsConfig.certificate_number.text_align : 'center',
                             color: fieldsConfig.certificate_number ? fieldsConfig.certificate_number.color : '#64748b'
                         }">
                        No. 105/UNROY/SI/S1/2026
                    </div>

                    <!-- 2. Nama Mahasiswa -->
                    <div x-show="fieldsConfig.name && fieldsConfig.name.is_visible"
                         @pointerdown="dragStart($event, 'name')"
                         :class="selectedField === 'name' ? 'border-2 border-royal-500 ring-2 ring-royal-500/20 bg-white/20' : 'hover:border border-slate-350'"
                         class="absolute z-10 px-3 py-1 rounded cursor-move select-none transform -translate-x-1/2 -translate-y-1/2 whitespace-nowrap select-none font-serif text-[4vw]"
                         :style="{
                             top: fieldsConfig.name ? fieldsConfig.name.top + '%' : '38%',
                             left: fieldsConfig.name ? fieldsConfig.name.left + '%' : '50%',
                             fontSize: fieldsConfig.name ? (fieldsConfig.name.font_size / 800 * 100) + 'cw' : '4.2%',
                             fontWeight: fieldsConfig.name ? fieldsConfig.name.font_weight : 'bold',
                             textAlign: fieldsConfig.name ? fieldsConfig.name.text_align : 'center',
                             color: fieldsConfig.name ? fieldsConfig.name.color : '#1d4ed8'
                         }">
                        Naufal Syarifuddin
                    </div>

                    <!-- 3. NIM -->
                    <div x-show="fieldsConfig.nim && fieldsConfig.nim.is_visible"
                         @pointerdown="dragStart($event, 'nim')"
                         :class="selectedField === 'nim' ? 'border-2 border-royal-500 ring-2 ring-royal-500/20 bg-white/20' : 'hover:border border-slate-350'"
                         class="absolute z-10 px-2 py-0.5 rounded cursor-move select-none transform -translate-x-1/2 -translate-y-1/2 select-none"
                         :style="{
                             top: fieldsConfig.nim ? fieldsConfig.nim.top + '%' : '45%',
                             left: fieldsConfig.nim ? fieldsConfig.nim.left + '%' : '50%',
                             fontSize: fieldsConfig.nim ? (fieldsConfig.nim.font_size / 800 * 100) + 'cw' : '2%',
                             fontWeight: fieldsConfig.nim ? fieldsConfig.nim.font_weight : 'normal',
                             textAlign: fieldsConfig.nim ? fieldsConfig.nim.text_align : 'center',
                             color: fieldsConfig.nim ? fieldsConfig.nim.color : '#475569'
                         }">
                        NIM. 23220465
                    </div>

                    <!-- 4. Fakultas -->
                    <div x-show="fieldsConfig.faculty && fieldsConfig.faculty.is_visible"
                         @pointerdown="dragStart($event, 'faculty')"
                         :class="selectedField === 'faculty' ? 'border-2 border-royal-500 ring-2 ring-royal-500/20 bg-white/20' : 'hover:border border-slate-350'"
                         class="absolute z-10 px-2 py-0.5 rounded cursor-move select-none transform -translate-x-1/2 -translate-y-1/2 select-none"
                         :style="{
                             top: fieldsConfig.faculty ? fieldsConfig.faculty.top + '%' : '53%',
                             left: fieldsConfig.faculty ? fieldsConfig.faculty.left + '%' : '50%',
                             fontSize: fieldsConfig.faculty ? (fieldsConfig.faculty.font_size / 800 * 100) + 'cw' : '2%',
                             fontWeight: fieldsConfig.faculty ? fieldsConfig.faculty.font_weight : 'normal',
                             textAlign: fieldsConfig.faculty ? fieldsConfig.faculty.text_align : 'center',
                             color: fieldsConfig.faculty ? fieldsConfig.faculty.color : '#475569'
                         }">
                        Fakultas Ilmu Komputer
                    </div>

                    <!-- 5. Program Studi -->
                    <div x-show="fieldsConfig.major && fieldsConfig.major.is_visible"
                         @pointerdown="dragStart($event, 'major')"
                         :class="selectedField === 'major' ? 'border-2 border-royal-500 ring-2 ring-royal-500/20 bg-white/20' : 'hover:border border-slate-350'"
                         class="absolute z-10 px-2 py-0.5 rounded cursor-move select-none transform -translate-x-1/2 -translate-y-1/2 select-none"
                         :style="{
                             top: fieldsConfig.major ? fieldsConfig.major.top + '%' : '59%',
                             left: fieldsConfig.major ? fieldsConfig.major.left + '%' : '50%',
                             fontSize: fieldsConfig.major ? (fieldsConfig.major.font_size / 800 * 100) + 'cw' : '2.5%',
                             fontWeight: fieldsConfig.major ? fieldsConfig.major.font_weight : 'bold',
                             textAlign: fieldsConfig.major ? fieldsConfig.major.text_align : 'center',
                             color: fieldsConfig.major ? fieldsConfig.major.color : '#0f172a'
                         }">
                        Sistem Informasi
                    </div>

                    <!-- 6. Gelar Akademik -->
                    <div x-show="fieldsConfig.degree && fieldsConfig.degree.is_visible"
                         @pointerdown="dragStart($event, 'degree')"
                         :class="selectedField === 'degree' ? 'border-2 border-royal-500 ring-2 ring-royal-500/20 bg-white/20' : 'hover:border border-slate-350'"
                         class="absolute z-10 px-2 py-0.5 rounded cursor-move select-none transform -translate-x-1/2 -translate-y-1/2 select-none"
                         :style="{
                             top: fieldsConfig.degree ? fieldsConfig.degree.top + '%' : '65%',
                             left: fieldsConfig.degree ? fieldsConfig.degree.left + '%' : '50%',
                             fontSize: fieldsConfig.degree ? (fieldsConfig.degree.font_size / 800 * 100) + 'cw' : '2.2%',
                             fontWeight: fieldsConfig.degree ? fieldsConfig.degree.font_weight : 'bold',
                             textAlign: fieldsConfig.degree ? fieldsConfig.degree.text_align : 'center',
                             color: fieldsConfig.degree ? fieldsConfig.degree.color : '#0f172a'
                         }">
                        Sarjana Komputer (S.Kom.)
                    </div>

                    <!-- 7. Tanggal Kelulusan -->
                    <div x-show="fieldsConfig.graduation_date && fieldsConfig.graduation_date.is_visible"
                         @pointerdown="dragStart($event, 'graduation_date')"
                         :class="selectedField === 'graduation_date' ? 'border-2 border-royal-500 ring-2 ring-royal-500/20 bg-white/20' : 'hover:border border-slate-350'"
                         class="absolute z-10 px-2 py-0.5 rounded cursor-move select-none transform -translate-x-1/2 -translate-y-1/2 select-none"
                         :style="{
                             top: fieldsConfig.graduation_date ? fieldsConfig.graduation_date.top + '%' : '72%',
                             left: fieldsConfig.graduation_date ? fieldsConfig.graduation_date.left + '%' : '50%',
                             fontSize: fieldsConfig.graduation_date ? (fieldsConfig.graduation_date.font_size / 800 * 100) + 'cw' : '1.8%',
                             fontWeight: fieldsConfig.graduation_date ? fieldsConfig.graduation_date.font_weight : 'normal',
                             textAlign: fieldsConfig.graduation_date ? fieldsConfig.graduation_date.text_align : 'center',
                             color: fieldsConfig.graduation_date ? fieldsConfig.graduation_date.color : '#0f172a'
                         }">
                        Lulus di Jakarta, 21 Mei 2026
                    </div>

                    <!-- 8. QR Code Verification -->
                    <div x-show="fieldsConfig.qr_code && fieldsConfig.qr_code.is_visible"
                         @pointerdown="dragStart($event, 'qr_code')"
                         :class="selectedField === 'qr_code' ? 'border-2 border-royal-500 ring-2 ring-royal-500/20' : 'hover:border border-slate-350'"
                         class="absolute z-10 bg-slate-100 flex items-center justify-center cursor-move shadow-md p-1 border border-slate-200 select-none transform -translate-x-1/2 -translate-y-1/2"
                         :style="{
                             top: fieldsConfig.qr_code ? fieldsConfig.qr_code.top + '%' : '78%',
                             left: fieldsConfig.qr_code ? fieldsConfig.qr_code.left + '%' : '80%',
                             width: fieldsConfig.qr_code ? (fieldsConfig.qr_code.size / 800 * 100) + 'cw' : '10%',
                             height: fieldsConfig.qr_code ? (fieldsConfig.qr_code.size / 800 * 100) + 'cw' : '10%'
                         }">
                        <div class="w-full h-full flex flex-col justify-between items-center p-0.5 relative pointer-events-none select-none">
                            <i data-lucide="qr-code" class="w-full h-full text-slate-800"></i>
                            <span class="text-[0.6vw] font-mono text-slate-500 absolute bottom-0 font-bold whitespace-nowrap bg-white px-0.5">VERIFY QR</span>
                        </div>
                    </div>

                </div>

            </div>

        </div>
    </div>

</div>

<!-- CSS Container Query Fallback for responsive text scaling -->
<style>
    @container (min-width: 0px) {
        /* Container-query-like absolute sizing fallback */
    }
    /* Set custom unit 'cw' representing 1% of the canvas container width */
    :root {
        --canvas-w: 800px;
    }
    div[x-ref="canvasContainer"] {
        container-type: inline-size;
        container-name: canvas;
    }
    @container canvas (min-width: 10px) {
        .absolute {
            /* dynamic */
        }
    }
    /* Let's support modern CSS container queries for font scaling perfectly! */
    @supports (font-size: 1cqw) {
        div[x-ref="canvasContainer"] > div {
            font-size: calc(var(--font-size-percent) * 1cqw);
        }
    }
</style>
@endsection
