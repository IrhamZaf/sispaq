@props([
    'value',
    'label',
    'icon' => 'tabler-chart-bar',
    'color' => 'primary',
    'href' => null,
    'trend' => null,
    'trendLabel' => null,
])

@if($href)
  <a href="{{ $href }}" class="text-decoration-none text-body d-block h-100">
@endif
<div class="card h-100 {{ $href ? '' : 'h-100' }}">
  <div class="card-body py-4">
    <div class="d-flex align-items-center">
      <div class="badge rounded bg-label-{{ $color }} me-3 p-2 flex-shrink-0">
        <i class="icon-base ti {{ $icon }} icon-md"></i>
      </div>
      <div class="flex-grow-1 min-w-0">
        <h5 class="mb-0 text-heading">{{ $value }}</h5>
        <small class="text-body-secondary">{{ $label }}</small>
        @if($trend || $trendLabel)
          <p class="mb-0 mt-1 small">
            @if($trend)<span class="fw-medium">{{ $trend }}</span>@endif
            @if($trendLabel)<span class="text-body-secondary">{{ $trendLabel }}</span>@endif
          </p>
        @endif
      </div>
    </div>
  </div>
</div>
@if($href)
  </a>
@endif
