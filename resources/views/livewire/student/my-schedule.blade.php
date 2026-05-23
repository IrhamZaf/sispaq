@push('vendor-style')
  @vite([
    'resources/assets/vendor/libs/fullcalendar/fullcalendar.scss',
    'resources/assets/vendor/libs/flatpickr/flatpickr.scss',
  ])
@endpush
@push('page-style')
  @vite('resources/assets/vendor/scss/pages/app-calendar.scss')
@endpush
@push('vendor-script')
  @vite([
    'resources/assets/vendor/libs/fullcalendar/fullcalendar.js',
    'resources/assets/vendor/libs/moment/moment.js',
    'resources/assets/vendor/libs/flatpickr/flatpickr.js',
  ])
@endpush
@push('page-script')
  <script>
    window.sispaqCalendarEvents = @json($calendarEvents);
    window.sispaqCourseColorMap = @json($courseColorMap);
  </script>
  @vite(['resources/js/sispaq-student-calendar.js'])
@endpush

<div>
  @include('livewire.student.partials.calendar-app', [
    'calendarId' => 'calendar',
    'sidebarId' => 'app-calendar-sidebar',
    'courses' => $courses,
    'courseColorMap' => $courseColorMap,
  ])

  <div class="card mt-4 d-lg-none">
    <div class="card-header"><h6 class="mb-0">Senarai Jadual</h6></div>
    <div class="table-responsive">
      <table class="table table-hover mb-0">
        <thead class="table-light"><tr><th>Kursus</th><th>Masa</th><th>Lokasi</th></tr></thead>
        <tbody>
          @forelse($schedules as $s)
            <tr data-course-id="{{ $s->course_id }}" class="sispaq-schedule-row">
              <td>{{ $s->course->code }} M{{ $s->module_number }}</td>
              <td>{{ date('d/m/Y H:i', strtotime($s->schedule_datetime)) }}</td>
              <td>{{ $s->location }}</td>
            </tr>
          @empty
            <tr><td colspan="3" class="text-center text-body-secondary py-4">Tiada jadual.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
