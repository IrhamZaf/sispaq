@props([
    'calendarId' => 'calendar',
    'sidebarId' => 'app-calendar-sidebar',
    'courses',
    'courseColorMap' => [],
])

@php use Illuminate\Support\Str; @endphp

<div
  class="card app-calendar-wrapper sispaq-calendar-root"
  data-calendar-el="{{ $calendarId }}"
  data-sidebar-id="{{ $sidebarId }}"
>
  <div class="row g-0">
    <div class="col app-calendar-sidebar border-end" id="{{ $sidebarId }}">
      <div class="px-3 pt-2">
        <div class="inline-calendar"></div>
      </div>
      <hr class="mb-6 mx-n4 mt-3" />
      <div class="px-6 pb-2">
        <div>
          <h5>Tapis Kelas</h5>
        </div>

        <div class="form-check form-check-secondary mb-5 ms-2">
          <input class="form-check-input select-all" type="checkbox" id="selectAll-{{ $sidebarId }}" checked />
          <label class="form-check-label" for="selectAll-{{ $sidebarId }}">Lihat Semua</label>
        </div>

        <div class="app-calendar-events-filter text-heading">
          @forelse($courses as $course)
            @php $color = $courseColorMap[$course->id] ?? 'primary'; @endphp
            <div class="form-check form-check-{{ $color }} mb-5 ms-2">
              <input
                class="form-check-input input-filter"
                type="checkbox"
                id="filter-{{ $sidebarId }}-{{ $course->id }}"
                data-value="{{ $course->id }}"
                checked
              />
              <label class="form-check-label" for="filter-{{ $sidebarId }}-{{ $course->id }}">
                {{ $course->code }}
                <span class="d-block small text-body-secondary fw-normal">{{ Str::limit($course->name, 36) }}</span>
              </label>
            </div>
          @empty
            <p class="small text-body-secondary ms-2">Tiada kursus aktif.</p>
          @endforelse
        </div>
      </div>
    </div>

    <div class="col app-calendar-content">
      <div class="card shadow-none border-0">
        <div class="card-body pb-0">
          <div wire:ignore id="{{ $calendarId }}"></div>
        </div>
      </div>
      <div class="app-overlay"></div>
    </div>
  </div>
</div>
