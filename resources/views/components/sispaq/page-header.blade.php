@props([
    'title',
    'subtitle' => null,
    'icon' => null,
    'breadcrumb' => [],
])

<div class="row mb-4">
  <div class="col-12">
    @if(count($breadcrumb))
      <nav aria-label="breadcrumb" class="mb-2">
        <ol class="breadcrumb breadcrumb-style1 mb-0">
          @foreach($breadcrumb as $item)
            @if($loop->last)
              <li class="breadcrumb-item active">{{ $item['label'] }}</li>
            @else
              <li class="breadcrumb-item">
                <a href="{{ $item['url'] ?? '#' }}">{{ $item['label'] }}</a>
              </li>
            @endif
          @endforeach
        </ol>
      </nav>
    @endif
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
      <div>
        <h4 class="mb-1 fw-bold text-heading">
          @if($icon)<i class="ti {{ $icon }} text-primary me-2"></i>@endif{{ $title }}
        </h4>
        @if($subtitle)
          <p class="mb-0 text-body-secondary">{{ $subtitle }}</p>
        @endif
      </div>
      @if(isset($actions))
        <div class="d-flex flex-wrap gap-2">{{ $actions }}</div>
      @endif
    </div>
  </div>
</div>
