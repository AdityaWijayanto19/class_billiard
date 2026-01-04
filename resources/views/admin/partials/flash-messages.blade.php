{{-- Flash Messages untuk Admin Pages --}}
@if(session('success'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" 
         class="mb-8 flex items-center gap-3 bg-emerald-500/10 border border-emerald-500/20 px-4 py-3 rounded-md animate-in fade-in slide-in-from-top-4 duration-300">
        <i class="ri-checkbox-circle-fill text-emerald-500"></i>
        <span class="text-[11px] font-black uppercase tracking-widest text-emerald-500">{{ session('success') }}</span>
    </div>
@endif

@if(session('error'))
    <div x-data="{ show: true }" x-show="show" 
         class="mb-8 p-4 bg-red-500/10 border border-red-500/20 rounded-md animate-in fade-in slide-in-from-top-4 duration-300">
        <div class="flex items-center gap-2 mb-3">
            <i class="ri-error-warning-fill text-red-500"></i>
            <span class="text-[10px] font-black uppercase tracking-widest text-red-500">Error</span>
        </div>
        <p class="text-[11px] text-red-400 font-medium tracking-tight">{{ session('error') }}</p>
    </div>
@endif

@if(session('warning'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
         class="mb-8 p-4 bg-amber-500/10 border border-amber-500/20 rounded-md animate-in fade-in slide-in-from-top-4 duration-300">
        <div class="flex items-center gap-2">
            <i class="ri-alert-line text-amber-500"></i>
            <span class="text-[11px] font-black uppercase tracking-widest text-amber-500">{{ session('warning') }}</span>
        </div>
    </div>
@endif

@if($errors->any())
    <div x-data="{ show: true }" x-show="show" class="mb-8 p-4 bg-red-500/10 border border-red-500/20 rounded-md animate-in fade-in slide-in-from-top-4 duration-300">
        <div class="flex items-center gap-2 mb-3">
            <i class="ri-error-warning-fill text-red-500"></i>
            <span class="text-[10px] font-black uppercase tracking-widest text-red-500">Koreksi Diperlukan</span>
        </div>
        <ul class="space-y-1">
            @foreach($errors->all() as $error)
                <li class="text-[11px] text-red-400 font-medium tracking-tight">— {{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

