@props([
    'icon' => true,
    'position' => 'left-5 top-1/2 -translate-y-1/2',
    'width' => 'w-52',
])

<div 
    x-data="{ 
        open: false, 
        locked: false, 
        hideTimeout: null,
        toggleLock() {
            this.locked = !this.locked;
            if (this.locked) {
                this.open = true;
            } else {
                this.open = false;
            }
        },
        handleMouseLeave() {
            if (!this.locked) {
                this.hideTimeout = setTimeout(() => this.open = false, 150);
            }
        }
    }" 
    class="relative inline-block"
>
    <button 
        @click="toggleLock()"
        @mouseenter="clearTimeout(hideTimeout); open = true"
        @mouseleave="handleMouseLeave()"
        type="button" 
        class="focus:outline-none transition"
        :class="locked ? 'text-yellow-500' : 'text-gray-400 hover:text-yellow-500'"
    >
        @if ($icon)
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" 
                 viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M13 16h-1v-4h-1m1-4h.01M12 2a10 10 0 100 20 10 10 0 000-20z" />
            </svg>
        @else
            {{ $triggerText ?? '?' }}
        @endif
    </button>

    <div 
        x-show="open"
        x-transition
        @mouseenter="clearTimeout(hideTimeout); open = true"
        @mouseleave="handleMouseLeave()"
        @click.away="locked = false; open = false"
        class="absolute {{ $position }} {{ $width }} text-xs bg-gray-800 text-white rounded-md px-3 py-2 shadow-lg z-10"
    >
        {{ $slot }}
        <div class="absolute -left-1 top-1/2 -translate-y-1/2 w-2 h-2 bg-gray-800 rotate-45"></div>
    </div>
</div>