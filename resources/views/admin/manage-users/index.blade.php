@extends('layouts.admin')

@section('title', 'Staff Management')

@section('content')
    <div class="space-y-8 animate-in fade-in duration-500">

        <!-- HEADER -->
        <div
            class="flex flex-col md:flex-row md:items-center justify-between gap-6 border-b border-slate-200 dark:border-white/5 pb-8">
            <div class="space-y-1">
                <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Manajemen Staff</h1>
                <p class="text-xs text-slate-500 dark:text-gray-500 font-medium uppercase tracking-wider">Otoritas & Akses
                    Pengguna Sistem</p>
            </div>

            <a href="{{ route('admin.manage-users.create') }}"
                class="btn-primary text-black text-[10px] font-black uppercase tracking-widest py-2.5 px-6 rounded-md transition-all flex items-center gap-2 shadow-sm"
                style="background-color: var(--primary-color);">
                <i class="ri-user-add-line text-sm"></i>
                Tambah Staff
            </a>
        </div>

        <!-- TABLE -->
        <div class="w-full">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr>
                        <th
                            class="px-4 py-4 text-[10px] font-black text-slate-400 dark:text-gray-500 uppercase tracking-widest border-b border-slate-200 dark:border-white/5">
                            Identitas
                        </th>
                        <th
                            class="px-4 py-4 text-[10px] font-black text-slate-400 dark:text-gray-500 uppercase tracking-widest border-b border-slate-200 dark:border-white/5">
                            Role
                        </th>
                        <th
                            class="px-4 py-4 text-[10px] font-black text-slate-400 dark:text-gray-500 uppercase tracking-widest border-b border-slate-200 dark:border-white/5">
                            Shift
                        </th>
                        <th
                            class="px-4 py-4 text-[10px] font-black text-slate-400 dark:text-gray-500 uppercase tracking-widest border-b border-slate-200 dark:border-white/5 text-right">
                            Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-white/5">
                    @foreach($users as $user)
                        <tr class="group hover:bg-slate-50 dark:hover:bg-white/[0.02] transition-colors">
                            <td class="px-4 py-4">
                                <div class="flex items-center gap-3">
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=111&color={{ str_replace('#', '', auth()->user()->primary_color) }}&bold=true"
                                        class="w-8 h-8 rounded-md border border-slate-200 dark:border-white/10"
                                        alt="{{ $user->name }}">
                                    <div>
                                        <div class="text-sm font-bold text-slate-900 dark:text-white tracking-tight">
                                            {{ $user->name }}
                                        </div>
                                        <div class="text-[11px] text-slate-500 dark:text-gray-500 font-medium">
                                            {{ $user->email }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-4">
                                @php
                                    $role = $user->getRoleNames()->first() ?? 'no-role';
                                    $roleColors = [
                                        'super_admin' => 'text-red-500 bg-red-500/5 border-red-500/10',
                                        'admin' => 'text-black bg-opacity-100 border-opacity-100',
                                        'kitchen' => 'text-blue-500 bg-blue-500/5 border-blue-500/10',
                                        'no-role' => 'text-gray-500 bg-gray-500/5 border-gray-500/10',
                                    ];
                                @endphp
                                <span
                                    :class="'admin' === '{{ $role }}' ? 'inline-block px-2 py-0.5 border rounded text-[9px] font-black uppercase tracking-widest' : ''"
                                    :style="'admin' === '{{ $role }}' ? { color: 'black', backgroundColor: 'var(--primary-color)', borderColor: 'var(--primary-color)' } : {}"
                                    class="inline-block px-2 py-0.5 border rounded text-[9px] font-black uppercase tracking-widest {{ $roleColors[$role] ?? $roleColors['no-role'] }}">
                                    {{ str_replace('_', ' ', $role) }}
                                </span>
                            </td>
                            <td class="px-4 py-4">
                                @if($user->shift)
                                    @php
                                        $shiftName = $user->shift->name;
                                        $isShift1 = str_contains(strtolower($shiftName), 'shift 1') || str_contains(strtolower($shiftName), 'shift1');
                                        $shiftBg = $isShift1 ? 'bg-blue-500/10 border-blue-500/20' : 'bg-purple-500/10 border-purple-500/20';
                                        $shiftTextColor = $isShift1 ? 'text-blue-600 dark:text-blue-400' : 'text-purple-600 dark:text-purple-400';
                                    @endphp
                                    <div class="inline-block px-3 py-1.5 rounded border {{ $shiftBg }}">
                                        <div class="text-[11px] font-bold {{ $shiftTextColor }} uppercase tracking-tight">
                                            {{ $user->shift->name }}
                                        </div>
                                        <div class="text-[9px] {{ $isShift1 ? 'text-blue-500 dark:text-blue-300' : 'text-purple-500 dark:text-purple-300' }}">
                                            {{ $user->shift->start_time->format('H:i') }} - {{ $user->shift->end_time->format('H:i') }}
                                        </div>
                                    </div>
                                @else
                                    <span
                                        class="text-[10px] text-slate-400 dark:text-gray-600 uppercase font-bold tracking-widest">None</span>
                                @endif
                            </td>
                            <td class="px-4 py-4 text-right">
                                <div class="flex justify-end gap-1">
                                    <a href="{{ route('admin.manage-users.edit', $user) }}"
                                        class="p-2 text-slate-400 transition-colors"
                                        @mouseenter="$el.style.color = 'var(--primary-color)'"
                                        @mouseleave="$el.style.color = ''" title="Edit Staff">
                                        <i class="ri-edit-line text-lg"></i>
                                    </a>

                                    <!-- SweetAlert Delete Trigger -->
                                    <form action="{{ route('admin.manage-users.destroy', $user) }}" method="POST"
                                        class="inline delete-form">
                                        @csrf @method('DELETE')
                                        <button type="button" onclick="confirmDelete(this)"
                                            class="p-2 text-slate-400 hover:text-red-500 transition-colors" title="Hapus Staff">
                                            <i class="ri-delete-bin-line text-lg"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- PAGINATION -->
        <div class="pt-6 border-t border-slate-200 dark:border-white/5 text-xs">
            {{ $users->links() }}
        </div>
    </div>

    {{-- SweetAlert2 Custom Styling & Script --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function confirmDelete(button) {
            const form = button.closest('.delete-form');

            Swal.fire({
                title: 'KONFIRMASI HAPUS',
                text: "Akses staff ini akan dicabut secara permanen dari sistem.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: getComputedStyle(document.documentElement).getPropertyValue('--primary-color').trim(),
                cancelButtonColor: '#1a1a1a',
                confirmButtonText: 'YA, HAPUS AKSES',
                cancelButtonText: 'BATAL',
                background: '#0a0a0a', // Dark Mode Bg
                color: '#ffffff',
                borderRadius: '8px', // Precision Radius
                customClass: {
                    title: 'text-sm font-black tracking-widest',
                    htmlContainer: 'text-xs text-gray-400 font-medium',
                    confirmButton: 'text-[10px] font-black uppercase tracking-widest px-6 py-2.5 rounded-md',
                    cancelButton: 'text-[10px] font-black uppercase tracking-widest px-6 py-2.5 rounded-md'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        }

        // Success Alert after redirect
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'BERHASIL',
                text: "{{ session('success') }}",
                background: '#0a0a0a',
                color: '#ffffff',
                showConfirmButton: false,
                timer: 2000,
                borderRadius: '8px'
            });
        @endif
    </script>

    <style>
        /* Styling tambahan untuk menyesuaikan SweetAlert dengan Manifesto */
        .swal2-popup {
            border: 1px solid rgba(255, 255, 255, 0.05);
            font-family: 'Plus Jakarta Sans', sans-serif !important;
        }
    </style>
@endsection