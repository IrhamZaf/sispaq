<div>
  <x-sispaq.page-header title="Kehadiran" subtitle="Peratus kehadiran mengikut kursus aktif" icon="tabler-user-check" :breadcrumb="[['label' => 'Portal Pelajar', 'url' => route('student.dashboard')], ['label' => 'Kehadiran']]" />

  <div class="row">
    @forelse($enrollments as $enroll)
      @php $rate = $this->getAttendanceRate($enroll->id); $threshold = $enroll->course->attendanceThreshold(); @endphp
      <div class="col-md-6 col-xl-4 mb-4">
        <div class="card card-border-shadow-{{ $rate >= $threshold ? 'success' : 'warning' }} h-100">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-start mb-3">
              <x-sispaq.course-chip :course="$enroll->course" :show-name="false" />
              <h3 class="mb-0 fw-bold {{ $rate < $threshold ? 'text-warning' : 'text-success' }}">{{ $rate }}%</h3>
            </div>
            <p class="small text-body-secondary mb-2">{{ $enroll->course->name }}</p>
            <div class="sispaq-progress-block mb-2">
              <div class="progress">
                <div class="progress-bar bg-{{ $rate >= $threshold ? 'success' : 'warning' }}" style="width: {{ min($rate, 100) }}%"></div>
              </div>
            </div>
            <small class="text-body-secondary">Ambang: {{ $threshold }}%</small>
            @if($rate < $threshold)
              <div class="alert alert-warning py-2 mt-3 mb-0 small">Amaran kehadiran rendah</div>
            @endif
          </div>
        </div>
      </div>
    @empty
      <div class="col-12"><x-sispaq.empty-state message="Tiada kursus aktif." /></div>
    @endforelse
  </div>
</div>
