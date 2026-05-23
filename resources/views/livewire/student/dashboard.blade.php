@php use Illuminate\Support\Str; @endphp

@push('vendor-style')
  @vite([
    'resources/assets/vendor/libs/apex-charts/apex-charts.scss',
    'resources/assets/vendor/libs/fullcalendar/fullcalendar.scss',
    'resources/assets/vendor/libs/flatpickr/flatpickr.scss',
  ])
@endpush
@push('page-style')
  @vite([
    'resources/assets/vendor/scss/pages/app-calendar.scss',
  ])
@endpush
@push('vendor-script')
  @vite([
    'resources/assets/vendor/libs/apex-charts/apexcharts.js',
    'resources/assets/vendor/libs/fullcalendar/fullcalendar.js',
    'resources/assets/vendor/libs/moment/moment.js',
    'resources/assets/vendor/libs/flatpickr/flatpickr.js',
  ])
@endpush
@push('page-script')
  <script>
    window.sispaqDashboard = @json($chartConfig);
    window.sispaqCalendarEvents = @json($calendarEvents);
    window.sispaqCourseColorMap = @json($courseColorMap);
  </script>
  @vite([
    'resources/js/sispaq-student-dashboard.js',
    'resources/js/sispaq-student-calendar.js',
  ])
@endpush

