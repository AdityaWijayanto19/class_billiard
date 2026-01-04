{{-- Standard Page Header untuk Admin Pages --}}
@props([
    'title' => '',
    'subtitle' => '',
    'showBackButton' => true,
    'backRoute' => 'admin.dashboard',
    'backText' => 'Back to Dashboard',
    'actionButton' => null, // Optional: HTML string untuk action button
])

<!-- HEADER STANDARD -->
<div class="flex flex-col md:flex-row md:items-center justify-between gap-6 border-b border-slate-200 dark:border-white/5 pb-8 mb-10">
    <div class="space-y-1">
        @if($showBackButton)
        <a href="{{ route($backRoute) }}" 
           class="group flex items-center gap-2 text-[10px] font-black uppercase tracking-widest text-slate-400 transition-all duration-300 mb-2"
           @mouseenter="$el.style.color = 'var(--primary-color)'"
           @mouseleave="$el.style.color = ''">
            <i class="ri-arrow-left-line transition-transform group-hover:-translate-x-1"></i> {{ $backText }}
        </a>
        @endif
        <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white uppercase">{!! $title !!}</h1>
        @if($subtitle)
        <p class="text-xs text-slate-500 dark:text-gray-500 font-medium">{{ $subtitle }}</p>
        @endif
    </div>
    
    @if($actionButton)
    <div>
        {!! $actionButton !!}
    </div>
    @endif
</div>

