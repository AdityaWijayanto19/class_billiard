<!DOCTYPE html>
<html lang="id" class="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Login | Class Billiard</title>

    <!-- Typography: Plus Jakarta Sans -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        [x-cloak] {
            display: none !important;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #050505;
            background-image: radial-gradient(circle at 50% -20%, #1a1a1a 0%, #050505 80%);
        }

        /* Standard Precision Radius & Smooth Transitions */
        .input-field {
            @apply bg-white/[0.02] border border-white/10 rounded-md py-3 px-4 text-sm text-white placeholder:text-slate-700 focus:outline-none focus:border-gold-400/50 focus:ring-1 focus:ring-gold-400/20 transition-all duration-300;
        }

        @keyframes shake {

            0%,
            100% {
                transform: translateX(0);
            }

            25% {
                transform: translateX(-4px);
            }

            75% {
                transform: translateX(4px);
            }
        }

        .animate-shake {
            animation: shake 0.4s cubic-bezier(.36, .07, .19, .97) both;
        }
    </style>
</head>

<body class="text-slate-200 min-h-screen flex items-center justify-center p-6 antialiased">

    <div class="w-full max-w-[400px]">
        <!-- HEADER: Typography Hierarchy Standard -->
        <div class="text-center mb-10 space-y-3">
            <div
                class="inline-flex items-center justify-center w-14 h-14 rounded-md bg-white/[0.03] border border-white/10 mb-2 group transition-all duration-500 hover:border-gold-400/30">
                <svg class="w-7 h-7 text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
            </div>
            <div class="space-y-1">
                <h1 class="text-2xl font-bold tracking-tight text-white uppercase">Class Billiard</h1>
                <p class="text-[10px] font-black tracking-[0.2em] text-slate-500 uppercase">Staff Access Portal • Secure
                    Environment</p>
            </div>
        </div>

        <!-- ALERT SYSTEM: Minimalist Composition -->
        @if ($errors->any())
            <div id="error-alert" class="mb-6 animate-shake">
                <div class="bg-red-500/5 border border-red-500/20 px-4 py-3 rounded-md flex items-start gap-3">
                    <svg class="w-4 h-4 text-red-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <div class="space-y-1">
                        <p class="text-[10px] font-black uppercase tracking-widest text-red-500">Authentication Failed</p>
                        <p class="text-xs text-red-400/80 leading-relaxed">Kredensial tidak sesuai dengan database kami.</p>
                    </div>
                </div>
            </div>
        @endif

        @if (session('status'))
            <div id="status-alert" class="mb-6">
                <div class="bg-gold-400/5 border border-gold-400/20 px-4 py-3 rounded-md flex items-center gap-3">
                    <svg class="w-4 h-4 text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <p class="text-[10px] font-bold text-gold-400 uppercase tracking-wider">{{ session('status') }}</p>
                </div>
            </div>
        @endif

        <!-- MAIN LOGIN CARD -->
        <div class="relative group">
            <!-- Subtle Glow Decor -->
            <div
                class="absolute -inset-0.5 bg-gold-400/10 rounded-lg blur opacity-0 group-hover:opacity-100 transition duration-1000">
            </div>

            <div class="relative bg-[#0A0A0A] border border-white/5 rounded-lg p-8 shadow-2xl">
                <!-- Top Accent Line -->
                <div
                    class="absolute top-0 left-0 w-full h-[1px] bg-gradient-to-r from-transparent via-gold-400/50 to-transparent">
                </div>

                <form method="POST" action="{{ route('login') }}" class="space-y-6"
                    x-data="{ showPass: false, loading: false }" @submit="loading = true">
                    @csrf

                    <!-- Email Field -->
                    <div class="space-y-2">
                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] ml-1">
                            Work Email
                        </label>
                        <input type="email" name="email" value="{{ old('email') }}" required autofocus
                            class="w-full bg-slate-50 dark:bg-white/[0.02] border border-slate-200 dark:border-white/10 rounded-md px-4 py-2.5 text-sm text-slate-900 dark:text-white focus:border-gold-400 focus:ring-0 transition-all outline-none"
                            placeholder="name@classbilliard.com">
                    </div>

                    <!-- Password Field -->
                    <div class="space-y-2">
                        <div class="flex justify-between items-center ml-1">
                            <label class="block text-[10px] font-black text-slate-500 uppercase tracking-[0.2em]">
                                Secure Password
                            </label>
                        </div>
                        <div class="relative">
                            <input :type="showPass ? 'text' : 'password'" name="password" required
                                class="w-full bg-slate-50 dark:bg-white/[0.02] border border-slate-200 dark:border-white/10 rounded-md px-4 py-2.5 text-sm text-slate-900 dark:text-white focus:border-gold-400 focus:ring-0 transition-all outline-none input-field pr-12"
                                placeholder="••••••••">

                            <button type="button" @click="showPass = !showPass"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-600 hover:text-gold-400 transition-colors focus:outline-none">
                                <svg x-show="!showPass" class="w-4 h-4" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <svg x-show="showPass" x-cloak class="w-4 h-4" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Submit Button: Interactive Feedback Standard -->
                    <button type="submit" :disabled="loading"
                        class="w-full bg-gold-400 hover:bg-yellow-600 disabled:opacity-50 disabled:cursor-not-allowed text-black text-[10px] font-black uppercase tracking-[0.2em] py-4 rounded-md transition-all duration-300 shadow-lg shadow-yellow-950/20 flex items-center justify-center gap-2 group overflow-hidden relative">

                        <span x-show="!loading" class="flex items-center gap-2">
                            Authorize Access
                            <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform duration-300" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M13 7l5 5m0 0l-5 5m5-5H6" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round"></path>
                            </svg>
                        </span>

                        <span x-show="loading" x-cloak class="flex items-center gap-2">
                            <svg class="animate-spin h-4 w-4 text-black" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            Authenticating...
                        </span>
                    </button>
                </form>
            </div>
        </div>

        <!-- FOOTER: Minimalist Composition -->
        <div class="mt-10 space-y-4 text-center">
            <p class="text-slate-600 text-[10px] font-medium tracking-[0.1em] uppercase">
                &copy; 2025 CLASS BILLIARD • Restricted Access Portal
            </p>
        </div>
    </div>

    <!-- Scripts: Alpine.js & UX Enhancements -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Auto-dismiss alerts after 5 seconds
            const dismissAlert = (id) => {
                const el = document.getElementById(id);
                if (el) {
                    setTimeout(() => {
                        el.style.transition = 'all 0.6s cubic-bezier(0.4, 0, 0.2, 1)';
                        el.style.opacity = '0';
                        el.style.transform = 'translateY(-10px)';
                        setTimeout(() => el.remove(), 600);
                    }, 5000);
                }
            };
            dismissAlert('error-alert');
            dismissAlert('status-alert');
        });
    </script>
</body>

</html>