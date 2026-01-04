@extends('layouts.admin')

@section('title', 'Manajemen Stok Makanan')

@section('content')
    <div class="min-h-screen bg-white dark:bg-[#050505] p-6 lg:p-10">

        <!-- HEADER -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 border-b border-slate-200 dark:border-white/5 pb-8 mb-10">
            <div class="space-y-1">
                <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white uppercase">
                    Manajemen <span style="color: var(--primary-color);">Stok Makanan</span>
                </h1>
                <p class="text-xs text-slate-500 dark:text-gray-500 font-medium">
                    Kelola inventory makanan dan pantau ketersediaan stok real-time
                </p>
            </div>

            <!-- Tombol Tambah -->
            <button onclick="showAddModal()" 
                class="text-black text-[10px] font-black uppercase tracking-widest py-3 px-6 rounded-md transition-all shadow-sm flex items-center gap-2 active:scale-95 btn-primary"
                style="background-color: var(--primary-color);">
                <i class="ri-add-circle-line text-lg"></i>
                Tambah Stok
            </button>
        </div>

        <!-- ALERT SUCCESS -->
        @if(session('success'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
                class="mb-8 flex items-center justify-between bg-emerald-500/10 border border-emerald-500/20 px-4 py-3 rounded-md transition-all">
                <div class="flex items-center gap-3 text-emerald-500">
                    <i class="ri-checkbox-circle-fill text-lg"></i>
                    <span class="text-[11px] font-black uppercase tracking-widest">{{ session('success') }}</span>
                </div>
                <button @click="show = false" class="text-emerald-500/50 hover:text-emerald-500"><i class="ri-close-line"></i></button>
            </div>
        @endif

        <!-- TABLE -->
        <div class="overflow-x-auto">
            <table class="w-full border-collapse">
                <thead>
                    <tr class="bg-slate-50 dark:bg-white/5 border-y border-slate-200 dark:border-white/10">
                        <th class="px-6 py-4 text-left text-[11px] font-black uppercase tracking-widest text-slate-700 dark:text-gray-400">
                            Nama Menu
                        </th>
                        <th class="px-6 py-4 text-left text-[11px] font-black uppercase tracking-widest text-slate-700 dark:text-gray-400">
                            Kategori
                        </th>
                        <th class="px-6 py-4 text-center text-[11px] font-black uppercase tracking-widest text-slate-700 dark:text-gray-400">
                            Stok Tersedia
                        </th>
                        <th class="px-6 py-4 text-center text-[11px] font-black uppercase tracking-widest text-slate-700 dark:text-gray-400">
                            Level Reorder
                        </th>
                        <th class="px-6 py-4 text-center text-[11px] font-black uppercase tracking-widest text-slate-700 dark:text-gray-400">
                            Status
                        </th>
                        <th class="px-6 py-4 text-center text-[11px] font-black uppercase tracking-widest text-slate-700 dark:text-gray-400">
                            Terakhir Update
                        </th>
                        <th class="px-6 py-4 text-center text-[11px] font-black uppercase tracking-widest text-slate-700 dark:text-gray-400">
                            Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-white/5">
                    @forelse($inventories as $inventory)
                        <tr class="hover:bg-slate-50 dark:hover:bg-white/5 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    @if($inventory->menu->image)
                                        <img src="{{ asset('storage/' . $inventory->menu->image) }}" 
                                            alt="{{ $inventory->menu->name }}"
                                            class="w-10 h-10 rounded-md object-cover">
                                    @else
                                        <div class="w-10 h-10 bg-slate-200 dark:bg-white/10 rounded-md flex items-center justify-center">
                                            <i class="ri-image-line text-slate-400"></i>
                                        </div>
                                    @endif
                                    <div>
                                        <p class="text-sm font-semibold text-slate-900 dark:text-white">
                                            {{ $inventory->menu->name }}
                                        </p>
                                        <p class="text-xs text-slate-500 dark:text-gray-500">
                                            ID: {{ $inventory->menu->id }}
                                        </p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-xs font-medium text-slate-700 dark:text-gray-300">
                                    {{ $inventory->menu->category->name ?? '-' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="text-sm font-bold text-slate-900 dark:text-white">
                                    {{ $inventory->quantity }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="text-xs text-slate-600 dark:text-gray-400">
                                    {{ $inventory->reorder_level }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($inventory->isInStock())
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest" 
                                        style="background-color: rgba(34, 197, 94, 0.1); color: #22c55e;">
                                        <span class="w-2 h-2 rounded-full" style="background-color: #22c55e;"></span>
                                        Tersedia
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest"
                                        style="background-color: rgba(239, 68, 68, 0.1); color: #ef4444;">
                                        <span class="w-2 h-2 rounded-full" style="background-color: #ef4444;"></span>
                                        Habis
                                    </span>
                                @endif
                                @if($inventory->isBelowReorderLevel())
                                    <div class="mt-2">
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest"
                                            style="background-color: rgba(245, 158, 11, 0.1); color: #f59e0b;">
                                            <i class="ri-alert-line"></i>
                                            Perlu Reorder
                                        </span>
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center text-xs text-slate-600 dark:text-gray-400">
                                @if($inventory->last_restocked_at)
                                    {{ $inventory->last_restocked_at->format('d M Y, H:i') }}
                                @else
                                    <span class="text-slate-400">Belum update</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <!-- Edit Button -->
                                    <button onclick="showEditModal({{ $inventory->id }}, {{ $inventory->menu_id }}, {{ $inventory->quantity }}, {{ $inventory->reorder_level }})"
                                        class="w-8 h-8 rounded-md flex items-center justify-center hover:scale-110 transition-transform text-slate-600 dark:text-gray-400 hover:bg-slate-100 dark:hover:bg-white/10"
                                        title="Edit">
                                        <i class="ri-edit-2-line"></i>
                                    </button>

                                    <!-- Delete Button -->
                                    <form action="{{ route('admin.inventory.destroy', $inventory->id) }}" method="POST" 
                                        class="inline-block"
                                        onsubmit="return confirm('Hapus stok ini? Tindakan ini tidak dapat dibatalkan.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                            class="w-8 h-8 rounded-md flex items-center justify-center hover:scale-110 transition-transform text-red-600 hover:bg-red-100 dark:hover:bg-red-500/10">
                                            <i class="ri-delete-bin-line"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="space-y-3">
                                    <div class="w-12 h-12 bg-slate-100 dark:bg-white/5 rounded-lg flex items-center justify-center mx-auto">
                                        <i class="ri-inbox-line text-2xl text-slate-400"></i>
                                    </div>
                                    <p class="text-sm text-slate-500 dark:text-gray-500 font-medium">
                                        Belum ada stok yang dikelola
                                    </p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- PAGINATION -->
        @if($inventories->hasPages())
            <div class="mt-8">
                {{ $inventories->links() }}
            </div>
        @endif
    </div>

    <!-- ADD MODAL -->
    <div id="addModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white dark:bg-[#0A0A0A] rounded-lg shadow-xl max-w-md w-full border border-slate-200 dark:border-white/10">
            <!-- Header -->
            <div class="px-6 py-4 border-b border-slate-200 dark:border-white/10 flex items-center justify-between">
                <h2 class="text-lg font-bold text-slate-900 dark:text-white">Tambah Stok Makanan</h2>
                <button onclick="closeAddModal()" class="text-slate-500 hover:text-slate-700 dark:hover:text-gray-300">
                    <i class="ri-close-line text-xl"></i>
                </button>
            </div>

            <!-- Body -->
            <form action="{{ route('admin.inventory.store') }}" method="POST" class="p-6 space-y-4">
                @csrf

                <!-- Menu Selection -->
                <div>
                    <label class="block text-[11px] font-black uppercase tracking-widest text-slate-700 dark:text-gray-400 mb-2">
                        Pilih Menu
                    </label>
                    <select name="menu_id" required 
                        class="w-full px-3 py-2 rounded-md border border-slate-300 dark:border-white/10 bg-white dark:bg-white/5 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-offset-0 focus:border-transparent transition-all"
                        style="--tw-ring-color: var(--primary-color);">
                        <option value="">-- Pilih Menu --</option>
                        @foreach($menus as $menu)
                            @if(!$menu->inventory)
                                <option value="{{ $menu->id }}">{{ $menu->name }} - {{ $menu->category->name ?? '-' }}</option>
                            @endif
                        @endforeach
                    </select>
                    @error('menu_id')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Quantity -->
                <div>
                    <label class="block text-[11px] font-black uppercase tracking-widest text-slate-700 dark:text-gray-400 mb-2">
                        Stok Tersedia
                    </label>
                    <input type="number" name="quantity" min="0" required
                        class="w-full px-3 py-2 rounded-md border border-slate-300 dark:border-white/10 bg-white dark:bg-white/5 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-offset-0 focus:border-transparent transition-all"
                        style="--tw-ring-color: var(--primary-color);"
                        placeholder="Jumlah stok">
                    @error('quantity')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Reorder Level -->
                <div>
                    <label class="block text-[11px] font-black uppercase tracking-widest text-slate-700 dark:text-gray-400 mb-2">
                        Level Reorder (Opsional)
                    </label>
                    <input type="number" name="reorder_level" min="0" value="10"
                        class="w-full px-3 py-2 rounded-md border border-slate-300 dark:border-white/10 bg-white dark:bg-white/5 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-offset-0 focus:border-transparent transition-all"
                        style="--tw-ring-color: var(--primary-color);"
                        placeholder="Jumlah minimum sebelum reorder">
                    @error('reorder_level')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Buttons -->
                <div class="flex gap-3 pt-4">
                    <button type="button" onclick="closeAddModal()"
                        class="flex-1 px-4 py-2 rounded-md text-slate-700 dark:text-gray-300 bg-slate-100 dark:bg-white/10 hover:bg-slate-200 dark:hover:bg-white/20 transition-colors text-sm font-semibold uppercase tracking-widest">
                        Batal
                    </button>
                    <button type="submit"
                        class="flex-1 px-4 py-2 rounded-md text-white text-sm font-semibold uppercase tracking-widest transition-all active:scale-95"
                        style="background-color: var(--primary-color);">
                        Tambah Stok
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- EDIT MODAL -->
    <div id="editModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white dark:bg-[#0A0A0A] rounded-lg shadow-xl max-w-md w-full border border-slate-200 dark:border-white/10">
            <!-- Header -->
            <div class="px-6 py-4 border-b border-slate-200 dark:border-white/10 flex items-center justify-between">
                <h2 class="text-lg font-bold text-slate-900 dark:text-white">Edit Stok</h2>
                <button onclick="closeEditModal()" class="text-slate-500 hover:text-slate-700 dark:hover:text-gray-300">
                    <i class="ri-close-line text-xl"></i>
                </button>
            </div>

            <!-- Body -->
            <form id="editForm" method="POST" class="p-6 space-y-4">
                @csrf
                @method('PUT')

                <!-- Menu Name (Readonly) -->
                <div>
                    <label class="block text-[11px] font-black uppercase tracking-widest text-slate-700 dark:text-gray-400 mb-2">
                        Menu
                    </label>
                    <input type="text" id="editMenuName" readonly
                        class="w-full px-3 py-2 rounded-md border border-slate-300 dark:border-white/10 bg-slate-100 dark:bg-white/5 text-slate-900 dark:text-white text-sm">
                </div>

                <!-- Quantity -->
                <div>
                    <label class="block text-[11px] font-black uppercase tracking-widest text-slate-700 dark:text-gray-400 mb-2">
                        Stok Tersedia
                    </label>
                    <input type="number" id="editQuantity" name="quantity" min="0" required
                        class="w-full px-3 py-2 rounded-md border border-slate-300 dark:border-white/10 bg-white dark:bg-white/5 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-offset-0 focus:border-transparent transition-all"
                        style="--tw-ring-color: var(--primary-color);">
                </div>

                <!-- Reorder Level -->
                <div>
                    <label class="block text-[11px] font-black uppercase tracking-widest text-slate-700 dark:text-gray-400 mb-2">
                        Level Reorder
                    </label>
                    <input type="number" id="editReorderLevel" name="reorder_level" min="0"
                        class="w-full px-3 py-2 rounded-md border border-slate-300 dark:border-white/10 bg-white dark:bg-white/5 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-offset-0 focus:border-transparent transition-all"
                        style="--tw-ring-color: var(--primary-color);">
                </div>

                <!-- Buttons -->
                <div class="flex gap-3 pt-4">
                    <button type="button" onclick="closeEditModal()"
                        class="flex-1 px-4 py-2 rounded-md text-slate-700 dark:text-gray-300 bg-slate-100 dark:bg-white/10 hover:bg-slate-200 dark:hover:bg-white/20 transition-colors text-sm font-semibold uppercase tracking-widest">
                        Batal
                    </button>
                    <button type="submit"
                        class="flex-1 px-4 py-2 rounded-md text-white text-sm font-semibold uppercase tracking-widest transition-all active:scale-95"
                        style="background-color: var(--primary-color);">
                        Update Stok
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function showAddModal() {
            const addModal = document.getElementById('addModal');
            if (addModal) {
                addModal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            }
        }

        function closeAddModal() {
            const addModal = document.getElementById('addModal');
            if (addModal) {
                addModal.classList.add('hidden');
                document.body.style.overflow = 'auto';
            }
        }

        function showEditModal(inventoryId, menuId, quantity, reorderLevel) {
            const menu = @json($menus);
            const menuName = menu.find(m => m.id === menuId)?.name || 'Unknown';
            
            const editMenuName = document.getElementById('editMenuName');
            const editQuantity = document.getElementById('editQuantity');
            const editReorderLevel = document.getElementById('editReorderLevel');
            const editForm = document.getElementById('editForm');
            const editModal = document.getElementById('editModal');
            
            // Safety check: if critical elements are missing, abort function
            if (!editMenuName || !editQuantity || !editReorderLevel || !editForm || !editModal) {
                console.error('Required edit modal elements not found');
                return;
            }
            
            editMenuName.value = menuName;
            editQuantity.value = quantity;
            editReorderLevel.value = reorderLevel;
            editForm.action = `/admin/inventory/${inventoryId}`;
            
            editModal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeEditModal() {
            const editModal = document.getElementById('editModal');
            if (editModal) {
                editModal.classList.add('hidden');
                document.body.style.overflow = 'auto';
            }
        }

        // Close modal on escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                closeAddModal();
                closeEditModal();
            }
        });

        // Close modal on outside click
        const addModal = document.getElementById('addModal');
        if (addModal) {
            addModal.addEventListener('click', (e) => {
                if (e.target === e.currentTarget) closeAddModal();
            });
        }

        const editModal = document.getElementById('editModal');
        if (editModal) {
            editModal.addEventListener('click', (e) => {
                if (e.target === e.currentTarget) closeEditModal();
            });
        }
    </script>
@endsection
