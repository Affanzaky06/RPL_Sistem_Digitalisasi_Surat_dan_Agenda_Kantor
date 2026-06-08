@props(['active' => false, 'icon' => ''])

<a {{ $attributes }} class="nav-link {{ $active ? 'active bg-success text-white' : 'link-body-emphasis' }}"
    aria-current="{{ $active ? 'page' : false }}">
    <svg class="bi pe-none me-2" width="16" height="16" aria-hidden="true">
        <use xlink:href="#{{ $icon }}"></use>
    </svg>
    {{ $slot }}
</a>