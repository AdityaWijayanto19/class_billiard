@extends('layouts.admin')

@section('title', 'Kelola Permissions')

@section('content')
<!-- HEADER SECTION -->
<div class="flex flex-col md:flex-row md:items-center justify-between gap-6 border-b border-slate-200 dark:border-white/5 pb-8 mb-8">
    <div class="space-y-1">
        <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white flex items-center gap-3">
            <div class="w-10 h-10 rounded-md flex items-center justify-center" style="background-color: rgba(var(--primary-color-rgb), 0.1);">
                <i class="ri-shield-check-line text-lg" style="color: var(--primary-color);"></i>
            </div>
            Kelola Permissions
        </h1>
        <p class="text-xs text-slate-500 dark:text-gray-500 font-medium">Berikan hak akses lebih kepada user admin dengan mengelola permissions mereka. User kitchen memiliki dashboard terpisah dan tidak dapat dikelola di sini.</p>
    </div>
    <div class="flex items-center gap-2 px-4 py-2 rounded-lg bg-slate-100 dark:bg-white/5 border border-slate-200 dark:border-white/10">
        <i class="ri-group-line" style="color: var(--primary-color);"></i>
        <span class="text-[10px] font-black uppercase tracking-widest text-slate-700 dark:text-slate-300">Total: {{ $users->total() }} Users</span>
    </div>
</div>

<!-- USERS LIST CONTAINER -->
@if($users->isEmpty())
    <div class="p-12 text-center border border-slate-200 dark:border-white/5 rounded-lg bg-slate-50 dark:bg-white/[0.02]">
        <i class="ri-user-search-line text-5xl text-slate-300 dark:text-slate-700 mb-4 block"></i>
        <p class="text-sm font-bold text-slate-600 dark:text-slate-400 mb-2">Tidak ada user admin yang ditemukan</p>
        <p class="text-[11px] text-slate-500 dark:text-slate-500">Semua user sudah memiliki permissions diatur atau tidak ada user admin lain yang tersedia</p>
    </div>
@else
    <div class="space-y-3">
        @foreach($users as $user)
            <div class="group p-6 border border-slate-200 dark:border-white/5 rounded-lg transition-all duration-300 bg-white dark:bg-white/[0.02]"
                @mouseenter="$el.style.borderColor = 'rgba(var(--primary-color-rgb), 0.5)'"
                @mouseleave="$el.style.borderColor = ''">
                <div class="flex items-center justify-between gap-6">
                    {{-- User Info Left --}}
                    <div class="flex items-center gap-4 flex-1">
                        {{-- Avatar --}}
                        <div class="w-12 h-12 rounded-md flex items-center justify-center font-bold text-lg shrink-0" style="background-color: rgba(var(--primary-color-rgb), 0.1); color: var(--primary-color); border: 1px solid rgba(var(--primary-color-rgb), 0.2);">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>

                        {{-- User Details --}}
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-bold text-slate-900 dark:text-white truncate">{{ $user->name }}</p>
                            <p class="text-[11px] text-slate-500 dark:text-slate-400 truncate">{{ $user->email }}</p>
                            <div class="flex items-center gap-2 mt-2">
                                <span class="inline-flex items-center px-2 py-1 rounded-md text-[10px] font-black uppercase tracking-widest
                                    @if($user->role === 'admin')
                                        bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300
                                    @elseif($user->role === 'kitchen')
                                        bg-orange-100 dark:bg-orange-900/30 text-orange-700 dark:text-orange-300
                                    @else
                                        bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300
                                    @endif
                                ">
                                    {{ ucfirst($user->role) }}
                                </span>

                                @php
                                    $permCount = $user->getDirectPermissions()->count();
                                @endphp
                                <span class="text-[10px] font-black uppercase tracking-widest text-slate-500 dark:text-slate-400">
                                    <i class="ri-shield-line"></i> {{ $permCount }} Permissions
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Action Button Right --}}
                    <a href="{{ route('admin.permissions.manage', $user->id) }}" 
                       class="inline-flex items-center gap-2 px-4 py-2.5 text-black text-[10px] font-black uppercase tracking-widest rounded-md transition-all duration-300 group-hover:shadow-md shrink-0" style="background-color: var(--primary-color);" @mouseenter="$el.style.opacity = '0.85'" @mouseleave="$el.style.opacity = '1'">
                        <i class="ri-edit-line"></i>
                        Kelola
                    </a>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Pagination --}}
    @if($users->hasPages())
        <div class="mt-8 flex items-center justify-center gap-2">
            {{ $users->links(data: ['view' => 'pagination::tailwind']) }}
        </div>
    @endif
@endif
@endsection
