<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Kesalahan pada sistem')</title>
     <script src="https://cdn.tailwindcss.com"></script>
     <link rel="icon" type="image/png" href="/logo.png">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@500;700;800&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Montserrat', sans-serif;
            overflow: hidden;
            background-color: #FEEA6E;
        }

        @keyframes spin {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }

        @keyframes sway {

            0%,
            100% {
                transform: rotate(-2deg);
            }

            50% {
                transform: rotate(2deg);
            }
        }

        @keyframes shimmer {

            0%,
            100% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.02, 1.01);
            }
        }

        @keyframes text-shimmer {

            0%,
            100% {
                color: #374151;
            }

            50% {
                color: #4b5563;
            }
        }

        @keyframes pulse {

            0%,
            100% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.05);
            }
        }

        .animate-spin-slow {
            animation: spin 20s linear infinite;
        }

        .animate-sway-1 {
            animation: sway 7s ease-in-out infinite;
        }

        .animate-sway-2 {
            animation: sway 10s ease-in-out infinite 1s;
        }

        .animate-shimmer {
            animation: shimmer 10s ease-in-out infinite;
        }

        .animate-text-shimmer {
            animation: text-shimmer 10s ease-in-out infinite;
        }

        .animate-pulse {
            animation: pulse 3s ease-in-out infinite;
        }
    </style>
</head>

<body class="flex items-center justify-center min-h-screen">

    <div class="relative w-full h-screen flex flex-col items-center justify-center text-center p-6">

        <div class="absolute top-10 left-10 w-24 h-24 md:w-32 md:h-32 z-10 animate-spin-slow">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-full h-full" viewBox="0 0 24 24">
                <defs>
                    <radialGradient id="sunGradient">
                        <stop offset="0%" stop-color="#FFD580" />
                        <stop offset="100%" stop-color="#F97316" />
                    </radialGradient>
                </defs>
                <circle cx="12" cy="12" r="5" fill="url(#sunGradient)" />
                <g fill="url(#sunGradient)">
                    <rect x="11" y="1" width="2" height="4" rx="1" />
                    <rect x="11" y="1" width="2" height="4" rx="1" transform="rotate(45 12 12)" />
                    <rect x="11" y="1" width="2" height="4" rx="1" transform="rotate(90 12 12)" />
                    <rect x="11" y="1" width="2" height="4" rx="1" transform="rotate(135 12 12)" />
                    <rect x="11" y="1" width="2" height="4" rx="1" transform="rotate(180 12 12)" />
                    <rect x="11" y="1" width="2" height="4" rx="1" transform="rotate(225 12 12)" />
                    <rect x="11" y="1" width="2" height="4" rx="1" transform="rotate(270 12 12)" />
                    <rect x="11" y="1" width="2" height="4" rx="1" transform="rotate(315 12 12)" />
                </g>
            </svg>
        </div>

        <div class="absolute inset-0 z-40 flex items-center justify-center pointer-events-none">
            <div class="relative w-full max-w-4xl -translate-y-9 md:-translate-y-12">
                <svg class="w-full h-auto overflow-visible" viewBox="0 0 800 300">

                    <path id="flightPath"
                        d="M 2 150 C 200 100, 350 100, 350 150 A 55 80 0 1 0 350 149.9 C 350 200, 600 200, 798 150"
                        stroke="rgba(0, 0, 0, 0.3)" stroke-width="2" stroke-linecap="round" stroke-dasharray="15 10"
                        fill="none" />

                    <path id="tracePath"
                        d="M 2 150 C 200 100, 350 100, 350 150 A 55 80 0 1 0 350 149.9 C 350 200, 600 200, 798 150"
                        stroke="#EAB308" stroke-width="2.5" fill="none">
                        <animate attributeName="stroke-dashoffset" to="0" dur="8s" repeatCount="indefinite"
                            calcMode="linear" />
                    </path>

                    <g>
                        <path d="M0 0 L-25 10 L-20 0 L-25 -10 Z" fill="#FBBF24" stroke="#EAB308" stroke-width="1.5"
                            stroke-linejoin="round">
                            <animateMotion dur="8s" repeatCount="indefinite" rotate="auto" calcMode="linear">
                                <mpath href="#flightPath" />
                            </animateMotion>
                        </path>
                    </g>
                </svg>
            </div>
        </div>

        <div class="relative z-30 flex flex-col items-center">
            <h1
                class="text-[9rem] md:text-[12rem] lg:text-[16rem] font-extrabold text-gray-700 leading-none tracking-tighter animate-text-shimmer">@yield('code', 'Error')</h1>
            <p class="text-lg md:text-xl text-gray-600/80 -mt-4 md:-mt-6 px-4">
                @yield('message', 'Terjadi kesalahan yang tidak diketahui.')
            </p>
            <div class="mt-10">
                <a href="/"
                    class="inline-block bg-[#22C58E] hover:bg-[#1fae7e] text-white font-bold py-3 px-10 rounded-full text-lg transition-all duration-300 shadow-lg animate-pulse">
                    KEMBALI
                </a>
            </div>
        </div>

        <div class="absolute bottom-0 left-0 w-full h-1/3 z-20">
            <div class="absolute bottom-0 left-0 w-full h-full animate-shimmer">
                <svg class="w-full h-full" viewBox="0 0 1440 180" preserveAspectRatio="none" fill="none"
                    xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M1440 121.782L1318.81 134.951L1126.83 118.061L1010.51 104.892L897.912 143.587L785.311 110.612L683.929 104.892L560.158 150.936L447.557 121.782L331.236 125.503L211.194 91.0102L106.091 131.23L0 121.782V180H1440V121.782Z"
                        fill="#F9D462" />
                </svg>
            </div>
            <div class="absolute bottom-10 left-[5%] md:left-[15%] w-[130px] md:w-[180px] origin-bottom animate-sway-1">
                <img src="{{ asset('images/cactus-with-flowers-png.webp') }}" alt="Ilustrasi Kaktus">
            </div>
            <div
                class="absolute bottom-8 right-[5%] md:right-[15%] w-[110px] md:w-[140px] origin-bottom animate-sway-2">
                <img src="{{ asset('images/cactus-with-flowers-png.webp') }}" alt="Ilustrasi Kaktus">
            </div>
        </div>

    </div>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const tracePath = document.getElementById("tracePath");
            const animation = tracePath.querySelector("animate");

            const pathLength = tracePath.getTotalLength();

            tracePath.setAttribute("stroke-dasharray", pathLength);
            tracePath.setAttribute("stroke-dashoffset", pathLength);

            animation.setAttribute("from", pathLength);
        });
    </script>

</body>

</html>