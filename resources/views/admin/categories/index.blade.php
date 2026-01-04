@extends('layouts.admin')

@section('title', 'Category Master')

@section('content')
    <div class="space-y-8 animate-in fade-in duration-500">

        <!-- HEADER: Professional & Precise -->
        <div
            class="flex flex-col md:flex-row md:items-center justify-between gap-6 border-b border-slate-200 dark:border-white/5 pb-8">
            <div class="space-y-1">
                <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">
                    Category Master
                </h1>
                <p class="text-xs text-slate-500 dark:text-gray-500 font-medium">
                    Organisir pengelompokan menu dan atur prioritas tampilan katalog.
                </p>
            </div>

            <div class="flex items-center gap-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                <i class="ri-information-line text-sm"></i>
                <span>Total: {{ $categories->count() }} Categories</span>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">

            <!-- SIDE: Quick Create Form -->
            <div class="lg:col-span-4">
                <div class="sticky top-24 space-y-6">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="w-1 h-4 rounded-full" style="background-color: var(--primary-color);"></span>
                        <h2 class="text-xs font-black uppercase tracking-[0.2em] text-slate-900 dark:text-white">Create New
                            Category</h2>
                    </div>

                    <form action="{{ route('admin.categories.store') }}" method="POST" class="space-y-5">
                        @csrf
                        <div class="space-y-2">
                            <label
                                class="text-[10px] font-bold text-slate-400 dark:text-gray-500 uppercase tracking-[0.15em] ml-1">Category
                                Name</label>
                            <input type="text" name="name" required placeholder="Ex: Main Course"
                                class="w-full bg-slate-50 dark:bg-white/[0.02] border border-slate-200 dark:border-white/10 rounded-md py-3 px-4 text-sm dark:text-white outline-none transition-all placeholder:text-slate-300 dark:placeholder:text-gray-700 font-medium"
                                @focus="$el.style.borderColor = 'var(--primary-color)'"
                                @blur="$el.style.borderColor = ''">
                        </div>

                        <div class="space-y-2">
                            <label
                                class="text-[10px] font-bold text-slate-400 dark:text-gray-500 uppercase tracking-[0.15em] ml-1">Order
                                Priority</label>
                            <input type="number" name="order_priority" value="0" required
                                class="w-full bg-slate-50 dark:bg-white/[0.02] border border-slate-200 dark:border-white/10 rounded-md py-3 px-4 text-sm dark:text-white outline-none transition-all font-bold"
                                @focus="$el.style.borderColor = 'var(--primary-color)'"
                                @blur="$el.style.borderColor = ''">
                            <p class="text-[9px] text-slate-400 dark:text-gray-600 italic font-medium">Angka lebih kecil
                                akan muncul lebih awal di website.</p>
                        </div>

                        <button type="submit"
                            class="w-full text-black font-extrabold py-3.5 rounded-md transition-all shadow-lg uppercase tracking-[0.2em] text-[10px]"
                            style="background-color: var(--primary-color); box-shadow: 0 10px 15px -3px rgba(var(--primary-color-rgb), 0.1);"
                            @mouseenter="$el.style.opacity = '0.85'"
                            @mouseleave="$el.style.opacity = '1'">
                            Save Category
                        </button>
                    </form>
                </div>
            </div>

            <!-- MAIN: Category List -->
            <div class="lg:col-span-8">
                <div class="flex items-center gap-2 mb-6">
                    <span class="w-1 h-4 bg-slate-200 dark:bg-white/10 rounded-full"></span>
                    <h2 class="text-xs font-black uppercase tracking-[0.2em] text-slate-900 dark:text-white">Active
                        Registries</h2>
                </div>

                <div class="grid grid-cols-1 gap-3">
                    @foreach($categories as $category)
                        <div
                            class="group flex items-center justify-between p-4 bg-white dark:bg-[#0A0A0A] border border-slate-200 dark:border-white/5 rounded-lg transition-all duration-300"
                            @mouseenter="$el.style.borderColor = 'rgba(var(--primary-color-rgb), 0.4)'"
                            @mouseleave="$el.style.borderColor = ''">
                            <div class="flex items-center gap-5">
                                <!-- Priority Badge -->
                                <div
                                    class="w-12 h-12 flex flex-col items-center justify-center rounded-md bg-slate-50 dark:bg-white/5 border border-slate-100 dark:border-white/5 transition-colors"
                                    @mouseenter="$el.style.backgroundColor = 'rgba(var(--primary-color-rgb), 0.05)'"
                                    @mouseleave="$el.style.backgroundColor = ''">
                                    <span class="text-[8px] font-black text-slate-400 uppercase leading-none mb-1">Pos</span>
                                    <span
                                        class="text-sm font-black text-slate-900 dark:text-white">{{ $category->order_priority }}</span>
                                </div>

                                <div class="space-y-0.5">
                                    <h3
                                        class="text-sm font-bold text-slate-900 dark:text-white transition-colors uppercase tracking-tight"
                                        @mouseenter="$el.style.color = 'var(--primary-color)'"
                                        @mouseleave="$el.style.color = ''">
                                        {{ $category->name }}
                                    </h3>
                                    <div class="flex items-center gap-3">
                                        <span
                                            class="text-[10px] font-medium text-slate-400 tracking-wider">/{{ $category->slug }}</span>
                                        <span class="w-1 h-1 rounded-full bg-slate-200 dark:bg-white/10"></span>
                                        <span
                                            class="text-[10px] font-bold uppercase" style="color: var(--primary-color);">{{ $category->menus->count() }}
                                            Items</span>
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center gap-2">
                                <button
                                    onclick="openEditModal('{{ $category->id }}', '{{ $category->name }}', '{{ $category->order_priority }}')"
                                    class="w-9 h-9 flex items-center justify-center rounded border border-slate-200 dark:border-white/10 text-slate-400 transition-all"
                                    @mouseenter="$el.style.borderColor = 'var(--primary-color)'; $el.style.color = 'var(--primary-color)'"
                                    @mouseleave="$el.style.borderColor = ''; $el.style.color = ''">
                                    <i class="ri-pencil-line"></i>
                                </button>

                                <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="button" onclick="confirmDelete(this)"
                                        class="w-9 h-9 flex items-center justify-center rounded border border-slate-200 dark:border-white/10 text-slate-400 hover:border-red-500 hover:text-red-500 transition-all">
                                        <i class="ri-delete-bin-line"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL: Professional Sleek Style -->
    <div id="editModal" class="fixed inset-0 z-[60] hidden items-center justify-center bg-black/60 backdrop-blur-sm p-4">
        <div
            class="bg-white dark:bg-[#0A0A0A] border border-slate-200 dark:border-white/10 w-full max-w-md rounded-lg shadow-2xl animate-in zoom-in-95 duration-200 overflow-hidden">
            <div class="p-6 border-b border-slate-100 dark:border-white/5">
                <h2 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-widest">Edit Category</h2>
            </div>

            <form id="editForm" method="POST" class="p-6 space-y-5">
                @csrf @method('PUT')
                <div class="space-y-2">
                    <label
                        class="text-[10px] font-bold text-slate-400 dark:text-gray-500 uppercase tracking-widest">Category
                        Name</label>
                    <input type="text" name="name" id="editName" required
                        class="w-full bg-slate-50 dark:bg-white/[0.02] border border-slate-200 dark:border-white/10 rounded-md py-3 px-4 text-sm dark:text-white outline-none"
                        @focus="$el.style.borderColor = 'var(--primary-color)'"
                        @blur="$el.style.borderColor = ''">
                        @focus="$el.style.borderColor = 'var(--primary-color)'"
                        @blur="$el.style.borderColor = ''">
                </div>
                <div class="space-y-2">
                    <label class="text-[10px] font-bold text-slate-400 dark:text-gray-500 uppercase tracking-widest">Order
                        Priority</label>
                    <input type="number" name="order_priority" id="editPriority" required
                        class="w-full bg-slate-50 dark:bg-white/[0.02] border border-slate-200 dark:border-white/10 rounded-md py-3 px-4 text-sm dark:text-white outline-none\"
                        @focus=\"$el.style.borderColor = 'var(--primary-color)'\"
                        @blur=\"$el.style.borderColor = ''\">
                </div>

                <div class="flex gap-3 pt-4 border-t border-slate-100 dark:border-white/5">
                    <button type="button" onclick="closeModal()"
                        class="flex-1 px-4 py-3 rounded-md border border-slate-200 dark:border-white/10 text-[10px] font-bold uppercase tracking-widest text-slate-500 hover:bg-slate-50 dark:hover:bg-white/5 transition-all">Cancel</button>
                    <button type="submit"
                        class="flex-1 px-4 py-3 rounded-md text-black text-[10px] font-black uppercase tracking-widest transition-all"
                        style="background-color: var(--primary-color);"
                        @mouseenter="$el.style.opacity = '0.85'"
                        @mouseleave="$el.style.opacity = '1'">Apply
                        Changes</button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            function openEditModal(id, name, priority) {
                const modal = document.getElementById('editModal');
                const form = document.getElementById('editForm');
                const editName = document.getElementById('editName');
                const editPriority = document.getElementById('editPriority');
                
                // Safety check: if critical elements are missing, abort function
                if (!modal || !form || !editName || !editPriority) {
                    console.error('Required modal elements not found');
                    return;
                }
                
                editName.value = name;
                editPriority.value = priority;
                form.action = `/admin/categories/${id}`;
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            }

            function closeModal() {
                const modal = document.getElementById('editModal');
                
                // Safety check: if modal element is missing, abort function
                if (!modal) {
                    console.error('Edit modal element not found');
                    return;
                }
                
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }

            function confirmDelete(button) {
                Swal.fire({
                    title: 'Delete Category?',
                    text: "Semua menu yang terhubung dengan kategori ini akan ikut terhapus secara permanen.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#1e1e1e',
                    confirmButtonText: 'Yes, Delete All',
                    background: document.documentElement.classList.contains('dark') ? '#0A0A0A' : '#fff',
                    color: document.documentElement.classList.contains('dark') ? '#fff' : '#000',
                    customClass: {
                        popup: 'rounded-lg border border-white/5 shadow-2xl',
                        confirmButton: 'rounded-md text-[10px] font-bold px-6 py-3',
                        cancelButton: 'rounded-md text-[10px] font-bold px-6 py-3'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        button.closest('form').submit();
                    }
                });
            }
        </script>
    @endpush
@endsection