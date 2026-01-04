@extends('layouts.admin')

@section('title', 'Manage Permissions for ' . $user->name)

@section('content')
<!-- HEADER SECTION -->
<div class="flex flex-col md:flex-row md:items-center justify-between gap-6 border-b border-slate-200 dark:border-white/5 pb-8 mb-8">
    <div class="space-y-1">
        <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white flex items-center gap-3">
            <div class="w-10 h-10 rounded-md flex items-center justify-center" style="background-color: rgba(var(--primary-color-rgb), 0.1);">
                <i class="ri-shield-check-line text-lg" style="color: var(--primary-color);"></i>
            </div>
            Manage Permissions
        </h1>
        <p class="text-xs text-slate-500 dark:text-gray-500 font-medium">Configure access permissions for <span class="font-bold">{{ $user->name }}</span></p>
    </div>
    <div class="flex items-center gap-3 px-4 py-2.5 rounded-lg bg-slate-100 dark:bg-white/5 border border-slate-200 dark:border-white/10">
        <i class="ri-shield-line text-lg" style="color: var(--primary-color);"></i>
        <span class="text-[10px] font-black uppercase tracking-widest text-slate-700 dark:text-slate-300">{{ count($userPermissions) }} Active</span>
    </div>
</div>

<!-- USER INFO CARD -->
<div class="p-6 border border-slate-200 dark:border-white/5 rounded-lg bg-white dark:bg-white/[0.02] mb-8">
    <div class="flex items-center gap-6">
        <!-- Avatar -->
        <div class="w-14 h-14 rounded-md flex items-center justify-center font-bold text-xl shrink-0" style="background-color: rgba(var(--primary-color-rgb), 0.1); color: var(--primary-color); border: 1px solid rgba(var(--primary-color-rgb), 0.2);">
            {{ strtoupper(substr($user->name, 0, 1)) }}
        </div>

        <!-- User Details -->
        <div class="flex-1">
            <p class="text-base font-bold text-slate-900 dark:text-white">{{ $user->name }}</p>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">{{ $user->email }}</p>
        </div>

        <!-- Role Badge -->
        <div class="text-right">
            <span class="inline-flex items-center px-3 py-1.5 rounded-md text-[10px] font-black uppercase tracking-widest
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
        </div>
    </div>
</div>

<!-- SUCCESS MESSAGE -->
<div id="successMessage" class="mt-8 hidden p-4 bg-emerald-500/10 border border-emerald-500/30 rounded-lg flex items-start gap-4">
    <i class="ri-checkbox-circle-fill text-emerald-500 text-xl shrink-0 mt-0.5"></i>
    <div class="flex-1">
        <p class="text-sm font-bold text-emerald-600 dark:text-emerald-400" id="successText"></p>
    </div>
    <button type="button" @click="this.parentElement.classList.add('hidden')" class="text-emerald-500 hover:text-emerald-700 shrink-0">
        <i class="ri-close-line"></i>
    </button>
</div>

<!-- ERROR MESSAGE -->
<div id="errorMessage" class="mt-8 hidden p-4 bg-red-500/10 border border-red-500/30 rounded-lg flex items-start gap-4">
    <i class="ri-alert-fill text-red-500 text-xl shrink-0 mt-0.5"></i>
    <div class="flex-1">
        <p class="text-sm font-bold text-red-600 dark:text-red-400" id="errorText"></p>
    </div>
    <button type="button" @click="this.parentElement.classList.add('hidden')" class="text-red-500 hover:text-red-700 shrink-0">
        <i class="ri-close-line"></i>
    </button>
</div>

