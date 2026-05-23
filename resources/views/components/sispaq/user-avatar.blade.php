@props(['name', 'size' => 'md', 'color' => 'primary'])

@php
  $sizeClass = match($size) {
    'sm' => 'avatar-sm',
    'lg' => 'avatar-lg',
    default => '',
  };
  $initials = collect(explode(' ', trim($name)))->take(2)->map(fn ($w) => mb_strtoupper(mb_substr($w, 0, 1)))->join('');
@endphp

<div class="avatar {{ $sizeClass }} {{ $attributes->get('class') }}">
  <span class="avatar-initial rounded-circle bg-label-{{ $color }} fw-semibold">{{ $initials }}</span>
</div>
