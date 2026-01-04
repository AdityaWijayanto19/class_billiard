@extends('layouts.admin')

@section('title', 'Edit Contact - Admin')

@section('content')
<div class="min-h-screen bg-white dark:bg-[#050505] p-6 lg:p-10 transition-colors duration-300">
    
    <!-- HEADER STANDARD -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 border-b border-slate-200 dark:border-white/5 pb-8 mb-10">
        <div class="space-y-1">
            <a href="{{ route('admin.dashboard') }}" class="group flex items-center gap-2 text-[10px] font-black uppercase tracking-widest text-slate-400 transition-all duration-300 mb-2"
                @mouseenter="$el.style.color = 'var(--primary-color)'"
                @mouseleave="$el.style.color = ''">
                <i class="ri-arrow-left-line transition-transform group-hover:-translate-x-1"></i> Back to Dashboard
            </a>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white uppercase">Contact <span style="color: var(--primary-color);">Page</span></h1>
            <p class="text-xs text-slate-500 dark:text-gray-500 font-medium">Manajemen konten halaman Contact Us yang akan ditampilkan di website.</p>
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

    <form action="{{ route('admin.cms.contact.update') }}" method="POST" class="space-y-12">
        @csrf

        <!-- SECTION 1: PAGE HEADER -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            <div class="lg:col-span-4">
                <h2 class="text-sm font-black uppercase tracking-[0.2em] text-slate-900 dark:text-white">Page Header</h2>
                <p class="text-xs text-slate-500 dark:text-gray-500 mt-2 leading-relaxed">Judul dan deskripsi yang akan muncul di bagian atas halaman Contact Us.</p>
            </div>
            <div class="lg:col-span-8 bg-white dark:bg-[#0A0A0A] border border-slate-200 dark:border-white/5 rounded-lg p-8 space-y-6">
                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-gray-500">Page Title</label>
                    <input type="text" name="title" value="{{ $contact->title ?? 'Contact Us' }}" 
                           class="w-full bg-slate-50 dark:bg-white/[0.02] border border-slate-200 dark:border-white/10 rounded-md px-4 py-2.5 text-sm text-slate-900 dark:text-white outline-none transition-all" @focus="$el.style.borderColor = 'var(--primary-color)'" @blur="$el.style.borderColor = ''"
                           placeholder="e.g. Contact Us">
                </div>
                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-gray-500">Navbar Label</label>
                    <input id="navbarLabelInput" type="text" name="navbar_label" value="{{ $contact->navbar_label ?? 'CONTACT US' }}"
                           class="w-full bg-slate-50 dark:bg-white/[0.02] border border-slate-200 dark:border-white/10 rounded-md px-4 py-2.5 text-sm text-slate-900 dark:text-white outline-none transition-all" @focus="$el.style.borderColor = 'var(--primary-color)'" @blur="$el.style.borderColor = ''"
                           placeholder="Text shown on navbar button, e.g. CONTACT US">
                    <p class="text-xs text-slate-400 mt-1">Nama yang akan muncul di tombol navbar (mis. CONTACT US atau Chat).</p>
                </div>
                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-gray-500">Navbar Link</label>
                    <input id="navbarLinkInput" type="text" name="navbar_link" value="{{ $contact->navbar_link ?? '' }}"
                           class="w-full bg-slate-50 dark:bg-white/[0.02] border border-slate-200 dark:border-white/10 rounded-md px-4 py-2.5 text-sm text-slate-900 dark:text-white outline-none transition-all" @focus="$el.style.borderColor = 'var(--primary-color)'" @blur="$el.style.borderColor = ''"
                           placeholder="https://wa.me/6281234567890 or /some/page">
                    <p class="text-xs text-slate-400 mt-1">Link yang akan dibuka saat pengunjung klik tombol navbar. Bisa berupa full URL (https://...) atau nomor/wa short (will be normalized to wa.me jika perlu).</p>
                </div>
                
                
                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-gray-500">WhatsApp Link</label>
                    <input id="whatsappInput" type="text" name="whatsapp" value="{{ $contact->whatsapp ?? '' }}" 
                           class="w-full bg-slate-50 dark:bg-white/[0.02] border border-slate-200 dark:border-white/10 rounded-md px-4 py-2.5 text-sm text-slate-900 dark:text-white outline-none transition-all" @focus="$el.style.borderColor = 'var(--primary-color)'" @blur="$el.style.borderColor = ''"
                           placeholder="https://wa.me/6281234567890 or 081234567890 (will be normalized)">
                    <p class="text-xs text-slate-400 mt-1">Masukkan link WhatsApp (contoh: <code>https://wa.me/6281234567890</code> atau nomor lokal seperti <code>081234567890</code>). Sistem akan menormalisasi input menjadi <code>https://wa.me/&lt;number&gt;</code> untuk digunakan di navbar.</p>

                    <!-- Preview of final link that navbar will use -->
                    <div id="whatsappPreview" class="mt-3 p-3 bg-slate-50 dark:bg-white/[0.02] border border-slate-200 dark:border-white/10 rounded-md">
                        <p class="text-xs text-slate-400 mb-2">Navbar akan memakai link berikut jika field diisi:</p>
                        <div class="flex items-center gap-3">
                            <a id="whatsappPreviewLink" href="{{ $contact->whatsapp ?? '#' }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 px-3 py-2 bg-black text-white rounded shadow">
                                <i class="ri-whatsapp-line"></i>
                                <span id="whatsappPreviewText" class="text-sm">{{ $contact->whatsapp ?? 'Tidak ada link' }}</span>
                            </a>
                            <span id="whatsappPreviewFallback" class="text-xs text-slate-400">{{-- fallback info --}}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        

        <!-- SECTION 5: VISIBILITY TOGGLE -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            <div class="lg:col-span-4">
                <h2 class="text-sm font-black uppercase tracking-[0.2em] text-slate-900 dark:text-white">Page Visibility</h2>
                <p class="text-xs text-slate-500 dark:text-gray-500 mt-2 leading-relaxed">Aktifkan atau nonaktifkan tampilan halaman Contact Us di website.</p>
            </div>
            <div class="lg:col-span-8 bg-white dark:bg-[#0A0A0A] border border-slate-200 dark:border-white/5 rounded-lg p-8">
                <label class="relative inline-flex items-center cursor-pointer"
                    x-data="{ isActive: {{ ($contact && $contact->is_active) ? 'true' : 'false' }} }">
                    <input type="checkbox" name="is_active" value="1" {{ ($contact && $contact->is_active) ? 'checked' : '' }} class="sr-only peer" @change="isActive = !isActive">
                    <div class="w-11 h-6 bg-slate-200 dark:bg-white/10 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all"
                        :style="{ backgroundColor: isActive ? 'var(--primary-color)' : '#cbd5e1' }"></div>
                    <span class="ml-3 text-[10px] font-black uppercase tracking-widest text-slate-400">Contact Page Active</span>
                </label>
            </div>
        </div>

        <!-- STICKY ACTION AREA -->
        <div class="pt-12 border-t border-slate-200 dark:border-white/5 flex justify-end">
            <button type="submit" class="w-full md:w-auto text-black text-[10px] font-black uppercase tracking-widest py-4 px-16 rounded-md transition-all shadow-lg active:scale-95" style="background-color: var(--primary-color); box-shadow: 0 10px 15px -3px rgba(var(--primary-color-rgb), 0.1);" @mouseenter="$el.style.opacity = '0.85'" @mouseleave="$el.style.opacity = '1'">
                Save Contact Page Configuration
            </button>
        </div>
    </form>
</div>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');
    body { font-family: 'Plus Jakarta Sans', sans-serif; }

    input:focus, textarea:focus {
        border-color: var(--primary-color) !important;
        box-shadow: 0 0 0 1px rgba(var(--primary-color-rgb), 0.1) !important;
    }
</style>
<script>
    document.addEventListener('DOMContentLoaded', function () {
    const input = document.getElementById('whatsappInput');
    const navbarLabelInput = document.getElementById('navbarLabelInput');
    const navbarLinkInput = document.getElementById('navbarLinkInput');
    const previewLink = document.getElementById('whatsappPreviewLink');
    const previewText = document.getElementById('whatsappPreviewText');
    const previewFallback = document.getElementById('whatsappPreviewFallback');

    // Safety check: if any critical element is missing, abort script
    if (!previewLink || !previewText || !previewFallback) {
        console.error('Required DOM elements for contact preview not found');
        return;
    }

        function normalizeWhatsApp(inputVal) {
            if (!inputVal) return null;
            let v = inputVal.trim();
            // If starts with http or https, ensure https
            if (/^https?:\/\//i.test(v)) {
                v = v.replace(/^http:\/\//i, 'https://');
                return v;
            }

                function normalizeAnyLink(inputVal) {
                    if (!inputVal) return null;
                    let v = inputVal.trim();
                    // If starts with http or https, ensure https
                    if (/^https?:\/\//i.test(v)) {
                        return v.replace(/^http:\/\//i, 'https://');
                    }
                    // If looks like digits or starts with +, normalize to wa.me
                    const digits = v.replace(/\D+/g, '');
                    if (!digits) return null;
                    if (digits.startsWith('0')) {
                        return 'https://wa.me/62' + digits.slice(1);
                    }
                    return 'https://wa.me/' + digits;
                }
            // Otherwise, strip non-digits and normalize local leading 0 -> 62 (Indonesia assumption)
            const digits = v.replace(/\D+/g, '');
            if (!digits) return null;
            if (digits.startsWith('0')) {
                return 'https://wa.me/62' + digits.slice(1);
            }
            // Already has country code or full digits
            return 'https://wa.me/' + digits;
        }

        function updatePreview() {
            const waVal = input ? input.value : '';
            const navLinkVal = navbarLinkInput ? navbarLinkInput.value : '';
            const navLabelVal = navbarLabelInput ? navbarLabelInput.value : '';

            // navbar link takes precedence if provided
            let normalized = normalizeAnyLink(navLinkVal) || normalizeWhatsApp(waVal);
            const label = navLabelVal && navLabelVal.trim() !== '' ? navLabelVal.trim() : 'CONTACT US';

            if (normalized) {
                previewLink.href = normalized;
                previewText.textContent = label + ' — ' + normalized;
                previewFallback.textContent = '';
            } else {
                previewLink.href = '#';
                previewText.textContent = label + ' — Tidak ada link';
                previewFallback.textContent = 'Jika kosong, navbar akan fallback ke anchor/contact page.';
            }
        }

        if (input) {
            input.addEventListener('input', updatePreview);
            input.addEventListener('blur', updatePreview);
        }
        if (navbarLinkInput) {
            navbarLinkInput.addEventListener('input', updatePreview);
            navbarLinkInput.addEventListener('blur', updatePreview);
        }
        if (navbarLabelInput) {
            navbarLabelInput.addEventListener('input', updatePreview);
            navbarLabelInput.addEventListener('blur', updatePreview);
        }

        // Initialize
        updatePreview();
    });
</script>
@endsection