<!-- PERMISSIONS FORM -->
<form id="permissionsForm" class="space-y-6">
    @csrf

    @forelse($groupedPermissions as $category => $permissions)
        <div class="border border-slate-200 dark:border-white/5 rounded-lg overflow-hidden bg-white dark:bg-white/[0.02]">
            <!-- Category Header -->
            <div class="px-6 py-4 border-b border-slate-200 dark:border-white/5 bg-slate-50 dark:bg-white/[0.02]">
                <h2 class="text-sm font-bold text-slate-900 dark:text-white tracking-tight flex items-center gap-3 capitalize">
                    @switch($category)
                        @case('order')
                            <i class="ri-shopping-cart-2-line text-lg" style="color: var(--primary-color);"></i>
                            @break
                        @case('payment')
                            <i class="ri-money-dollar-circle-line text-lg" style="color: var(--primary-color);"></i>
                            @break
                        @case('kitchen')
                            <i class="ri-restaurant-2-line text-lg" style="color: var(--primary-color);"></i>
                            @break
                        @case('menu')
                            <i class="ri-file-list-line text-lg" style="color: var(--primary-color);"></i>
                            @break
                        @case('category')
                            <i class="ri-folder-line text-lg" style="color: var(--primary-color);"></i>
                            @break
                        @case('table')
                            <i class="ri-layout-grid-line text-lg" style="color: var(--primary-color);"></i>
                            @break
                        @case('report')
                            <i class="ri-bar-chart-line text-lg" style="color: var(--primary-color);"></i>
                            @break
                        @case('user')
                            <i class="ri-user-line text-lg" style="color: var(--primary-color);"></i>
                            @break
                        @case('role')
                            <i class="ri-shield-line text-lg" style="color: var(--primary-color);"></i>
                            @break
                        @default
                            <i class="ri-checkbox-circle-line text-lg" style="color: var(--primary-color);"></i>
                    @endswitch
                    {{ ucfirst($category) }} 
                    <span class="text-[10px] font-black ml-auto text-slate-500 dark:text-slate-400 uppercase tracking-widest">{{ count($permissions) }} items</span>
                </h2>
            </div>

            <!-- Permissions List -->
            <div class="divide-y divide-slate-200 dark:divide-white/5">
                @foreach($permissions as $permission)
                    @php
                        $isChecked = in_array($permission->name, $userPermissions);
                        $parts = explode('.', $permission->name);
                        $action = end($parts);
                        $actionText = match($action) {
                            'view' => 'Melihat',
                            'create' => 'Membuat baru',
                            'update' => 'Mengubah',
                            'delete' => 'Menghapus',
                            'manage' => 'Mengelola',
                            'assign' => 'Menugaskan',
                            'confirm' => 'Mengkonfirmasi',
                            'refund' => 'Mengembalikan dana',
                            'export' => 'Mengekspor',
                            'audit_log' => 'Melihat log audit',
                            'view_reports' => 'Melihat laporan',
                            'view_history' => 'Melihat riwayat',
                            'mark_ready' => 'Tandai siap',
                            'complete' => 'Menyelesaikan',
                            'cancel' => 'Membatalkan',
                            'view_queue' => 'Melihat antrian',
                            'manage_sounds' => 'Mengelola suara',
                            'update_price' => 'Mengubah harga',
                            'toggle_availability' => 'Mengubah ketersediaan',
                            'view_categories' => 'Melihat kategori',
                            'view_sales' => 'Melihat penjualan',
                            'view_hourly' => 'Melihat per jam',
                            'send_email' => 'Mengirim email',
                            'show' => 'Menampilkan detail',
                            'update_status' => 'Mengubah status',
                            default => $action
                        };
                    @endphp
                    <label class="px-6 py-4 hover:bg-slate-50 dark:hover:bg-white/[0.03] transition-colors duration-200 flex items-center gap-4 cursor-pointer group">
                        <!-- Checkbox -->
                        <input type="checkbox" 
                               name="permissions[]" 
                               value="{{ $permission->name }}"
                               {{ $isChecked ? 'checked' : '' }}
                               class="w-5 h-5 rounded border-2 border-slate-300 dark:border-white/20 bg-white dark:bg-slate-900 cursor-pointer transition-all"
                               style="accent-color: var(--primary-color); color: var(--primary-color);" >

                        <!-- Permission Info -->
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-bold text-slate-900 dark:text-white transition-colors" @mouseenter="$el.style.color = 'var(--primary-color)'" @mouseleave="$el.style.color = ''">
                                {{ $permission->name }}
                            </p>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                                {{ $actionText }}
                            </p>
                        </div>

                        <!-- Status Badge -->
                        @if($isChecked)
                            <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[10px] font-black uppercase tracking-widest bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300 whitespace-nowrap">
                                <i class="ri-check-line mr-1"></i>Aktif
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[10px] font-black uppercase tracking-widest bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 whitespace-nowrap">
                                <i class="ri-close-line mr-1"></i>Nonaktif
                            </span>
                        @endif
                    </label>
                @endforeach
            </div>
        </div>
    @empty
        <div class="p-8 text-center border border-slate-200 dark:border-white/5 rounded-lg">
            <p class="text-slate-500 dark:text-slate-400">No permissions available</p>
        </div>
    @endforelse

    <!-- ACTION BUTTONS -->
    <div class="flex flex-col sm:flex-row gap-4 items-center justify-between pt-6 border-t border-slate-200 dark:border-white/5">
        <a href="{{ route('admin.permissions.select-user') }}" 
           class="inline-flex items-center gap-2 text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white font-bold text-sm transition-colors">
            <i class="ri-arrow-left-line"></i>
            <span>Kembali ke Daftar User</span>
        </a>

        <div class="flex gap-3 w-full sm:w-auto">
            <button type="reset" 
                    class="flex-1 sm:flex-none px-6 py-2.5 border border-slate-300 dark:border-white/20 text-slate-900 dark:text-white text-[10px] font-black uppercase tracking-widest rounded-md hover:bg-slate-100 dark:hover:bg-white/5 transition-all">
                Reset
            </button>
            <button type="submit" 
                    id="submitBtn"
                    class="flex-1 sm:flex-none px-6 py-2.5 text-black text-[10px] font-black uppercase tracking-widest rounded-md transition-all flex items-center justify-center gap-2 group hover:shadow-md"
                    style="background-color: var(--primary-color);"
                    @mouseenter="$el.style.opacity = '0.85'"
                    @mouseleave="$el.style.opacity = '1'">
                <i class="ri-save-line group-hover:scale-110 transition-transform"></i>
                <span>Simpan Permissions</span>
            </button>
        </div>
    </div>
