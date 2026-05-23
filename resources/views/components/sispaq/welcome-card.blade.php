@props([
    'title',
    'subtitle',
    'badge' => null,
    'variant' => 'primary',
])

@php
  $bgClass = match ($variant) {
    'success' => 'bg-success',
    'teacher' => 'bg-primary',
    'admin' => 'bg-dark',
    default => 'bg-primary',
  };
@endphp

<div class="col-12 mb-4">
  <div class="card {{ $bgClass }} text-white border-0">
    <div class="card-body p-4 p-md-5">
      <div class="row align-items-center">
        <div class="col-md-8">
          <h4 class="text-white fw-bold mb-2">{{ $title }}</h4>
          <p class="text-white mb-3 opacity-75">{{ $subtitle }}</p>
          @if($badge)
            <span class="badge bg-white text-primary fw-semibold px-3 py-2">{{ $badge }}</span>
          @endif
          @if(isset($actions))
            <div class="mt-3 d-flex flex-wrap gap-2">{{ $actions }}</div>
          @endif
        </div>
        <div class="col-md-4 d-none d-md-flex justify-content-end">
          <span class="avatar avatar-xl">
            <span class="avatar-initial rounded-circle bg-white bg-opacity-25">
              <i class="ti tabler-school ti-xl text-white"></i>
            </span>
          </span>
        </div>
      </div>
    </div>
  </div>
</div>
