@extends('layouts.admin')

@section('content')
<div class="min-h-screen bg-white dark:bg-[#050505] p-6 lg:p-10">
    
    <!-- HEADER SECTION -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 border-b border-slate-200 dark:border-white/5 pb-8 mb-10">
        <div class="space-y-1">
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white uppercase">Account Settings</h1>
            <p class="text-xs text-slate-500 dark:text-gray-500 font-medium">Manage your administrative credentials and security preferences.</p>
        </div>
        
        @if(session('success'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" 
                 class="flex items-center gap-3 bg-emerald-500/10 border border-emerald-500/20 px-4 py-2 rounded-md">
                <i class="ri-checkbox-circle-fill text-emerald-500"></i>
                <span class="text-[11px] font-bold text-emerald-500 uppercase tracking-wider">{{ session('success') }}</span>
            </div>
        @endif
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">
        
        <!-- LEFT COLUMN: PROFILE INFO -->
        <div class="lg:col-span-4 space-y-2">
            <h2 class="text-sm font-black uppercase tracking-[0.2em]" style="color: var(--primary-color);">General Information</h2>
            <p class="text-xs text-slate-500 dark:text-gray-500 leading-relaxed">
                This information will be used for system logging and administrative identification.
            </p>
        </div>

        <!-- RIGHT COLUMN: PROFILE FORM -->
        <div class="lg:col-span-8">
            <div class="bg-white dark:bg-[#0A0A0A] border border-slate-200 dark:border-white/5 rounded-lg p-8 transition-all duration-300">
                <form action="{{ route('admin.profile.update') }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Full Name -->
                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-gray-500">Full Name</label>
                            <input type="text" name="name" value="{{ old('name', $user->name) }}" 
                                   class="w-full bg-slate-50 dark:bg-white/[0.02] border border-slate-200 dark:border-white/10 rounded-md px-4 py-2.5 text-sm text-slate-900 dark:text-white transition-all outline-none"
                                   placeholder="Enter your full name">
                            @error('name') <p class="text-[10px] text-red-500 font-bold uppercase tracking-tight">{{ $message }}</p> @enderror
                        </div>

                        <!-- Email Address -->
                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-gray-500">Email Address</label>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}" 
                                   class="w-full bg-slate-50 dark:bg-white/[0.02] border border-slate-200 dark:border-white/10 rounded-md px-4 py-2.5 text-sm text-slate-900 dark:text-white transition-all outline-none"
                                   placeholder="email@example.com">
                            @error('email') <p class="text-[10px] text-red-500 font-bold uppercase tracking-tight">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="flex justify-end pt-4">
                        <button type="submit" class="text-black text-[10px] font-black uppercase tracking-widest py-3 px-8 rounded-md transition-all shadow-sm active:scale-95 btn-primary">
                            Update Personal Info
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- DIVIDER -->
        <div class="lg:col-span-12 border-t border-slate-100 dark:border-white/5 my-4"></div>

        <!-- LEFT COLUMN: SECURITY -->
        <div class="lg:col-span-4 space-y-2">
            <h2 class="text-sm font-black uppercase tracking-[0.2em]" style="color: var(--primary-color);">Security Access</h2>
            <p class="text-xs text-slate-500 dark:text-gray-500 leading-relaxed">
                Ensure your account is using a long, random password to stay secure.
            </p>
        </div>

        <!-- RIGHT COLUMN: PASSWORD FORM -->
        <div class="lg:col-span-8">
            <div class="bg-white dark:bg-[#0A0A0A] border border-slate-200 dark:border-white/5 rounded-lg p-8 transition-all duration-300">
                <form action="{{ route('admin.profile.password') }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div class="space-y-6">
                        <!-- Current Password -->
                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-gray-500">Current Password</label>
                            <input type="password" name="current_password" 
                                   class="w-full bg-slate-50 dark:bg-white/[0.02] border border-slate-200 dark:border-white/10 rounded-md px-4 py-2.5 text-sm text-slate-900 dark:text-white transition-all outline-none"
                                   placeholder="••••••••">
                            @error('current_password') <p class="text-[10px] text-red-500 font-bold uppercase tracking-tight">{{ $message }}</p> @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- New Password -->
                            <div class="space-y-2">
                                <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-gray-500">New Password</label>
                                <input type="password" name="password" 
                                       class="w-full bg-slate-50 dark:bg-white/[0.02] border border-slate-200 dark:border-white/10 rounded-md px-4 py-2.5 text-sm text-slate-900 dark:text-white transition-all outline-none"
                                       placeholder="••••••••">
                                @error('password') <p class="text-[10px] text-red-500 font-bold uppercase tracking-tight">{{ $message }}</p> @enderror
                            </div>

                            <!-- Confirm Password -->
                            <div class="space-y-2">
                                <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-gray-500">Confirm Password</label>
                                <input type="password" name="password_confirmation" 
                                       class="w-full bg-slate-50 dark:bg-white/[0.02] border border-slate-200 dark:border-white/10 rounded-md px-4 py-2.5 text-sm text-slate-900 dark:text-white transition-all outline-none"
                                       placeholder="••••••••">
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end pt-4">
                        <button type="submit" class="bg-slate-900 dark:bg-white text-white dark:text-black text-[10px] font-black uppercase tracking-widest py-3 px-8 rounded-md hover:bg-slate-800 dark:hover:bg-gray-200 transition-all shadow-sm active:scale-95">
                            Update Password
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- DIVIDER -->
        <div class="lg:col-span-12 border-t border-slate-100 dark:border-white/5 my-4"></div>

        <!-- LEFT COLUMN: COLOR PREFERENCE -->
        <div class="lg:col-span-4 space-y-2">
            <h2 class="text-sm font-black uppercase tracking-[0.2em]" style="color: var(--primary-color);">Color Preference</h2>
            <p class="text-xs text-slate-500 dark:text-gray-500 leading-relaxed">
                Customize your dashboard theme color to match your preference.
            </p>
        </div>

        <!-- RIGHT COLUMN: COLOR PREFERENCE FORM -->
        <div class="lg:col-span-8">
            <div class="bg-white dark:bg-[#0A0A0A] border border-slate-200 dark:border-white/5 rounded-lg p-8 transition-all duration-300">
                <form action="{{ route('admin.profile.color') }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <!-- Color Option 1: Gold -->
                        <label class="group relative cursor-pointer">
                            <input type="radio" name="primary_color" value="#fbbf24" 
                                {{ (auth()->user()->primary_color ?? '#fa9a08') === '#fbbf24' ? 'checked' : '' }}
                                class="sr-only peer">
                            <div class="p-6 border-2 border-slate-200 dark:border-white/10 rounded-lg bg-slate-50 dark:bg-white/[0.02] transition-all duration-300 peer-checked:border-[#fbbf24] peer-checked:bg-yellow-50 dark:peer-checked:bg-yellow-950/20 hover:border-yellow-300 dark:hover:border-yellow-700">
                                <div class="flex flex-col items-center gap-4">
                                    <div class="w-full h-16 rounded-md bg-gradient-to-r from-yellow-300 to-yellow-400 border border-yellow-200 dark:border-yellow-900 shadow-md"></div>
                                    <div class="text-center">
                                        <p class="text-sm font-black text-slate-900 dark:text-white uppercase tracking-tight">Gold</p>
                                        <p class="text-[10px] text-slate-500 dark:text-gray-400 uppercase tracking-widest font-medium mt-1">#fbbf24</p>
                                    </div>
                                </div>
                                <div class="absolute top-3 right-3 w-5 h-5 rounded-full border-2 border-white dark:border-slate-900 bg-[#fbbf24] flex items-center justify-center opacity-0 peer-checked:opacity-100 transition-opacity">
                                    <i class="ri-check-line text-white text-xs"></i>
                                </div>
                            </div>
                        </label>

                        <!-- Color Option 2: Orange (Current) -->
                        <label class="group relative cursor-pointer">
                            <input type="radio" name="primary_color" value="#fa9a08" 
                                {{ (auth()->user()->primary_color ?? '#fa9a08') === '#fa9a08' ? 'checked' : '' }}
                                class="sr-only peer">
                            <div class="p-6 border-2 border-slate-200 dark:border-white/10 rounded-lg bg-slate-50 dark:bg-white/[0.02] transition-all duration-300 peer-checked:border-[#fa9a08] peer-checked:bg-orange-50 dark:peer-checked:bg-orange-950/20 hover:border-orange-300 dark:hover:border-orange-700">
                                <div class="flex flex-col items-center gap-4">
                                    <div class="w-full h-16 rounded-md bg-gradient-to-r from-orange-400 to-orange-500 border border-orange-300 dark:border-orange-800 shadow-md"></div>
                                    <div class="text-center">
                                        <p class="text-sm font-black text-slate-900 dark:text-white uppercase tracking-tight">Orange</p>
                                        <p class="text-[10px] text-slate-500 dark:text-gray-400 uppercase tracking-widest font-medium mt-1">#fa9a08</p>
                                    </div>
                                </div>
                                <div class="absolute top-3 right-3 w-5 h-5 rounded-full border-2 border-white dark:border-slate-900 bg-[#fa9a08] flex items-center justify-center opacity-0 peer-checked:opacity-100 transition-opacity">
                                    <i class="ri-check-line text-white text-xs"></i>
                                </div>
                            </div>
                        </label>

                        <!-- Color Option 3: Elegant Teal -->
                        <label class="group relative cursor-pointer">
                            <input type="radio" name="primary_color" value="#2f7d7a" 
                                {{ (auth()->user()->primary_color ?? '#fa9a08') === '#2f7d7a' ? 'checked' : '' }}
                                class="sr-only peer">
                            <div class="p-6 border-2 border-slate-200 dark:border-white/10 rounded-lg bg-slate-50 dark:bg-white/[0.02] transition-all duration-300 peer-checked:border-[#2f7d7a] peer-checked:bg-teal-50 dark:peer-checked:bg-teal-950/20 hover:border-teal-300 dark:hover:border-teal-700">
                                <div class="flex flex-col items-center gap-4">
                                    <div class="w-full h-16 rounded-md bg-gradient-to-r from-teal-600 to-teal-700 border border-teal-600 dark:border-teal-900 shadow-md"></div>
                                    <div class="text-center">
                                        <p class="text-sm font-black text-slate-900 dark:text-white uppercase tracking-tight">Teal</p>
                                        <p class="text-[10px] text-slate-500 dark:text-gray-400 uppercase tracking-widest font-medium mt-1">#2f7d7a</p>
                                    </div>
                                </div>
                                <div class="absolute top-3 right-3 w-5 h-5 rounded-full border-2 border-white dark:border-slate-900 bg-[#2f7d7a] flex items-center justify-center opacity-0 peer-checked:opacity-100 transition-opacity">
                                    <i class="ri-check-line text-white text-xs"></i>
                                </div>
                            </div>
                        </label>
                    </div>

                    <div class="flex justify-end pt-4">
                        <button type="submit" class="text-black text-[10px] font-black uppercase tracking-widest py-3 px-8 rounded-md transition-all shadow-sm active:scale-95 btn-primary">
                            Save Color Preference
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');
    
    body {
        font-family: 'Plus Jakarta Sans', sans-serif;
    }

    /* Custom Focus Ring using CSS Variable */
    input:focus {
        border-color: var(--primary-color) !important;
        box-shadow: 0 0 0 1px var(--primary-color) !important;
    }
</style>
@endsection