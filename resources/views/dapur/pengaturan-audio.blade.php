@extends('layouts.dapur')

@section('title', 'Pengaturan Audio - Billiard Class')

{{-- Include shift calculation PHP block --}}
@include('dapur.partials.shift-calculation')

{{-- Include shift meta tags --}}
@include('dapur.partials.shift-meta')

{{-- Include theme initialization script --}}
@include('dapur.partials.theme-manager')

{{-- Include dynamic color variables --}}
@include('dapur.partials.color-variables')

{{-- Include common styles --}}
@include('dapur.partials.common-styles')

@push('head')
    <script>
    // Alpine.js untuk Pengaturan Audio - harus didaftarkan sebelum Alpine memproses DOM
    document.addEventListener('alpine:init', () => {
            Alpine.data('kitchenNotification', () => ({
                availableSounds: [],
                selectedAudio: localStorage.getItem('kitchenNotificationAudio') || '',
                audioElement: null,
                newAudioName: '',
                previewFile: null,
                currentAudioName: '',
                isUploading: false,

                async init() {
                    this.audioElement = document.getElementById('testAudioElement');
                    await this.loadAvailableSounds();
                    this.loadSavedAudioPreference();
                },

                async loadAvailableSounds() {
                    try {
                        const response = await fetch('/notification-sounds');
                        if (response.ok) {
                            this.availableSounds = await response.json();
                        }
                    } catch (error) {
                        console.error('Error loading sounds:', error);
                    }
                },

                loadSavedAudioPreference() {
                    const savedAudio = localStorage.getItem('kitchenNotificationAudio');
                    const audioType = localStorage.getItem('kitchenNotificationAudioType');
                    
                    if (savedAudio && savedAudio !== '') {
                        if (audioType === 'database') {
                            const sound = this.availableSounds.find(s => s.filename === savedAudio);
                            if (sound) {
                                this.selectedAudio = savedAudio;
                                this.currentAudioName = sound.name || '';
                                this.updateAudioSource();
                            } else {
                                this.selectedAudio = '';
                                this.currentAudioName = '';
                                localStorage.removeItem('kitchenNotificationAudio');
                                localStorage.removeItem('kitchenNotificationAudioType');
                            }
                        } else {
                            // File type - not persistent, clear it
                            this.selectedAudio = '';
                            this.currentAudioName = '';
                            localStorage.removeItem('kitchenNotificationAudio');
                            localStorage.removeItem('kitchenNotificationAudioType');
                        }
                    } else {
                        this.selectedAudio = '';
                        this.currentAudioName = '';
                    }
                },

                updateAudioSource() {
                    const source = document.getElementById('testAudioSource');
                    if (!source || !this.audioElement) return;

                    if (!this.selectedAudio || this.selectedAudio === '') {
                        source.src = '';
                        source.type = '';
                        this.audioElement.pause();
                        return;
                    }
                    
                    // Check if selectedAudio is a file object (direct file)
                    if (this.selectedAudio instanceof File) {
                        source.src = URL.createObjectURL(this.selectedAudio);
                        source.type = this.selectedAudio.type || '';
                    } else {
                        // It's a filename from database
                        const sound = this.availableSounds.find(s => s.filename === this.selectedAudio);
                        if (sound) {
                            // Use storage path if file exists in storage
                            if (sound.file_path.startsWith('sounds/')) {
                                source.src = '{{ url("/notification-sounds") }}/' + sound.id + '/file';
                                source.type = '';
                            } else {
                                source.src = '{{ asset("assets/sounds") }}/' + sound.filename;
                                source.type = '';
                            }
                        } else {
                            // No sound found, clear source
                            source.src = '';
                            source.type = '';
                            return;
                        }
                    }
                    
                    if (this.audioElement) {
                        this.audioElement.load();
                    }
                },

                getCurrentAudioName() {
                    if (this.selectedAudio instanceof File) {
                        return this.selectedAudio.name;
                    }
                    const sound = this.availableSounds.find(s => s.filename === this.selectedAudio);
                    return sound ? sound.name : 'Tidak ada audio';
                },

                formatFileSize(bytes) {
                    if (bytes === 0) return '0 Bytes';
                    const k = 1024;
                    const sizes = ['Bytes', 'KB', 'MB'];
                    const i = Math.floor(Math.log(bytes) / Math.log(k));
                    return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
                },

                openFilePicker() {
                    if (this.$refs.audioFilePicker) {
                        this.$refs.audioFilePicker.click();
                    }
                },

                handleAudioFileSelect(event) {
                    const file = event.target?.files?.[0] || event?.files?.[0];
                    if (!file) return;
                    
                    // Validate file type
                    if (!file.type.startsWith('audio/')) {
                        Swal.fire({
                            icon: 'error',
                            title: 'File tidak valid',
                            text: 'Harap pilih file audio (mp3, wav, ogg)',
                            background: '#161616',
                            color: '#fff'
                        });
                        return;
                    }
                    
                    // Validate file size (max 2MB)
                    if (file.size > 2 * 1024 * 1024) {
                        Swal.fire({
                            icon: 'error',
                            title: 'File terlalu besar',
                            text: 'Ukuran file maksimal 2MB',
                            background: '#161616',
                            color: '#fff'
                        });
                        return;
                    }
                    
                    this.previewFile = file;
                    // Auto-generate name from filename if not provided
                    if (!this.newAudioName) {
                        this.newAudioName = file.name.replace(/\.[^/.]+$/, '');
                    }
                },

                async useAudioDirectly() {
                    if (!this.previewFile) return;

                    // When user wants to "use directly" we will upload the file to the server
                    // so the selection becomes persistent across sessions (logout/login).
                    const formData = new FormData();
                    const nameToUse = this.newAudioName && this.newAudioName.trim() !== '' ? this.newAudioName.trim() : this.previewFile.name.replace(/\.[^/.]+$/, '');
                    formData.append('name', nameToUse);
                    formData.append('audio', this.previewFile);

                    this.isUploading = true;
                    try {
                        const response = await fetch('/notification-sounds', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: formData
                        });

                        const data = await response.json();

                        if (data.success && data.sound) {
                            // Select the newly uploaded sound (database-backed) and persist preference
                            this.selectedAudio = data.sound.filename;
                            this.currentAudioName = data.sound.name;
                            this.saveAudioPreference();

                            // Clear preview
                            this.previewFile = null;
                            this.newAudioName = '';
                            if (this.$refs.audioFilePicker) {
                                this.$refs.audioFilePicker.value = '';
                            }

                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: 'Audio berhasil dipilih dan disimpan sebagai default',
                                background: '#161616',
                                color: '#fff',
                                timer: 2000,
                                showConfirmButton: false
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: data.message || 'Gagal menyimpan audio sebagai default',
                                background: '#161616',
                                color: '#fff'
                            });
                        }
                    } catch (error) {
                        console.error('Error uploading audio for direct use:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Terjadi kesalahan saat menyimpan audio',
                            background: '#161616',
                            color: '#fff'
                        });
                    } finally {
                        this.isUploading = false;
                    }
                },

                async uploadAudioAsNew() {
                    if (!this.previewFile) return;
                    if (this.isUploading) return;
                    
                    if (!this.newAudioName) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Nama audio diperlukan',
                            text: 'Harap isi nama audio',
                            background: '#161616',
                            color: '#fff'
                        });
                        return;
                    }

                    const formData = new FormData();
                    formData.append('name', this.newAudioName);
                    formData.append('audio', this.previewFile);

                    this.isUploading = true;
                    try {
                        const response = await fetch('/notification-sounds', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: formData
                        });

                        const data = await response.json();

                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: 'Audio berhasil disimpan',
                                background: '#161616',
                                color: '#fff'
                            });
                            
                            // Reload sounds
                            await this.loadAvailableSounds();
                            
                            // Auto-select the newly uploaded sound
                            if (data.sound) {
                                this.selectedAudio = data.sound.filename;
                                this.currentAudioName = data.sound.name;
                                this.saveAudioPreference();
                            }
                            
                            // Clear preview
                            this.previewFile = null;
                            this.newAudioName = '';
                            if (this.$refs.audioFilePicker) {
                                this.$refs.audioFilePicker.value = '';
                            }
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: data.message || 'Gagal upload audio',
                                background: '#161616',
                                color: '#fff'
                            });
                        }
                    } catch (error) {
                        console.error('Error uploading audio:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Terjadi kesalahan saat upload audio',
                            background: '#161616',
                            color: '#fff'
                        });
                    } finally {
                        this.isUploading = false;
                    }
                },

                async selectAudioFromList(sound) {
                    this.selectedAudio = sound.filename;
                    this.currentAudioName = sound.name;
                    this.saveAudioPreference();
                    
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: 'Audio berhasil dipilih',
                        background: '#161616',
                        color: '#fff',
                        timer: 2000,
                        showConfirmButton: false
                    });
                },

                saveAudioPreference() {
                    // Save filename if it's from database, or save file name if it's a direct file
                    if (this.selectedAudio instanceof File) {
                        localStorage.setItem('kitchenNotificationAudio', this.selectedAudio.name);
                        localStorage.setItem('kitchenNotificationAudioType', 'file');
                    } else {
                        localStorage.setItem('kitchenNotificationAudio', this.selectedAudio);
                        localStorage.setItem('kitchenNotificationAudioType', 'database');
                    }
                    this.updateAudioSource();
                },

                testAudio() {
                    if (!this.selectedAudio || this.selectedAudio === '') {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Tidak ada audio',
                            text: 'Harap pilih audio terlebih dahulu',
                            background: '#161616',
                            color: '#fff',
                            timer: 2000,
                            showConfirmButton: false
                        });
                        return;
                    }
                    
                    // Ensure audio source is updated before testing
                    this.updateAudioSource();
                    
                    // Wait a bit for audio to load
                    setTimeout(() => {
                        const source = document.getElementById('testAudioSource');
                        if (!this.audioElement) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Audio element tidak ditemukan',
                                background: '#161616',
                                color: '#fff',
                                timer: 2000,
                                showConfirmButton: false
                            });
                            return;
                        }
                        
                        if (!source || !source.src || source.src === '') {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Audio source tidak tersedia. Pastikan audio sudah dipilih dengan benar.',
                                background: '#161616',
                                color: '#fff',
                                timer: 2000,
                                showConfirmButton: false
                            });
                            return;
                        }
                        
                        // Reset and play
                        this.audioElement.currentTime = 0;
                        this.audioElement.play().catch(err => {
                            console.error('Audio test failed:', err);
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal memutar audio',
                                text: 'Pastikan file audio tersedia dan formatnya didukung (mp3, wav, ogg)',
                                background: '#161616',
                                color: '#fff',
                                timer: 3000,
                                showConfirmButton: false
                            });
                        });
                    }, 100);
                },

                async deleteSound(soundId) {
                    Swal.fire({
                        title: 'Hapus Audio?',
                        text: 'Audio yang dihapus tidak dapat dikembalikan',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: getComputedStyle(document.documentElement).getPropertyValue('--primary-color').trim(),
                        cancelButtonColor: '#6b7280',
                        confirmButtonText: 'Ya, Hapus',
                        cancelButtonText: 'Batal',
                        background: '#161616',
                        color: '#fff'
                    }).then(async (result) => {
                        if (result.isConfirmed) {
                            try {
                                const response = await fetch(`/notification-sounds/${soundId}`, {
                                    method: 'DELETE',
                                    headers: {
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                        'Content-Type': 'application/json'
                                    }
                                });

                                const data = await response.json();

                                if (data.success) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Berhasil',
                                        text: 'Audio berhasil dihapus',
                                        background: '#161616',
                                        color: '#fff'
                                    });
                                    
                                    // Check if deleted sound was selected
                                    const deletedSound = this.availableSounds.find(s => s.id === soundId);
                                    if (deletedSound && this.selectedAudio === deletedSound.filename) {
                                        this.selectedAudio = '';
                                        this.currentAudioName = '';
                                        localStorage.removeItem('kitchenNotificationAudio');
                                        localStorage.removeItem('kitchenNotificationAudioType');
                                        this.updateAudioSource();
                                    }
                                    
                                    // Reload sounds after a short delay
                                    setTimeout(async () => {
                                        await this.loadAvailableSounds();
                                    }, 300);
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Gagal',
                                        text: data.message || 'Gagal menghapus audio',
                                        background: '#161616',
                                        color: '#fff'
                                    });
                                    
                                    // Reload sounds anyway to refresh the list
                                    await this.loadAvailableSounds();
                                }
                            } catch (error) {
                                console.error('Error deleting sound:', error);
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: 'Terjadi kesalahan saat menghapus audio',
                                    background: '#161616',
                                    color: '#fff'
                                });
                            }
                        }
                    });
                }
            }));
        });
