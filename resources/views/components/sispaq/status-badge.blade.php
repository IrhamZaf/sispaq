@props(['status', 'type' => 'application'])

@php
  $classMap = match ($type) {
    'application' => [
      'pending' => 'bg-label-warning',
      'under_review' => 'bg-label-info',
      'test_scheduled' => 'bg-label-primary',
      'offered' => 'bg-label-success',
      'approved' => 'bg-label-success',
      'rejected' => 'bg-label-danger',
    ],
    'payment' => [
      'pending' => 'bg-label-warning',
      'paid' => 'bg-label-success',
      'failed' => 'bg-label-danger',
    ],
    'enrollment' => [
      'active' => 'bg-label-success',
      'completed' => 'bg-label-secondary',
      'dropped' => 'bg-label-danger',
    ],
    default => [],
  };

  $labelKey = "sispaq.status.{$type}.{$status}";
  $label = __($labelKey);
  if ($label === $labelKey) {
      $label = ucfirst(str_replace('_', ' ', $status));
  }
  $class = $classMap[$type][$status] ?? 'bg-label-secondary';
@endphp

<span class="badge {{ $class }}">{{ $label }}</span>
