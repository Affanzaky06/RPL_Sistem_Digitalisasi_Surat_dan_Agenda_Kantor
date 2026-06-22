@props(['active' => false, 'icon' => ''])

<a {{ $attributes }} class="nav-link {{ $active ? 'active bg-success text-white' : 'link-body-emphasis' }}"
    aria-current="{{ $active ? 'page' : false }}">
    @if($icon)
        <i class="bi bi-{{ $icon }} me-2"></i>
    @endif
    {{ $slot }}
</a>