</script>
@endpush

{{-- Include sidebar & main content styles --}}
@include('dapur.partials.sidebar-main-styles')

@section('content')
    {{-- Logout Form --}}
    @include('dapur.partials.logout-form')

    <div class="max-w-4xl mx-auto">
                    {{-- Audio Settings Card --}}
                    <div class="bg-white dark:bg-[#1a1a1a] border border-gray-200 dark:border-white/10 p-8 rounded-2xl shadow-lg dark:shadow-2xl dark:shadow-black/30" x-data="kitchenNotification()">
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-8 flex items-center gap-3">
                            <i class="ri-settings-3-line icon-primary text-xl"></i>
                            Pengaturan Audio Notifikasi
                        </h2>
                        
                        {{-- Audio Settings --}}
                        <div class="mb-8">
                            <label class="block text-base font-semibold text-gray-900 dark:text-white mb-4">Pilih Audio Notifikasi</label>
                            
                            {{-- Current Audio Display --}}
                            <div x-show="selectedAudio" class="mb-6 p-4 bg-blue-50 dark:bg-blue-500/10 rounded-xl border border-blue-200 dark:border-blue-500/20">
                                <p class="text-xs font-semibold text-blue-600 dark:text-blue-400 mb-2 uppercase">Audio Saat Ini</p>
                                <p class="text-sm font-medium text-gray-900 dark:text-white" x-text="getCurrentAudioName() || 'Tidak ada audio dipilih'"></p>
                            </div>
                            <div x-show="!selectedAudio" class="mb-6 p-4 bg-gray-100 dark:bg-gray-500/10 rounded-xl border border-gray-300 dark:border-gray-500/20">
                                <p class="text-xs font-semibold text-gray-600 dark:text-gray-400 mb-2 uppercase">Audio Saat Ini</p>
                                <p class="text-sm font-medium text-gray-600 dark:text-gray-500">Tidak ada audio dipilih</p>
                            </div>
                            
                            {{-- File Picker untuk Pilih/Upload Audio --}}
                            <div class="mb-8">
                                <input type="file" @change="handleAudioFileSelect($event)" accept="audio/*" 
                                    class="hidden" x-ref="audioFilePicker">
                                <button @click="openFilePicker()" 
                                    class="w-full bg-gradient-to-r from-purple-600 to-purple-700 hover:from-purple-700 hover:to-purple-800 text-white text-sm font-semibold py-3 px-4 rounded-xl transition-all shadow-md hover:shadow-lg mb-4 flex items-center justify-center gap-2">
                                    <i class="ri-file-music-line"></i> Pilih File Audio
                                </button>
                                
                                {{-- Preview Selected File --}}
                                <template x-if="previewFile">
                                    <div class="mb-4 p-4 bg-green-50 dark:bg-green-500/10 rounded-xl border border-green-200 dark:border-green-500/20">
                                        <p class="text-xs font-semibold text-green-600 dark:text-green-400 mb-2 uppercase">File Dipilih</p>
                                        <p class="text-sm text-gray-900 dark:text-white font-medium" x-text="previewFile.name"></p>
                                        <p class="text-xs text-gray-600 dark:text-gray-400 mt-1" x-text="formatFileSize(previewFile.size)"></p>
                                    </div>
                                </template>
                                
                                {{-- Options untuk File yang Dipilih --}}
                                <template x-if="previewFile">
                                    <div class="space-y-4 mb-4">
                                    <input type="text" x-model="newAudioName" placeholder="Nama audio (opsional)" 
                                        class="w-full bg-white dark:bg-black/40 border border-gray-300 dark:border-white/10 rounded-xl py-3 px-4 text-sm text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-500/50 focus:border-transparent transition-all">
                                    
                                    <div class="flex gap-3">
                                        <button @click="useAudioDirectly()" 
                                            class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold py-3 px-4 rounded-xl transition-all shadow-md hover:shadow-lg flex items-center justify-center gap-2">
                                            <i class="ri-check-line"></i> Gunakan Langsung
                                        </button>
                                        <button @click="uploadAudioAsNew()" 
                                            :disabled="isUploading"
                                            class="flex-1 bg-green-600 hover:bg-green-700 disabled:opacity-60 disabled:cursor-not-allowed text-white text-sm font-semibold py-3 px-4 rounded-xl transition-all shadow-md hover:shadow-lg flex items-center justify-center gap-2">
                                            <i class="ri-upload-cloud-line"></i> Simpan ke Daftar
                                        </button>
                                    </div>
                                    </div>
                                </template>
                            </div>
                            
                            {{-- Test Audio Button --}}
                            <button x-show="selectedAudio" @click="testAudio()" 
                                class="w-full bg-gradient-to-r from-purple-600 to-purple-700 hover:from-purple-700 hover:to-purple-800 text-white text-sm font-semibold py-3 px-4 rounded-xl transition-all shadow-md hover:shadow-lg mb-8 flex items-center justify-center gap-2">
                                <i class="ri-play-line"></i> Test Audio
                            </button>
                            
                            {{-- List of Saved Audios --}}
                            <div class="mt-8 pt-8 border-t border-gray-200 dark:border-white/10">
                                <label class="block text-base font-semibold text-gray-900 dark:text-white mb-4">Daftar Audio Tersimpan</label>
                                <div class="max-h-80 overflow-y-auto space-y-3 pr-2">
                                    <div x-show="availableSounds.length === 0" class="text-sm text-gray-600 dark:text-gray-500 text-center py-8">
                                        <i class="ri-inbox-line text-4xl mb-2 opacity-50"></i>
                                        <p>Belum ada audio tersimpan</p>
                                    </div>
                                    <template x-for="sound in availableSounds" :key="sound.id">
                                        <div class="flex items-center justify-between bg-gray-50 dark:bg-white/5 rounded-xl p-4 border border-gray-200 dark:border-white/10 hover:bg-gray-100 dark:hover:bg-white/10 transition-all">
                                            <span class="text-sm text-gray-900 dark:text-white flex-1" x-text="sound.name"></span>
                                            <div class="flex items-center gap-2">
                                                <button @click.stop="selectAudioFromList(sound)" 
                                                    class="text-sm text-blue-400 hover:text-blue-300 px-3 py-1 rounded-lg hover:bg-blue-500/10 transition-all cursor-pointer relative z-10" 
                                                    title="Gunakan audio ini"
                                                    type="button">
                                                    <i class="ri-check-line"></i> Pilih
                                                </button>
                                                <button @click.stop="deleteSound(sound.id)" 
                                                    class="text-sm text-red-400 hover:text-red-300 px-3 py-1 rounded-lg hover:bg-red-500/10 transition-all cursor-pointer relative z-10" 
                                                    title="Hapus"
                                                    type="button">
                                                    <i class="ri-delete-bin-line"></i> Hapus
                                                </button>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    {{-- Hidden Audio Element untuk Testing --}}
    <audio id="testAudioElement" preload="auto">
        <source id="testAudioSource" src="">
    </audio>

    @push('scripts')
    <script>
        // Sidebar Toggle

        // Menu Navigation
        const menuOrders = document.getElementById('menu-orders');
        const menuAudio = document.getElementById('menu-audio');

        if (menuOrders) {
            menuOrders.addEventListener('click', (e) => {
                if (window.innerWidth <= 768) {
                    sidebar.classList.add('collapsed');
                    sidebarOverlay.classList.remove('show');
                }
            });
        }

        if (menuAudio) {
            menuAudio.addEventListener('click', (e) => {
                if (window.innerWidth <= 768) {
                    sidebar.classList.add('collapsed');
                    sidebarOverlay.classList.remove('show');
                }
            });
        }

    </script>

    {{-- Hidden Logout Form --}}
    {{-- Include theme manager script --}}
    @include('dapur.partials.theme-manager')
    
    {{-- Include shift check script --}}
    @include('dapur.partials.shift-check-script')
    @endpush
@endsection