<div>
  @if (session()->has('success'))
    <div class="alert alert-success alert-dismissible mb-4">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
  @endif

  {{-- Statistics (Vuexy cards — statistics demo style) --}}
  <div class="row g-4 mb-4">
    <div class="col-sm-6 col-xl-3">
      <x-sispaq.stat-card :value="$activeCoursesCount" label="Kursus Aktif" icon="tabler-book-2" color="primary" :href="route('student.courses', ['tab' => 'mine'])" />
    </div>
    <div class="col-sm-6 col-xl-3">
      <x-sispaq.stat-card :value="$attendanceRate . '%'" label="Kehadiran" icon="tabler-user-check" color="info" :href="route('student.attendance')" />
    </div>
    <div class="col-sm-6 col-xl-3">
      <x-sispaq.stat-card :value="$completedModulesCount" label="Modul Lulus" icon="tabler-circle-check" color="success" :href="route('student.academic')" />
    </div>
    <div class="col-sm-6 col-xl-3">
      <x-sispaq.stat-card :value="$pendingPaymentsCount" label="Bayaran Tertunggak" icon="tabler-credit-card" color="danger" :href="route('student.profile', ['tab' => 'bayaran'])" />
    </div>
  </div>

  {{-- Course progress --}}
  <div class="row g-4 mb-4">
    <div class="col-12">
      <div class="card h-100">
        <div class="card-header pb-0 d-flex justify-content-between align-items-start">
          <div>
            <h5 class="mb-1">Kemajuan Kursus</h5>
            <p class="card-subtitle mb-0 text-body-secondary small">Peratus modul mengikut kursus berdaftar</p>
          </div>
          <a href="{{ route('student.courses', ['tab' => 'mine']) }}" class="btn btn-sm btn-label-primary">Semua</a>
        </div>
        <div class="card-body">
          <div class="row align-items-center g-3">
            <div class="col-md-7">
              <div wire:ignore id="horizontalBarChart" style="min-height: 220px"></div>
            </div>
            <div class="col-md-5">
              <div class="border rounded p-3">
                @forelse($courseProgressItems as $item)
                  <div class="d-flex gap-2 align-items-center mb-3">
                    <div class="badge rounded bg-label-{{ $item['color'] }} p-1"><i class="ti tabler-book icon-sm"></i></div>
                    <div class="flex-grow-1">
                      <h6 class="mb-0 fw-normal small">{{ $item['code'] }}</h6>
                      <div class="progress mt-1" style="height: 4px">
                        <div class="progress-bar bg-{{ $item['color'] }}" style="width: {{ $item['pct'] }}%"></div>
                      </div>
                    </div>
                    <span class="fw-medium small">{{ $item['pct'] }}%</span>
                  </div>
                @empty
                  <p class="small text-body-secondary mb-0">Belum ada kursus berdaftar.</p>
                @endforelse
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- Calendar (same Vuexy layout as schedule page) --}}
  <div class="row g-4 mb-4">
    <div class="col-12">
      <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2">
        <h5 class="mb-0">Kalendar Kelas</h5>
        <a href="{{ route('student.schedule') }}" class="btn btn-sm btn-label-primary">
          <i class="ti tabler-calendar me-1"></i> Halaman Jadual
        </a>
      </div>
      @include('livewire.student.partials.calendar-app', [
        'calendarId' => 'dashboardCalendar',
        'sidebarId' => 'dashboard-calendar-sidebar',
        'courses' => $courses,
        'courseColorMap' => $courseColorMap,
      ])
    </div>
  </div>

  {{-- Upcoming + My courses --}}
  <div class="row g-4">
    <div class="col-lg-5">
      <div class="card h-100">
        <div class="card-header d-flex justify-content-between align-items-center">
          <div>
            <h5 class="mb-1">Kelas Akan Datang</h5>
            <p class="card-subtitle mb-0 text-body-secondary small">5 sesi terdekat</p>
          </div>
          <a href="{{ route('student.schedule') }}" class="btn btn-sm btn-label-secondary">Jadual</a>
        </div>
        <div class="card-body pt-2">
          @forelse($upcomingSchedules as $sched)
            <div class="d-flex gap-3 align-items-center mb-4 pb-3 border-bottom">
              <div class="badge rounded bg-label-primary p-2">
                <i class="ti tabler-calendar-event"></i>
              </div>
              <div class="flex-grow-1">
                <h6 class="mb-0">{{ $sched->course->code }} · Modul {{ $sched->module_number }}</h6>
                <small class="text-body-secondary">{{ date('d/m/Y H:i', strtotime($sched->schedule_datetime)) }} · {{ Str::limit($sched->location, 28) }}</small>
              </div>
            </div>
          @empty
            <p class="text-body-secondary small mb-0">Tiada kelas dijadualkan.</p>
          @endforelse
        </div>
      </div>
    </div>

    <div class="col-lg-7">
      <div class="card h-100">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="mb-0">Kursus Saya</h5>
          <a href="{{ route('student.courses', ['tab' => 'explore']) }}" class="btn btn-sm btn-label-primary">+ Terokai</a>
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
              <thead class="table-light">
                <tr>
                  <th>Kursus</th>
                  <th>Modul</th>
                  <th class="w-25">Kemajuan</th>
                  <th class="text-end">Tindakan</th>
                </tr>
              </thead>
              <tbody>
                @forelse($enrollments->take(5) as $enroll)
                  @php $pct = $this->moduleProgress($enroll); @endphp
                  <tr>
                    <td>
                      <span class="badge bg-label-primary me-1">{{ $enroll->course->code }}</span>
                      <span class="fw-medium small">{{ Str::limit($enroll->course->name, 24) }}</span>
                    </td>
                    <td class="small">{{ $enroll->current_module }}/{{ $enroll->course->total_modules }}</td>
                    <td>
                      <div class="d-flex align-items-center gap-2">
                        <div class="progress flex-grow-1" style="height: 6px">
                          <div class="progress-bar" style="width: {{ $pct }}%"></div>
                        </div>
                        <small>{{ $pct }}%</small>
                      </div>
                    </td>
                    <td class="text-end">
                      <a href="{{ route('student.course.detail', $enroll->id) }}" class="btn btn-sm btn-label-primary">Butiran</a>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="4" class="text-center py-5">
                      <x-sispaq.empty-state icon="tabler-book-off" message="Tiada pendaftaran." action-label="Terokai" :action-href="route('student.courses', ['tab' => 'explore'])" />
                    </td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
