@props(['class' => '', 'dusk' => null])

<button {{ $attributes->merge(['type' => 'submit']) }} class="{{ $class }} btn-primary text-black" style="background-color: var(--primary-color);" {{ $dusk ? "data-dusk=\"$dusk\"" : '' }}>
    {{ $slot }}
</button>
