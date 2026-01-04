@extends('layouts.admin')

@section('title', 'Register New Staff')

@section('content')
<div class="max-w-2xl mx-auto animate-in fade-in slide-in-from-bottom-4 duration-700">
    
    <!-- HEADER & NAVIGATION -->
    <div class="flex items-center gap-5 mb-10 pb-6 border-b border-slate-200 dark:border-white/5">
        <a href="{{ route('admin.manage-users.index') }}"
           class="w-9 h-9 flex items-center justify-center rounded-md border border-slate-200 dark:border-white/10 text-slate-500 dark:text-gray-400 transition-all duration-300"
           @mouseenter="$el.style.color = 'var(--primary-color)'; $el.style.borderColor = 'var(--primary-color)';"
           @mouseleave="$el.style.color = ''; $el.style.borderColor = '';">
            <i class="ri-arrow-left-s-line text-xl"></i>
        </a>
        <div class="space-y-0.5">
            <h1 class="text-xl font-bold tracking-tight text-slate-900 dark:text-white">Registrasi Staff Baru</h1>
            <p class="text-[10px] font-black text-slate-400 dark:text-gray-500 uppercase tracking-[0.2em]">User Access Control & Identity</p>
        </div>
    </div>

    @if ($errors->any())
        <!-- (Error Alert Tetap Sama - Sudah Sesuai Manifesto) -->
        <div class="mb-8 p-4 bg-red-500/5 border border-red-500/20 rounded-lg">
            <ul class="space-y-1">
                @foreach ($errors->all() as $error)
                    <li class="text-xs text-red-400/80 flex items-center gap-2">
                        <span class="w-1 h-1 bg-red-500 rounded-full"></span>
                        {{ $error }}
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.manage-users.store') }}" method="POST" class="space-y-8">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- NAMA LENGKAP -->
            <div class="space-y-2">
                <label class="text-[10px] font-black text-slate-500 dark:text-gray-500 uppercase tracking-widest ml-1">Nama Lengkap</label>
                <input type="text" name="name" value="{{ old('name') }}" placeholder="e.g. Alexander Graham" required
                    class="w-full bg-slate-50 dark:bg-white/[0.02] border border-slate-200 dark:border-white/10 rounded-md py-3 px-4 text-sm text-slate-900 dark:text-white focus:ring-0 outline-none transition-all placeholder:text-slate-400 dark:placeholder:text-gray-600"
                    @focus="$el.style.borderColor = 'var(--primary-color)'"
                    @blur="$el.style.borderColor = ''">
            </div>

            <!-- EMAIL -->
            <div class="space-y-2">
                <label class="text-[10px] font-black text-slate-500 dark:text-gray-500 uppercase tracking-widest ml-1">Email Kredensial</label>
                <input type="email" name="email" value="{{ old('email') }}" placeholder="staff@enterprise.com" required
                    class="w-full bg-slate-50 dark:bg-white/[0.02] border border-slate-200 dark:border-white/10 rounded-md py-3 px-4 text-sm text-slate-900 dark:text-white focus:ring-0 outline-none transition-all placeholder:text-slate-400 dark:placeholder:text-gray-600"
                    @focus="$el.style.borderColor = 'var(--primary-color)'"
                    @blur="$el.style.borderColor = ''">
            </div>
        </div>

        <!-- DROPDOWN SECTION (REBUILT WITH ALPINE.JS) -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4 border-t border-slate-200 dark:border-white/5">
            
            <!-- CUSTOM DROPDOWN: ROLE -->
            <div class="space-y-2" x-data="{ 
                open: false, 
                selected: '{{ old('role', 'kitchen') }}',
                options: {
                    'kitchen': 'Kitchen Staff',
                    'admin': 'Administrator',
                    'super_admin': 'Super Admin'
                }
            }">
                <label class="text-[10px] font-black text-slate-500 dark:text-gray-500 uppercase tracking-widest ml-1">Hak Akses (Role)</label>
                <div class="relative">
                    <input type="hidden" name="role" :value="selected">
                    <button type="button" @click="open = !open" @click.away="open = false"
                        class="w-full flex items-center justify-between bg-slate-50 dark:bg-white/[0.02] border border-slate-200 dark:border-white/10 rounded-md py-3 px-4 text-sm text-left text-slate-900 dark:text-white transition-all"
                        @mouseenter="$el.style.borderColor = 'var(--primary-color)'"
                        @mouseleave="$el.style.borderColor = ''">
                        <span x-text="options[selected]"></span>
                        <i class="ri-arrow-down-s-line text-slate-400 transition-transform duration-300" :class="open ? 'rotate-180' : ''"></i>
                    </button>
                    
                    <div x-show="open" 
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         class="absolute z-50 w-full mt-2 bg-[#0A0A0A] border border-white/10 rounded-md shadow-xl overflow-hidden backdrop-blur-md">
                        <template x-for="(label, value) in options">
                            <div @click="selected = value; open = false" 
                                 class="px-4 py-3 text-sm cursor-pointer transition-colors font-medium"
                                 :class="selected === value ? 'text-black' : 'text-gray-400 hover:text-gray-200'"
                                 :style="{ backgroundColor: selected === value ? 'var(--primary-color)' : '' }">
                                <span x-text="label"></span>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            <!-- CUSTOM DROPDOWN: SHIFT -->
            <div class="space-y-2" x-data="{ 
                open: false, 
                selected: '{{ old('shift_id', '') }}',
                shiftOptions: {
                    @foreach($shifts as $shift)
                        '{{ $shift->id }}': '{{ $shift->name }} ({{ $shift->start_time->format('H:i') }} - {{ $shift->end_time->format('H:i') }})',
                    @endforeach
                },
                get selectedLabel() {
                    return this.selected === '' ? 'None' : this.shiftOptions[this.selected] || 'Pilih Shift';
                }
            }">
                <label class="text-[10px] font-black text-slate-500 dark:text-gray-500 uppercase tracking-widest ml-1">Shift Operasional</label>
                <div class="relative">
                    <input type="hidden" name="shift_id" :value="selected">
                    <button type="button" @click="open = !open" @click.away="open = false"
                        class="w-full flex items-center justify-between bg-slate-50 dark:bg-white/[0.02] border border-slate-200 dark:border-white/10 rounded-md py-3 px-4 text-sm text-left text-slate-900 dark:text-white transition-all"
                        @mouseenter="$el.style.borderColor = 'var(--primary-color)'"
                        @mouseleave="$el.style.borderColor = ''">
                        <span x-text="selectedLabel" :class="selected === '' ? 'text-slate-400' : ''"></span>
                        <i class="ri-arrow-down-s-line text-slate-400 transition-transform duration-300" :class="open ? 'rotate-180' : ''"></i>
                    </button>
                    
                    <div x-show="open" 
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         class="absolute z-50 w-full mt-2 bg-[#0A0A0A] border border-white/10 rounded-md shadow-xl max-h-60 overflow-y-auto backdrop-blur-md">
                        
                        <div @click="selected = ''; open = false" 
                             class="px-4 py-3 text-sm text-gray-400 hover:text-gray-200 cursor-pointer transition-colors italic"
                             :class="selected === '' ? 'text-black' : ''"
                             :style="{ backgroundColor: selected === '' ? 'var(--primary-color)' : '' }">
                            None
                        </div>

                        @foreach($shifts as $shift)
                            <div @click="selected = '{{ $shift->id }}'; open = false" 
                                 class="px-4 py-3 text-sm cursor-pointer transition-colors font-medium"
                                 :class="selected === '{{ $shift->id }}' ? 'text-black' : 'text-gray-400 hover:text-gray-200'"
                                 :style="{ backgroundColor: selected === '{{ $shift->id }}' ? 'var(--primary-color)' : '' }">
                                <div class="flex justify-between items-center">
                                    <span>{{ $shift->name }}</span>
                                    <span class="text-[10px] opacity-70">{{ $shift->start_time->format('H:i') }} - {{ $shift->end_time->format('H:i') }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- SECTION: SECURITY -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4 border-t border-slate-200 dark:border-white/5">
            <div class="space-y-2">
                <label class="text-[10px] font-black text-slate-500 dark:text-gray-500 uppercase tracking-widest ml-1">Password Awal</label>
                <input type="password" name="password" required placeholder="••••••••"
                    class="w-full bg-slate-50 dark:bg-white/[0.02] border border-slate-200 dark:border-white/10 rounded-md py-3 px-4 text-sm text-slate-900 dark:text-white outline-none transition-all"
                    @focus="$el.style.borderColor = 'var(--primary-color)'"
                    @blur="$el.style.borderColor = ''">
            </div>

            <div class="space-y-2">
                <label class="text-[10px] font-black text-slate-500 dark:text-gray-500 uppercase tracking-widest ml-1">Konfirmasi Password</label>
                <input type="password" name="password_confirmation" required placeholder="••••••••"
                    class="w-full bg-slate-50 dark:bg-white/[0.02] border border-slate-200 dark:border-white/10 rounded-md py-3 px-4 text-sm text-slate-900 dark:text-white outline-none transition-all"
                    @focus="$el.style.borderColor = 'var(--primary-color)'"
                    @blur="$el.style.borderColor = ''">
            </div>
        </div>

        <div class="pt-10 flex flex-col items-center">
            <button type="submit" class="w-full btn-primary text-black text-[11px] font-black uppercase tracking-[0.2em] py-4 rounded-md transition-all shadow-sm active:scale-[0.98]"
                style="background-color: var(--primary-color);">
                Daftarkan Staff Baru
            </button>
            <div class="mt-6 flex items-center gap-2 text-[10px] text-slate-400 dark:text-gray-600 font-bold uppercase tracking-widest">
                <i class="ri-shield-check-line text-sm" style="color: var(--primary-color);"></i>
                Secure Access Protocol Active
            </div>
        </div>
    </form>
</div>

<style>
    /* Custom Scrollbar for Dropdown */
    ::-webkit-scrollbar { width: 4px; }
    ::-webkit-scrollbar-track { background: transparent; }
    ::-webkit-scrollbar-thumb { background: rgba(250, 154, 8, 0.2); border-radius: 10px; }
    ::-webkit-scrollbar-thumb:hover { background: rgba(250, 154, 8, 0.5); }
</style>
@endsection