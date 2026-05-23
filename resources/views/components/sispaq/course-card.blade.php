@props([
    'course',
    'status' => null,
    'applyUrl' => null,
])

<div class="card h-100 card-hover-border-primary">
  <div class="card-img-top sispaq-course-card-cover d-flex align-items-center justify-content-center">
    <span class="badge bg-white text-primary fs-6 fw-bold px-3 py-2">{{ $course->code }}</span>
  </div>
  <div class="card-body">
    <h5 class="card-title mb-2">{{ $course->name }}</h5>
    <p class="card-text small text-body-secondary mb-3">{{ \Illuminate\Support\Str::limit($course->description ?? '', 100) }}</p>
    <ul class="list-unstyled small mb-3">
      <li class="d-flex justify-content-between py-1 border-bottom">
        <span class="text-body-secondary">Modul</span><strong>{{ $course->total_modules }}</strong>
      </li>
      <li class="d-flex justify-content-between py-1 border-bottom">
        <span class="text-body-secondary">Yuran</span>
        <strong>
          @if($course->code === 'KIBLAT')
            A: RM{{ number_format($course->kiblat_cat_a_fee, 0) }}
          @else
            RM{{ number_format($course->fee_per_module, 0) }}/modul
          @endif
        </strong>
      </li>
      <li class="d-flex justify-content-between py-1">
        <span class="text-body-secondary">Jadual</span>
        <strong class="text-end" style="max-width: 55%">{{ $course->schedule_day_time ?? 'Talaqqi' }}</strong>
      </li>
    </ul>
    @if($status === 'enrolled')
      <span class="badge bg-label-success w-100 py-2">Sudah Berdaftar</span>
    @elseif($status === 'applied')
      <span class="badge bg-label-warning w-100 py-2">Permohonan Aktif</span>
    @elseif($applyUrl)
      <a href="{{ $applyUrl }}" class="btn btn-primary w-100">
        <i class="ti tabler-edit me-1"></i> Mohon Sekarang
      </a>
    @endif
  </div>
</div>
