@extends('layouts.admin')

@section('title', 'Edit Keunggulan Fasilitas - Admin')

@section('content')
    <div class="min-h-screen bg-white dark:bg-[#050505] p-6 lg:p-10 transition-colors duration-300">

        <!-- HEADER STANDARD -->
        <div
            class="flex flex-col md:flex-row md:items-center justify-between gap-6 border-b border-slate-200 dark:border-white/5 pb-8 mb-10">
            <div class="space-y-1">
                <a href="{{ route('admin.dashboard') }}"
                    class="group flex items-center gap-2 text-[10px] font-black uppercase tracking-widest text-slate-400 transition-all duration-300 mb-2" @mouseenter="$el.style.color = 'var(--primary-color)'" @mouseleave="$el.style.color = ''">
                    <i class="ri-arrow-left-line transition-transform group-hover:-translate-x-1"></i> Back to Dashboard
                </a>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white uppercase">Keunggulan <span
                        style="color: var(--primary-color);">Fasilitas</span></h1>
                <p class="text-xs text-slate-500 dark:text-gray-500 font-medium">Manajemen fitur unggulan dan keunggulan
                    fasilitas premium.</p>
            </div>
        </div>

        <!-- FLASH MESSAGE -->
        @if(session('success'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
                class="mb-8 flex items-center gap-3 bg-emerald-500/10 border border-emerald-500/20 px-4 py-3 rounded-md animate-in fade-in slide-in-from-top-4 duration-300">
                <i class="ri-checkbox-circle-fill text-emerald-500"></i>
                <span class="text-[11px] font-black uppercase tracking-widest text-emerald-500">{{ session('success') }}</span>
            </div>
        @endif

        <!-- ADD NEW FACILITY BUTTON -->
        <div class="mb-8">
            <button @click="$dispatch('open-add-modal')"
                class="flex items-center gap-2 text-black text-[10px] font-black uppercase tracking-widest py-3 px-6 rounded-md transition-all shadow-sm active:scale-95"
                style="background-color: var(--primary-color);" @mouseenter="$el.style.opacity = '0.85'" @mouseleave="$el.style.opacity = '1'">
                <i class="ri-add-line text-lg"></i>
                Add New Facility
            </button>
        </div>

        <!-- FACILITIES TABLE -->
        <div class="bg-white dark:bg-[#0A0A0A] border border-slate-200 dark:border-white/5 rounded-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-slate-50 dark:bg-white/[0.01] border-b border-slate-200 dark:border-white/5">
                        <tr>
                            <th class="px-6 py-4 text-left text-[10px] font-black uppercase tracking-widest text-slate-900 dark:text-white">Order</th>
                            <th class="px-6 py-4 text-left text-[10px] font-black uppercase tracking-widest text-slate-900 dark:text-white">Icon</th>
                            <th class="px-6 py-4 text-left text-[10px] font-black uppercase tracking-widest text-slate-900 dark:text-white">Title</th>
                            <th class="px-6 py-4 text-left text-[10px] font-black uppercase tracking-widest text-slate-900 dark:text-white">Subtitle</th>
                            <th class="px-6 py-4 text-center text-[10px] font-black uppercase tracking-widest text-slate-900 dark:text-white">Status</th>
                            <th class="px-6 py-4 text-right text-[10px] font-black uppercase tracking-widest text-slate-900 dark:text-white">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-white/5">
                        @forelse($keunggulanFasilitas as $facility)
                            <tr class="hover:bg-slate-50 dark:hover:bg-white/[0.02] transition-colors">
                                <td class="px-6 py-4">
                                    <span class="text-[11px] font-bold text-slate-900 dark:text-white">{{ $facility->order }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <i class="{{ $facility->icon }} text-lg" style="color: var(--primary-color);"></i>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-[11px] font-bold text-slate-900 dark:text-white">{{ $facility->title }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-[10px] text-slate-500 dark:text-gray-400">{{ Str::limit($facility->subtitle, 40) }}</span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-[9px] font-black uppercase tracking-widest {{ $facility->is_active ? 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400' : 'bg-slate-100 dark:bg-white/5 text-slate-600 dark:text-slate-400' }}">
                                        {{ $facility->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <button @click="$dispatch('open-edit-modal', {{ json_encode($facility) }})"
                                            class="p-2 rounded-md text-[10px] font-black transition-all" style="color: var(--primary-color);" @mouseenter="$el.style.opacity = '0.7'" @mouseleave="$el.style.opacity = '1'">
                                            <i class="ri-edit-line text-lg"></i>
                                        </button>
                                        <form action="{{ route('admin.cms.keunggulan-fasilitas.destroy', $facility->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="p-2 rounded-md text-red-500 text-[10px] font-black transition-all" @mouseenter="$el.style.opacity = '0.7'" @mouseleave="$el.style.opacity = '1'">
                                                <i class="ri-delete-bin-line text-lg"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <i class="ri-inbox-line text-4xl text-slate-300 dark:text-white/10 block mb-3"></i>
                                    <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">No Facilities Added Yet</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ADD/EDIT MODAL -->
    <div x-data="{
        showModal: false,
        isEditing: false,
        form: {
            id: null,
            title: '',
            subtitle: '',
            icon: '',
            name: '',
            description: '',
            order: '',
            is_active: false
        }
    }"
        @open-add-modal="showModal = true; isEditing = false; form = { id: null, title: '', subtitle: '', icon: '', name: '', description: '', order: '', is_active: false }"
        @open-edit-modal="showModal = true; isEditing = true; form = $event.detail"
        class="fixed inset-0 z-50 overflow-y-auto"
        style="display: none" x-show="showModal" @click.self="showModal = false">

        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="bg-white dark:bg-[#0A0A0A] border border-slate-200 dark:border-white/5 rounded-lg shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto"
                @click.stop>
                <!-- Modal Header -->
                <div class="sticky top-0 flex items-center justify-between px-8 py-6 border-b border-slate-200 dark:border-white/5 bg-slate-50/50 dark:bg-white/[0.01]">
                    <h2 class="text-lg font-black uppercase tracking-widest text-slate-900 dark:text-white">
                        <template x-if="isEditing">
                            Edit Facility
                        </template>
                        <template x-if="!isEditing">
                            Add New Facility
                        </template>
                    </h2>
                    <button @click="showModal = false"
                        class="p-2 rounded-md transition-all" style="color: var(--primary-color);">
                        <i class="ri-close-line text-xl"></i>
                    </button>
                </div>

                <!-- Modal Body -->
                <form @submit.prevent="submitForm" class="p-8 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Title -->
                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-gray-500">Title</label>
                            <input type="text" x-model="form.title"
                                class="w-full bg-slate-50 dark:bg-white/[0.02] border border-slate-200 dark:border-white/10 rounded-md px-4 py-3 text-sm text-slate-900 dark:text-white outline-none transition-all"
                                @focus="$el.style.borderColor = 'var(--primary-color)'" @blur="$el.style.borderColor = ''"
                                placeholder="e.g. Premium Billiard Tables" required>
                        </div>

                        <!-- Icon -->
                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-gray-500">Icon Class</label>
                            <input type="text" x-model="form.icon"
                                class="w-full bg-slate-50 dark:bg-white/[0.02] border border-slate-200 dark:border-white/10 rounded-md px-4 py-3 text-sm text-slate-900 dark:text-white outline-none transition-all"
                                @focus="$el.style.borderColor = 'var(--primary-color)'" @blur="$el.style.borderColor = ''"
                                placeholder="e.g. ri-shield-check-line" required>
                        </div>

                        <!-- Subtitle -->
                        <div class="space-y-2 md:col-span-2">
                            <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-gray-500">Subtitle</label>
                            <input type="text" x-model="form.subtitle"
                                class="w-full bg-slate-50 dark:bg-white/[0.02] border border-slate-200 dark:border-white/10 rounded-md px-4 py-3 text-sm text-slate-900 dark:text-white outline-none transition-all"
                                @focus="$el.style.borderColor = 'var(--primary-color)'" @blur="$el.style.borderColor = ''"
                                placeholder="e.g. World-class gaming experience">
                        </div>

                        <!-- Name -->
                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-gray-500">Name</label>
                            <input type="text" x-model="form.name"
                                class="w-full bg-slate-50 dark:bg-white/[0.02] border border-slate-200 dark:border-white/10 rounded-md px-4 py-3 text-sm text-slate-900 dark:text-white outline-none transition-all"
                                @focus="$el.style.borderColor = 'var(--primary-color)'" @blur="$el.style.borderColor = ''"
                                placeholder="e.g. Premium Tables">
                        </div>

                        <!-- Order -->
                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-gray-500">Display Order</label>
                            <input type="number" x-model="form.order"
                                class="w-full bg-slate-50 dark:bg-white/[0.02] border border-slate-200 dark:border-white/10 rounded-md px-4 py-3 text-sm text-slate-900 dark:text-white outline-none transition-all"
                                @focus="$el.style.borderColor = 'var(--primary-color)'" @blur="$el.style.borderColor = ''"
                                placeholder="1">
                        </div>

                        <!-- Description -->
                        <div class="space-y-2 md:col-span-2">
                            <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-gray-500">Description</label>
                            <textarea x-model="form.description" rows="4"
                                class="w-full bg-slate-50 dark:bg-white/[0.02] border border-slate-200 dark:border-white/10 rounded-md px-4 py-3 text-sm text-slate-900 dark:text-white outline-none transition-all"
                                @focus="$el.style.borderColor = 'var(--primary-color)'" @blur="$el.style.borderColor = ''"
                                placeholder="Facility description..."></textarea>
                        </div>

                        <!-- Active Status -->
                        <div class="space-y-2 md:col-span-2">
                            <label class="flex items-center gap-3 cursor-pointer">
                                <input type="checkbox" x-model="form.is_active" class="sr-only peer">
                                <div class="w-11 h-6 bg-slate-200 dark:bg-white/10 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all" style="background-color: var(--primary-color);" @change="$el.style.backgroundColor = $el.previousElementSibling.checked ? 'var(--primary-color)' : ''"></div>
                                <span class="text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-gray-500">Active</span>
                            </label>
                        </div>
                    </div>

                    <!-- Modal Actions -->
                    <div class="flex items-center justify-end gap-3 pt-6 border-t border-slate-200 dark:border-white/5">
                        <button type="button" @click="showModal = false"
                            class="px-6 py-3 rounded-md text-[10px] font-black uppercase tracking-widest border border-slate-200 dark:border-white/10 text-slate-900 dark:text-white transition-all"
                            @mouseenter="$el.style.backgroundColor = '#f1f5f9'" @mouseleave="$el.style.backgroundColor = ''">
                            Cancel
                        </button>
                        <button type="submit"
                            class="px-6 py-3 rounded-md text-black text-[10px] font-black uppercase tracking-widest transition-all"
                            style="background-color: var(--primary-color);" @mouseenter="$el.style.opacity = '0.85'" @mouseleave="$el.style.opacity = '1'">
                            <template x-if="isEditing">
                                Update Facility
                            </template>
                            <template x-if="!isEditing">
                                Add Facility
                            </template>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            const form = document.querySelector('[x-data*="showModal"]');
            
            // Safety check: if form element is missing, abort script
            if (!form) {
                console.error('Form element not found for keunggulan-fasilitas');
                return;
            }
            
            if (form.__x) {
                form.__x.submitForm = async function() {
                    try {
                        const url = this.isEditing
                            ? `{{ route('admin.cms.keunggulan-fasilitas.update', '__id__') }}`.replace('__id__', this.form.id)
                            : `{{ route('admin.cms.keunggulan-fasilitas.store') }}`;

                        const method = this.isEditing ? 'POST' : 'POST';
                        const formData = new FormData();
                        
                        Object.keys(this.form).forEach(key => {
                            if (key !== 'id') {
                                formData.append(key, this.form[key]);
                            }
                        });
                        
                        if (this.isEditing) {
                            formData.append('_method', 'POST');
                        }
                        const csrfToken = document.querySelector('meta[name="csrf-token"]');
                        if (csrfToken) {
                            formData.append('_token', csrfToken.content || '');
                        }

                        const response = await fetch(url, {
                            method: 'POST',
                            body: formData
                        });

                        if (response.ok) {
                            location.reload();
                        }
                    } catch (error) {
                        console.error('Error:', error);
                    }
                };
            }
        });
    </script>

    <style>
        /* Modal overlay animations */
        [x-show="showModal"] {
            animation: fadeIn 0.3s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }
    </style>
@endsection
