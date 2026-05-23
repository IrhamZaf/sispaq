@props(['course', 'showName' => true])

<span class="badge bg-label-primary mb-1">{{ $course->code }}</span>
@if($showName)
  <strong class="d-block small text-dark">{{ $course->name }}</strong>
@endif
