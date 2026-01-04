<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print QR - {{ $table->name }}</title>

    {{-- Google Font: Plus Jakarta Sans --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">

    {{-- Remix Icon --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@4.0.0/fonts/remixicon.css" />

    {{-- Tailwind CDN --}}
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #050505;
            background-image:
                radial-gradient(at 0% 0%, rgba(250, 154, 8, 0.15) 0px, transparent 50%),
                radial-gradient(at 100% 100%, rgba(250, 154, 8, 0.05) 0px, transparent 50%);
        }

        /* Modern Sleek Gradient for Header */
        .mesh-gradient {
            background: rgb(250, 154, 8);
            background: linear-gradient(145deg, rgba(250, 154, 8, 1) 0%, rgba(255, 191, 36, 1) 45%, rgba(234, 88, 12, 1) 100%);
        }

        /* Grainy Overlay effect */
        .grain-texture::before {
            content: "";
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            opacity: 0.2;
            pointer-events: none;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3联%3Cfilter id='noiseFilter'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.65' numOctaves='3' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noiseFilter)'/%3E%3C/svg%3E");
        }

        @media print {
            .no-print {
                display: none !important;
            }

            body {
                background: white !important;
                padding: 0 !important;
            }

            .print-card {
                box-shadow: none !important;
                border: 1px solid #eee !important;
                margin: 0 auto !important;
            }

            .mesh-gradient {
                background: var(--primary-color) !important;
                -webkit-print-color-adjust: exact;
            }
        }
    </style>
</head>

<body
    class="min-h-screen flex flex-col items-center justify-center p-6 lg:p-12 selection:text-black" style="selection:background-color: var(--primary-color);">

    <!-- NAVIGATION BAR -->
    <div
        class="no-print mb-12 flex items-center gap-4 bg-white/5 p-2 rounded-lg border border-white/5 backdrop-blur-md">
        <a href="{{ route('admin.tables.index') }}"
            class="group flex items-center gap-2 px-5 py-2.5 text-slate-400 hover:text-white transition-all duration-300 text-[10px] font-black uppercase tracking-widest">
            <i class="ri-arrow-left-s-line text-lg transition-transform group-hover:-translate-x-1"></i> Kembali
        </a>
        <div class="w-px h-4 bg-white/10"></div>
        <button onclick="window.print()"
            class="flex items-center gap-2 px-8 py-2.5 text-black rounded-md font-black uppercase tracking-widest text-[10px] transition-all duration-300 active:scale-95 btn-primary" style="box-shadow: 0 0 20px rgba(var(--primary-color-rgb), 0.3);">
            <i class="ri-printer-line text-sm"></i> Print Masterpiece
        </button>
    </div>

    <!-- MODERN POSTER QR -->
    <div
        class="print-card bg-[#0A0A0A] w-full max-w-[420px] rounded-lg overflow-hidden shadow-[0_32px_64px_-16px_rgba(0,0,0,0.5)] border border-white/5 relative">

        <!-- Header Section: Mesh Gradient -->
        <div class="mesh-gradient grain-texture p-12 text-center relative overflow-hidden">
            <div class="relative z-10">
                <h1 class="text-4xl font-extrabold italic tracking-tighter text-black uppercase leading-none">
                    CLASS<span class="text-white drop-shadow-sm">BILLIARD</span>
                </h1>
                <div class="inline-block mt-3 px-3 py-1 bg-black/10 rounded-full">
                    <p class="text-[9px] font-black uppercase tracking-[0.4em] text-black">Premium Table Access</p>
                </div>
            </div>
        </div>

        <!-- Body Section: Soft Inset Glow -->
        <div class="bg-white px-10 py-12 flex flex-col items-center justify-center relative">
            <!-- Subtle Radial Shadow for QR -->
            <div
                class="absolute inset-0 bg-[radial-gradient(circle_at_50%_40%,rgba(250,154,8,0.03),transparent)] pointer-events-none">
            </div>

            <!-- QR Container: Precision Border -->
            <div
                class="relative p-6 bg-white border border-slate-100 rounded-lg shadow-[0_10px_30px_-10px_rgba(0,0,0,0.1)] mb-10 transition-transform hover:scale-[1.02] duration-500">
                @if($table->qrcode && Storage::disk('public')->exists($table->qrcode))
                    <img src="{{ asset('storage/' . $table->qrcode) }}" alt="QR {{ $table->name }}"
                        class="w-[240px] h-[240px] object-contain relative z-10">
                @else
                    <div
                        class="w-[240px] h-[240px] bg-slate-50 rounded-md flex flex-col items-center justify-center border border-dashed border-slate-200">
                        <i class="ri-qr-code-line text-4xl text-slate-300"></i>
                    </div>
                @endif

                <!-- Corner Accents -->
                <div
                    class="absolute top-0 left-0 w-4 h-4 border-t-2 border-l-2 -translate-x-1 -translate-y-1"
                    style="border-color: rgba(var(--primary-color-rgb), 0.3);">
                </div>
                <div
                    class="absolute bottom-0 right-0 w-4 h-4 border-b-2 border-r-2 translate-x-1 translate-y-1"
                    style="border-color: rgba(var(--primary-color-rgb), 0.3);">
                </div>
            </div>

            <div class="text-center">
                <p class="text-[10px] uppercase tracking-[0.3em] font-black mb-3" style="color: var(--primary-color);">Authentication Link</p>
                <h2 class="text-6xl font-black text-slate-900 uppercase tracking-tighter italic leading-none">
                    {{ $table->name }}
                </h2>
                <div class="mt-4 flex items-center justify-center gap-2">
                    <span class="h-px w-8 bg-slate-200"></span>
                    <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest">
                        {{ $table->room ?? 'Operational Area' }}</p>
                    <span class="h-px w-8 bg-slate-200"></span>
                </div>
            </div>
        </div>

        <!-- Footer Section: Dark Mode Sleek -->
        <div class="bg-[#050505] p-10 relative">
            <div class="flex items-center justify-between gap-4 mb-10">
                <div class="flex flex-col items-center gap-2 group cursor-default">
                    <div
                        class="w-10 h-10 rounded-md bg-white/[0.03] border border-white/5 flex items-center justify-center text-black group-hover:text-black transition-all duration-500"
                        style="color: var(--primary-color);"
                        @mouseenter="$el.style.backgroundColor = 'var(--primary-color)'"
                        @mouseleave="$el.style.backgroundColor = 'rgba(255,255,255,0.03)'">
                        <i class="ri-restaurant-2-line text-lg"></i>
                    </div>
                    <span class="text-[8px] font-black uppercase text-white/40 tracking-[0.2em]">Eatery</span>
                </div>
                <div class="h-8 w-px bg-white/5"></div>
                <div class="flex flex-col items-center gap-2 group cursor-default">
                    <div
                        class="w-10 h-10 rounded-md bg-white/[0.03] border border-white/5 flex items-center justify-center text-black group-hover:text-black transition-all duration-500"
                        style="color: var(--primary-color);"
                        @mouseenter="$el.style.backgroundColor = 'var(--primary-color)'"
                        @mouseleave="$el.style.backgroundColor = 'rgba(255,255,255,0.03)'">
                        <i class="ri-billiards-line text-lg"></i>
                    </div>
                    <span class="text-[8px] font-black uppercase text-white/40 tracking-[0.2em]">Billiard</span>
                </div>
                <div class="h-8 w-px bg-white/5"></div>
                <div class="flex flex-col items-center gap-2 group cursor-default">
                    <div
                        class="w-10 h-10 rounded-md bg-white/[0.03] border border-white/5 flex items-center justify-center text-black group-hover:text-black transition-all duration-500"
                        style="color: var(--primary-color);"
                        @mouseenter="$el.style.backgroundColor = 'var(--primary-color)'"
                        @mouseleave="$el.style.backgroundColor = 'rgba(255,255,255,0.03)'">
                        <i class="ri-wifi-line text-lg"></i>
                    </div>
                    <span class="text-[8px] font-black uppercase text-white/40 tracking-[0.2em]">Wifi</span>
                </div>
            </div>

            <div class="bg-white/[0.02] border border-white/5 p-4 rounded-md text-center">
                <p class="text-[10px] text-slate-500 font-medium leading-relaxed tracking-tight">
                    Scan the code to explore our digital menu and <br class="hidden lg:block"> enjoy seamless table
                    service.
                </p>
            </div>

            <p class="text-[7px] mt-8 text-center text-white/10 font-mono tracking-widest uppercase truncate px-4">
                Access Node: {{ $url }}
            </p>
        </div>
    </div>

    <!-- Printing Instruction -->
    <div class="no-print mt-10 flex items-center gap-4 text-slate-500">
        <div class="flex -space-x-2">
            <div
                class="w-8 h-8 rounded-full flex items-center justify-center"
                style="background-color: rgba(var(--primary-color-rgb), 0.2); border: 1px solid rgba(var(--primary-color-rgb), 0.2); color: var(--primary-color);">
                <i class="ri-lightbulb-flash-line"></i>
            </div>
        </div>
        <p class="text-[10px] font-medium tracking-tight">
            Use <span class="text-white font-bold italic underline" style="text-decoration-color: var(--primary-color);">Glossy Heavyweight Paper</span>
            for the best modern result.
        </p>
    </div>

</body>

</html>