</form>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Store initial checkbox states for reset functionality
        const initialStates = {};
        document.querySelectorAll('input[name="permissions[]"]').forEach(checkbox => {
            initialStates[checkbox.value] = checkbox.checked;
        });

        // Real-time status badge update saat checkbox di klik
        document.querySelectorAll('input[name="permissions[]"]').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const label = this.closest('label');
                const statusBadge = label.querySelector('span:last-child');
                
                if (this.checked) {
                    // Change to Aktif (green)
                    statusBadge.className = 'inline-flex items-center px-2.5 py-1 rounded-md text-[10px] font-black uppercase tracking-widest bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300 whitespace-nowrap';
                    statusBadge.innerHTML = '<i class="ri-check-line mr-1"></i>Aktif';
                } else {
                    // Change to Nonaktif (gray)
                    statusBadge.className = 'inline-flex items-center px-2.5 py-1 rounded-md text-[10px] font-black uppercase tracking-widest bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 whitespace-nowrap';
                    statusBadge.innerHTML = '<i class="ri-close-line mr-1"></i>Nonaktif';
                }
            });
        });

        // Reset button functionality
        const resetBtn = document.querySelector('button[type="reset"]');
        if (resetBtn) {
            resetBtn.addEventListener('click', function(e) {
                e.preventDefault();
                
                // Uncheck semua permissions (UI saja, database tetap)
                document.querySelectorAll('input[name="permissions[]"]').forEach(checkbox => {
                    checkbox.checked = false;
                    
                    // Trigger change event to update badge ke nonaktif
                    checkbox.dispatchEvent(new Event('change', { bubbles: true }));
                });
            });
        }

        // Form submit functionality
        const permissionsForm = document.getElementById('permissionsForm');
        if (permissionsForm) {
            permissionsForm.addEventListener('submit', async function(e) {
                e.preventDefault();
                
                const submitBtn = document.getElementById('submitBtn');
                const successMsg = document.getElementById('successMessage');
                const errorMsg = document.getElementById('errorMessage');
                const successText = document.getElementById('successText');
                const errorText = document.getElementById('errorText');
                
                // Safety check: if critical elements are missing, abort function
                if (!submitBtn || !successMsg || !errorMsg || !successText || !errorText) {
                    console.error('Required permission form elements not found');
                    return;
                }
                
                const originalBtnHTML = submitBtn.innerHTML;
                
                // Show loading state
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="ri-loader-4-line animate-spin"></i> Menyimpan...';
                successMsg.classList.add('hidden');
                errorMsg.classList.add('hidden');
                
                try {
                    const formData = new FormData(this);
                    const permissions = formData.getAll('permissions[]');
                    
                    const csrfToken = document.querySelector('meta[name="csrf-token"]');
                    const response = await fetch('{{ route("admin.permissions.update", $user->id) }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken ? csrfToken.getAttribute('content') : ''
                        },
                        body: JSON.stringify({
                            permissions: permissions
                        })
                    });
                    
                    const result = await response.json();
                    
                    if (result.success) {
                        successText.textContent = result.message;
                        successMsg.classList.remove('hidden');
                        successMsg.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                        
                        // Redirect to permissions select-user page after 2 seconds
                        setTimeout(() => {
                            window.location.href = result.redirect_url;
                        }, 2000);
                    } else {
                        errorText.textContent = result.message;
                        errorMsg.classList.remove('hidden');
                        errorMsg.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                    }
                } catch (error) {
                    errorText.textContent = 'Terjadi kesalahan: ' + error.message;
                    errorMsg.classList.remove('hidden');
                    errorMsg.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                } finally {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalBtnHTML;
                }
            });
        }

    });
</script>
@endpush

@endsection
