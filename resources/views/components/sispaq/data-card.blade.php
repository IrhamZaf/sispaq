@props([
    'title',
    'icon' => null,
    'actionLabel' => null,
    'actionHref' => null,
    'padding' => 'default',
])

@php
  $bodyClass = match($padding) {
    'none' => 'p-0',
    'compact' => 'p-3',
    default => '',
  };
@endphp

<div {{ $attributes->merge(['class' => 'card h-100']) }}>
  <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
    <div class="card-title mb-0">
      @if($icon)
        <i class="ti {{ $icon }} text-primary me-2"></i>
      @endif
      <h5 class="mb-0 d-inline">{{ $title }}</h5>
    </div>
    @if($actionLabel && $actionHref)
      <a href="{{ $actionHref }}" class="btn btn-sm btn-label-primary">{{ $actionLabel }}</a>
    @elseif(isset($headerActions))
      {{ $headerActions }}
    @endif
  </div>
  <div class="card-body {{ $bodyClass }}">
    {{ $slot }}
  </div>
</div>
