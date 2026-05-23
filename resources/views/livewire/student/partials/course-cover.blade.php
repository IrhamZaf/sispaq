@props(['course', 'height' => '140px'])

@php
  $palette = [
    'KPAQ' => ['bg' => 'primary', 'icon' => 'tabler-book'],
    'KPAH' => ['bg' => 'info', 'icon' => 'tabler-book-2'],
    'KBA' => ['bg' => 'success', 'icon' => 'tabler-language'],
    'Pra KuTAB' => ['bg' => 'warning', 'icon' => 'tabler-microphone'],
    'KuTAB' => ['bg' => 'danger', 'icon' => 'tabler-bookmark'],
    'KPI' => ['bg' => 'secondary', 'icon' => 'tabler-heart'],
    'KIBLAT' => ['bg' => 'dark', 'icon' => 'tabler-compass'],
    'KTKT' => ['bg' => 'primary', 'icon' => 'tabler-books'],
  ];
  $p = $palette[$course->code] ?? ['bg' => 'primary', 'icon' => 'tabler-school'];
@endphp

<div class="rounded-2 text-center mb-0 bg-label-{{ $p['bg'] }} d-flex align-items-center justify-content-center" style="min-height: {{ $height }};">
  <div>
    <i class="ti {{ $p['icon'] }} display-4 text-{{ $p['bg'] }} mb-2"></i>
    <div class="badge bg-white text-{{ $p['bg'] }} fw-bold">{{ $course->code }}</div>
  </div>
</div>
