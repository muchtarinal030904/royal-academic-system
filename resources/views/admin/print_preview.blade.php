<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Cetak Ijazah - Royal Academic Print Suite</title>
    
    <!-- Premium Fonts -->
    <link href="https://fonts.bunny.net/css?family=eb-garamond:400,500,600,700,800" rel="stylesheet" />
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet" />
    
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    
    <!-- QRCode JS Lightweight Generator -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

    <style>
        /* Modern Inter font for the navbar control panel */
        .control-font {
            font-family: 'Inter', sans-serif;
        }

        /* Certificate default premium styling using Garamond */
        .certificate-font {
            font-family: 'EB Garamond', serif;
        }

        /* Responsive preview scaling and zooming */
        .preview-scale-container {
            transition: transform 0.2s ease-in-out;
            transform-origin: top center;
        }

        /* Standard A4 Landscape Dimensions */
        .print-page {
            width: 297mm;
            height: 210mm;
            position: relative;
            background-color: white;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            border: 1px solid #e2e8f0;
            overflow: hidden;
            box-sizing: border-box;
            page-break-after: always;
        }

        /* Absolute positioning wrapper for elements in canvas */
        .element-overlay {
            position: absolute;
            transform: translate(-50%, -50%);
            white-space: nowrap;
            z-index: 10;
        }

        /* QR Code wrapper positioning adjustment */
        .qr-overlay {
            position: absolute;
            transform: translate(-50%, -50%);
            z-index: 10;
            background-color: white;
            border: 1px solid #e2e8f0;
            padding: 4px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        /* Screen CSS centering */
        body {
            background-color: #f1f5f9;
            margin: 0;
            padding: 0;
        }

        /* High precision CSS printing controls */
        @media print {
            body {
                background: white !important;
                color: black !important;
                margin: 0 !important;
                padding: 0 !important;
            }
            .no-print {
                display: none !important;
            }
            .print-wrapper {
                padding: 0 !important;
                margin: 0 !important;
                display: block !important;
            }
            .print-page {
                box-shadow: none !important;
                border: none !important;
                width: 297mm !important;
                height: 210mm !important;
                margin: 0 !important;
                padding: 0 !important;
                page-break-after: always !important;
                overflow: hidden !important;
            }
            @page {
                size: A4 landscape;
                margin: 0;
            }
        }
    </style>
</head>
<body class="min-h-screen flex flex-col">

    <!-- Top Navigation Control Bar (Hidden when printing) -->
    <header class="no-print bg-slate-900 border-b border-slate-800 text-slate-200 px-6 py-4 flex flex-col sm:flex-row justify-between items-center gap-4 sticky top-0 z-50 control-font shadow-md">
        <div class="flex items-center space-x-3">
            @if(isset($isStudentView) && $isStudentView)
            <a href="{{ route('student.dashboard') }}" class="p-2 bg-slate-800 hover:bg-slate-700 text-white rounded-lg transition-colors flex items-center justify-center">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
            </a>
            @else
            <a href="{{ route('admin.print') }}" class="p-2 bg-slate-800 hover:bg-slate-700 text-white rounded-lg transition-colors flex items-center justify-center">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
            </a>
            @endif
            <div>
                <h1 class="text-sm font-bold text-white tracking-wide uppercase">Pratinjau Cetak Ijazah</h1>
                <p class="text-xxs text-slate-400">Total: {{ $students->count() }} Dokumen Mahasiswa • Desain A4 Landscape</p>
            </div>
        </div>

        <!-- Zoom and Print Actions -->
        <div class="flex items-center space-x-4">
            <!-- Zoom controls -->
            <div class="hidden md:flex items-center space-x-2 bg-slate-800 px-2.5 py-1 rounded-lg border border-slate-700">
                <button onclick="zoomOut()" class="p-1 text-slate-400 hover:text-white transition-colors" title="Perkecil">
                    <i data-lucide="minus" class="w-4 h-4"></i>
                </button>
                <span id="zoom-label" class="text-xs font-mono font-bold w-12 text-center text-slate-200">100%</span>
                <button onclick="zoomIn()" class="p-1 text-slate-400 hover:text-white transition-colors" title="Perbesar">
                    <i data-lucide="plus" class="w-4 h-4"></i>
                </button>
            </div>

            <!-- Print Trigger -->
            @if(isset($isStudentView) && $isStudentView)
            <button onclick="window.print()" class="px-5 py-2 bg-royal-600 hover:bg-royal-700 text-white font-extrabold text-xs rounded-lg transition-all shadow-sm shadow-royal-950/20 flex items-center cursor-pointer">
                <i data-lucide="download" class="w-4 h-4 mr-1.5 text-gold-400"></i>
                Unduh / Simpan PDF
            </button>
            @else
            <button onclick="triggerPrint()" class="px-5 py-2 bg-royal-600 hover:bg-royal-700 text-white font-extrabold text-xs rounded-lg transition-all shadow-sm shadow-royal-950/20 flex items-center cursor-pointer">
                <i data-lucide="printer" class="w-4 h-4 mr-1.5 text-gold-400"></i>
                Cetak / Simpan PDF
            </button>
            @endif
        </div>
    </header>

    <!-- Main Workspace for Preview -->
    <main class="flex-1 flex justify-center p-6 md:p-12 print-wrapper overflow-auto">
        <div id="preview-scale-wrapper" class="preview-scale-container space-y-8 flex flex-col items-center">
            
            @php
                $fields = $activeTemplate->fields_config;
            @endphp

            @foreach($students as $st)
            <!-- Page Div (Renders A4 landscape ratio perfectly on screen and page break in printing) -->
            <div class="print-page certificate-font relative shrink-0" id="certificate-page-{{ $st->id }}">
                
                <!-- Background Image (Dynamic from active template) -->
                <img src="{{ asset($activeTemplate->background_path) }}" class="absolute inset-0 w-full h-full object-cover select-none pointer-events-none z-0">
                
                <!-- OVERLAY TEXTS -->
                
                <!-- 1. Nomor Ijazah -->
                @if(isset($fields['certificate_number']) && $fields['certificate_number']['is_visible'])
                <div class="element-overlay" 
                     style="
                        top: {{ $fields['certificate_number']['top'] }}%;
                        left: {{ $fields['certificate_number']['left'] }}%;
                        font-size: {{ $fields['certificate_number']['font_size'] }}px;
                        font-weight: {{ $fields['certificate_number']['font_weight'] }};
                        text-align: {{ $fields['certificate_number']['text_align'] }};
                        color: {{ $fields['certificate_number']['color'] }};
                     ">
                    No. {{ $st->certificate_number ?: 'BELUM TERBIT' }}
                </div>
                @endif

                <!-- 2. Nama Mahasiswa -->
                @if(isset($fields['name']) && $fields['name']['is_visible'])
                <div class="element-overlay" 
                     style="
                        top: {{ $fields['name']['top'] }}%;
                        left: {{ $fields['name']['left'] }}%;
                        font-size: {{ $fields['name']['font_size'] }}px;
                        font-weight: {{ $fields['name']['font_weight'] }};
                        text-align: {{ $fields['name']['text_align'] }};
                        color: {{ $fields['name']['color'] }};
                     ">
                    {{ $st->user->name ?? '-' }}
                </div>
                @endif

                <!-- 3. NIM -->
                @if(isset($fields['nim']) && $fields['nim']['is_visible'])
                <div class="element-overlay" 
                     style="
                        top: {{ $fields['nim']['top'] }}%;
                        left: {{ $fields['nim']['left'] }}%;
                        font-size: {{ $fields['nim']['font_size'] }}px;
                        font-weight: {{ $fields['nim']['font_weight'] }};
                        text-align: {{ $fields['nim']['text_align'] }};
                        color: {{ $fields['nim']['color'] }};
                     ">
                    NIM. {{ $st->nim }}
                </div>
                @endif

                <!-- 4. Fakultas -->
                @if(isset($fields['faculty']) && $fields['faculty']['is_visible'])
                <div class="element-overlay" 
                     style="
                        top: {{ $fields['faculty']['top'] }}%;
                        left: {{ $fields['faculty']['left'] }}%;
                        font-size: {{ $fields['faculty']['font_size'] }}px;
                        font-weight: {{ $fields['faculty']['font_weight'] }};
                        text-align: {{ $fields['faculty']['text_align'] }};
                        color: {{ $fields['faculty']['color'] }};
                     ">
                    {{ $st->faculty }}
                </div>
                @endif

                <!-- 5. Program Studi -->
                @if(isset($fields['major']) && $fields['major']['is_visible'])
                <div class="element-overlay" 
                     style="
                        top: {{ $fields['major']['top'] }}%;
                        left: {{ $fields['major']['left'] }}%;
                        font-size: {{ $fields['major']['font_size'] }}px;
                        font-weight: {{ $fields['major']['font_weight'] }};
                        text-align: {{ $fields['major']['text_align'] }};
                        color: {{ $fields['major']['color'] }};
                     ">
                    {{ $st->major }}
                </div>
                @endif

                <!-- 6. Gelar Akademik -->
                @if(isset($fields['degree']) && $fields['degree']['is_visible'])
                <div class="element-overlay" 
                     style="
                        top: {{ $fields['degree']['top'] }}%;
                        left: {{ $fields['degree']['left'] }}%;
                        font-size: {{ $fields['degree']['font_size'] }}px;
                        font-weight: {{ $fields['degree']['font_weight'] }};
                        text-align: {{ $fields['degree']['text_align'] }};
                        color: {{ $fields['degree']['color'] }};
                     ">
                    Sarjana {{ $st->degree ?: 'Sarjana' }}
                </div>
                @endif

                <!-- 7. Tanggal Kelulusan -->
                @if(isset($fields['graduation_date']) && $fields['graduation_date']['is_visible'])
                <div class="element-overlay" 
                     style="
                        top: {{ $fields['graduation_date']['top'] }}%;
                        left: {{ $fields['graduation_date']['left'] }}%;
                        font-size: {{ $fields['graduation_date']['font_size'] }}px;
                        font-weight: {{ $fields['graduation_date']['font_weight'] }};
                        text-align: {{ $fields['graduation_date']['text_align'] }};
                        color: {{ $fields['graduation_date']['color'] }};
                     ">
                    @if($st->graduation_date)
                        Lulus di Jakarta, {{ $st->graduation_date->translatedFormat('d F Y') }}
                    @else
                        Lulus di Jakarta, {{ now()->translatedFormat('d F Y') }}
                    @endif
                </div>
                @endif

                <!-- 8. QR Code Verification -->
                @if(isset($fields['qr_code']) && $fields['qr_code']['is_visible'])
                <div class="qr-overlay flex flex-col items-center justify-center" 
                     style="
                        top: {{ $fields['qr_code']['top'] }}%;
                        left: {{ $fields['qr_code']['left'] }}%;
                     ">
                    <!-- Dynamic URL points to the public verification endpoint -->
                    <div class="qrcode-canvas" 
                         data-url="{{ route('verification.verify', $st->nim) }}"
                         data-size="{{ $fields['qr_code']['size'] ?? 80 }}"
                         style="width: {{ $fields['qr_code']['size'] ?? 80 }}px; height: {{ $fields['qr_code']['size'] ?? 80 }}px;"></div>
                     <span style="font-size: 8px; font-family: 'Inter', sans-serif; color: #64748b; font-weight: bold; margin-top: 2px;">SCAN UNTUK VERIFIKASI</span>
                </div>
                @endif

            </div>
            @endforeach

        </div>
    </main>

    <!-- Visual Page Controller Scripts -->
    <script>
        // Init Lucide
        lucide.createIcons();

        // 1. Render QR Codes dynamically using local QRCode.js
        document.addEventListener("DOMContentLoaded", function() {
            document.querySelectorAll(".qrcode-canvas").forEach(function(canvas) {
                const url = canvas.getAttribute("data-url");
                const size = parseInt(canvas.getAttribute("data-size")) || 80;
                
                // Clear any fallback contents
                canvas.innerHTML = "";
                
                new QRCode(canvas, {
                    text: url,
                    width: size,
                    height: size,
                    colorDark : "#0f172a",
                    colorLight : "#ffffff",
                    correctLevel : QRCode.CorrectLevel.H
                });
            });
        });

        // 2. Zoom Preview Scale Controls
        let currentScale = 1.0;
        const scaleContainer = document.getElementById("preview-scale-wrapper");
        const zoomLabel = document.getElementById("zoom-label");

        function zoomIn() {
            if (currentScale >= 1.5) return;
            currentScale += 0.1;
            updateZoom();
        }

        function zoomOut() {
            if (currentScale <= 0.5) return;
            currentScale -= 0.1;
            updateZoom();
        }

        function updateZoom() {
            scaleContainer.style.transform = `scale(${currentScale})`;
            zoomLabel.innerText = Math.round(currentScale * 100) + "%";
        }

        // 3. Printing + Automatic DB Status Update POST Action
        function triggerPrint() {
            const studentIds = [ @foreach($students as $st) {{ $st->id }}, @endforeach ];
            
            // Set loading styling on print button
            const printBtn = document.querySelector("button[onclick='triggerPrint()']");
            const originalHTML = printBtn.innerHTML;
            printBtn.innerHTML = `<i data-lucide="loader" class="w-4 h-4 mr-1.5 animate-spin"></i> Memproses...`;
            lucide.createIcons();
            printBtn.disabled = true;

            // POST to update DB printing status to "Sudah Cetak"
            fetch('/admin/certificates/update-status', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ student_ids: studentIds })
            })
            .then(response => response.json())
            .then(data => {
                // Restore button state
                printBtn.innerHTML = originalHTML;
                lucide.createIcons();
                printBtn.disabled = false;

                if (data.success) {
                    // Open print dialog
                    window.print();
                } else {
                    alert('Gagal memperbarui status cetak di database, tetapi dialog cetak tetap akan dibuka. ' + data.message);
                    window.print();
                }
            })
            .catch(error => {
                printBtn.innerHTML = originalHTML;
                lucide.createIcons();
                printBtn.disabled = false;
                
                // Fallback to print anyway if API fails
                console.error('Error updating status:', error);
                window.print();
            });
        }
    </script>
</body>
</html>
