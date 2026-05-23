@props([
    'icon' => 'tabler-inbox',
    'message' => 'Tiada rekod.',
    'actionLabel' => null,
    'actionHref' => null,
])

<div class="text-center py-5 text-muted">
  <i class="ti {{ $icon }} fs-1 d-block mb-3 opacity-50"></i>
  <p class="mb-3">{{ $message }}</p>
  @if($actionLabel && $actionHref)
    <a href="{{ $actionHref }}" class="btn btn-primary btn-sm">{{ $actionLabel }}</a>
  @endif
</div>
