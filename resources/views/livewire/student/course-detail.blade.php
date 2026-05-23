@push('page-style')
  @vite('resources/assets/vendor/scss/pages/app-academy-details.scss')
@endpush

@php
  $att = $this->attendanceSummary();
  $isTalaqqi = $enrollment->course->isTalaqqi();
  $jadualCount = $isTalaqqi ? $talaqqiBookings->count() : $schedules->count();
@endphp

<div>
  <nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb breadcrumb-style1 mb-0">
      <li class="breadcrumb-item"><a href="{{ route('student.dashboard') }}">Portal Pelajar</a></li>
      <li class="breadcrumb-item"><a href="{{ route('student.courses', ['tab' => 'mine']) }}">Kursus Saya</a></li>
      <li class="breadcrumb-item active">{{ $enrollment->course->code }}</li>
    </ol>
  </nav>

  {{-- Hero --}}
  <div class="card mb-4 overflow-hidden">
    <div class="card-body p-0">
      <div class="row g-0">
        <div class="col-md-4 col-lg-3 border-end">
          <div class="p-4 h-100 d-flex flex-column">
            @include('livewire.student.partials.course-cover', ['course' => $enrollment->course, 'height' => '160px'])
            <div class="mt-3">
              <span class="badge bg-label-primary">{{ $enrollment->course->code }}</span>
              <span class="ms-1"><x-sispaq.status-badge :status="$enrollment->status" type="enrollment" /></span>
            </div>
          </div>
        </div>
        <div class="col-md-8 col-lg-9">
          <div class="p-4 pb-3">
            <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
              <div>
                <h4 class="mb-1 text-heading">{{ $enrollment->course->name }}</h4>
                <p class="mb-0 text-body-secondary">
                  <i class="ti tabler-clock me-1"></i>{{ $enrollment->course->schedule_day_time ?? 'Jadual talaqqi' }}
                </p>
              </div>
              @if($isTalaqqi)
                <a href="{{ route('student.talaqqi.book') }}" class="btn btn-primary btn-sm">
                  <i class="ti tabler-calendar-plus me-1"></i> Tempah Talaqqi
                </a>
              @endif
            </div>
          </div>
          <div class="px-4 pb-4">
            <div class="row g-3">
              <div class="col-6 col-xl-3">
                <div class="d-flex align-items-center gap-2 p-3 rounded bg-label-primary">
                  <span class="avatar avatar-sm"><span class="avatar-initial rounded bg-primary"><i class="ti tabler-chart-dots"></i></span></span>
                  <div>
                    <h5 class="mb-0">{{ $this->moduleProgress() }}%</h5>
                    <small class="text-body-secondary">Kemajuan</small>
                  </div>
                </div>
              </div>
              <div class="col-6 col-xl-3">
                <div class="d-flex align-items-center gap-2 p-3 rounded bg-label-success">
                  <span class="avatar avatar-sm"><span class="avatar-initial rounded bg-success"><i class="ti tabler-circle-check"></i></span></span>
                  <div>
                    <h5 class="mb-0">{{ $this->passedModulesCount() }}/{{ $enrollment->course->total_modules }}</h5>
                    <small class="text-body-secondary">Modul lulus</small>
                  </div>
                </div>
              </div>
              <div class="col-6 col-xl-3">
                <div class="d-flex align-items-center gap-2 p-3 rounded bg-label-{{ $att['rate'] >= $att['threshold'] ? 'success' : 'warning' }}">
                  <span class="avatar avatar-sm"><span class="avatar-initial rounded bg-{{ $att['rate'] >= $att['threshold'] ? 'success' : 'warning' }}"><i class="ti tabler-user-check"></i></span></span>
                  <div>
                    <h5 class="mb-0">{{ $att['rate'] }}%</h5>
                    <small class="text-body-secondary">Kehadiran</small>
                  </div>
                </div>
              </div>
              <div class="col-6 col-xl-3">
                <div class="d-flex align-items-center gap-2 p-3 rounded bg-label-info">
                  <span class="avatar avatar-sm"><span class="avatar-initial rounded bg-info"><i class="ti tabler-school"></i></span></span>
                  <div>
                    <h5 class="mb-0">M{{ $enrollment->current_module }}</h5>
                    <small class="text-body-secondary">Modul semasa</small>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <ul class="nav nav-pills flex-wrap gap-2 mb-4">
        <li class="nav-item">
          <button type="button" class="nav-link {{ $section === 'jadual' ? 'active' : '' }}" wire:click="$set('section', 'jadual')">
            <i class="ti tabler-calendar me-1"></i> Jadual
            <span class="badge bg-white text-primary ms-1">{{ $jadualCount }}</span>
          </button>
        </li>
        <li class="nav-item">
          <button type="button" class="nav-link {{ $section === 'kehadiran' ? 'active' : '' }}" wire:click="$set('section', 'kehadiran')">
            <i class="ti tabler-user-check me-1"></i> Kehadiran
            <span class="badge bg-white text-primary ms-1">{{ $attendances->count() }}</span>
          </button>
        </li>
        <li class="nav-item">
          <button type="button" class="nav-link {{ $section === 'akademik' ? 'active' : '' }}" wire:click="$set('section', 'akademik')">
            <i class="ti tabler-school me-1"></i> Akademik
            <span class="badge bg-white text-primary ms-1">{{ $academicRecords->count() }}</span>
          </button>
        </li>
        @if($this->isCourseCompleted())
          <li class="nav-item">
            <button type="button" class="nav-link {{ $section === 'sijil' ? 'active' : '' }}" wire:click="$set('section', 'sijil')">
              <i class="ti tabler-certificate me-1"></i> {{ __('sispaq.course_detail.certificate') }}
              @if($certificate)
                <span class="badge bg-white text-primary ms-1"><i class="ti tabler-check"></i></span>
              @endif
            </button>
          </li>
        @endif
  </ul>

  <div class="row g-4 align-items-start">
    <div class="col-lg-8">
      @if($section === 'jadual')
        <div class="card">
          <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div>
              <h5 class="mb-0">{{ $isTalaqqi ? 'Jadual Sesi Talaqqi' : 'Jadual Kelas' }}</h5>
              <p class="mb-0 text-body-secondary small">{{ $jadualCount }} sesi direkod</p>
            </div>
            <a href="{{ route('student.schedule') }}" class="btn btn-sm btn-label-secondary">
              <i class="ti tabler-calendar me-1"></i> Kalendar penuh
            </a>
          </div>
          <div class="card-body p-0">
            @if($isTalaqqi)
              <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                  <thead class="table-light">
                    <tr><th>Tarikh</th><th>Guru</th><th>m/s</th><th>Status</th></tr>
                  </thead>
                  <tbody>
                    @forelse($talaqqiBookings as $b)
                      @php $isPast = strtotime($b->booking_datetime) < now()->timestamp; @endphp
                      <tr class="{{ $isPast ? 'text-body-secondary' : '' }}">
                        <td class="fw-medium">{{ date('d/m/Y H:i', strtotime($b->booking_datetime)) }}</td>
                        <td>{{ $b->teacher?->name ?? '—' }}</td>
                        <td>{{ $b->current_page }}</td>
                        <td><span class="badge bg-label-info">{{ $b->session_status }}</span></td>
                      </tr>
                    @empty
                      <tr><td colspan="4" class="text-center py-5 text-body-secondary">Tiada jadual sesi.</td></tr>
                    @endforelse
                  </tbody>
                </table>
              </div>
            @else
              <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                  <thead class="table-light">
                    <tr><th>Modul</th><th>Tarikh</th><th>Lokasi</th><th>Mod</th></tr>
                  </thead>
                  <tbody>
                    @forelse($schedules as $s)
                      @php $isPast = strtotime($s->schedule_datetime) < now()->timestamp; @endphp
                      <tr class="{{ $isPast ? 'text-body-secondary' : '' }}">
                        <td>
                          <span class="fw-medium">M{{ $s->module_number }}</span>
                          @if($s->module_number == $enrollment->current_module)
                            <span class="badge bg-label-primary ms-1">Semasa</span>
                          @endif
                        </td>
                        <td>{{ date('d/m/Y H:i', strtotime($s->schedule_datetime)) }}</td>
                        <td>{{ $s->location }}</td>
                        <td><span class="badge bg-label-{{ $s->is_hybrid ? 'info' : 'secondary' }}">{{ $s->is_hybrid ? 'Hibrid' : 'Fizikal' }}</span></td>
                      </tr>
                    @empty
                      <tr><td colspan="4" class="text-center py-5 text-body-secondary">Tiada jadual kelas.</td></tr>
                    @endforelse
                  </tbody>
                </table>
              </div>
            @endif
          </div>
        </div>
      @endif

      @if($section === 'kehadiran')
        <div class="row g-3 mb-4">
          <div class="col-sm-4">
            <div class="card card-border-shadow-primary h-100 mb-0">
              <div class="card-body">
                <p class="small text-body-secondary mb-1">Peratus kehadiran</p>
                <h3 class="mb-0 {{ $att['rate'] >= $att['threshold'] ? 'text-success' : 'text-warning' }}">{{ $att['rate'] }}%</h3>
                <small>Ambang {{ $att['threshold'] }}%</small>
              </div>
            </div>
          </div>
          <div class="col-sm-4">
            <div class="card card-border-shadow-success h-100 mb-0">
              <div class="card-body">
                <p class="small text-body-secondary mb-1">Hadir / bersebab</p>
                <h3 class="mb-0 text-success">{{ $att['present'] }}</h3>
                <small>daripada {{ $att['total'] }} rekod</small>
              </div>
            </div>
          </div>
          <div class="col-sm-4">
            <div class="card card-border-shadow-danger h-100 mb-0">
              <div class="card-body">
                <p class="small text-body-secondary mb-1">Tidak hadir</p>
                <h3 class="mb-0 text-danger">{{ $att['absent'] }}</h3>
                <small>rekod tidak lengkap</small>
              </div>
            </div>
          </div>
        </div>

        <div class="card">
          <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Senarai Kehadiran</h5>
            <a href="{{ route('student.attendance') }}" class="btn btn-sm btn-label-secondary">Semua kursus</a>
          </div>
          <div class="card-body p-0">
            <div class="table-responsive">
              <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                  <tr><th>Sesi</th><th>Tarikh</th><th>Status</th></tr>
                </thead>
                <tbody>
                  @forelse($attendances as $record)
                    <tr>
                      <td class="fw-medium">{{ $this->attendanceSessionLabel($record) }}</td>
                      <td>{{ $record->recorded_at ? date('d/m/Y H:i', strtotime($record->recorded_at)) : '—' }}</td>
                      <td>
                        <span class="badge bg-label-{{ $this->attendanceStatusClass($record->status) }}">
                          {{ $this->attendanceStatusLabel($record->status) }}
                        </span>
                      </td>
                    </tr>
                  @empty
                    <tr><td colspan="3" class="text-center py-5 text-body-secondary">Tiada rekod kehadiran.</td></tr>
                  @endforelse
                </tbody>
              </table>
            </div>
          </div>
        </div>
      @endif

      @if($section === 'akademik')
        <div class="card mb-4">
          <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Rekod Markah</h5>
            <a href="{{ route('student.academic') }}" class="btn btn-sm btn-label-secondary">Semua kursus</a>
          </div>
          <div class="card-body p-0">
            <div class="table-responsive">
              <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                  <tr><th>Modul</th><th>Markah</th><th>Status</th><th>Catatan</th></tr>
                </thead>
                <tbody>
                  @forelse($academicRecords as $r)
                    <tr>
                      <td>
                        <span class="fw-medium">M{{ $r->module_number }}</span>
                        @if($r->module_number == $enrollment->current_module)
                          <span class="badge bg-label-primary ms-1">Semasa</span>
                        @endif
                      </td>
                      <td><span class="fw-bold">{{ $r->marks }}</span><span class="text-body-secondary">/100</span></td>
                      <td><span class="badge bg-label-{{ $r->status === 'lulus' ? 'success' : 'danger' }}">{{ strtoupper($r->status) }}</span></td>
                      <td class="small text-body-secondary">
                        @if($r->module_number < $enrollment->current_module) Selesai
                        @elseif($r->module_number == $enrollment->current_module) Sedang dijalankan
                        @else Belum dimulakan @endif
                      </td>
                    </tr>
                  @empty
                    <tr><td colspan="4" class="text-center py-5 text-body-secondary">Tiada rekod markah.</td></tr>
                  @endforelse
                </tbody>
              </table>
            </div>
          </div>
        </div>

        @if($isTalaqqi && $talaqqiBookings->isNotEmpty())
          <div class="card">
            <div class="card-header">
              <h5 class="mb-0">Ringkasan Kemajuan Talaqqi</h5>
              <p class="mb-0 text-body-secondary small">Butiran penuh di tab Jadual</p>
            </div>
            <div class="card-body">
              @php $latest = $talaqqiBookings->first(); $maxPage = $talaqqiBookings->max('current_page') ?? 0; @endphp
              <div class="d-flex align-items-center gap-4 flex-wrap">
                <div>
                  <p class="small text-body-secondary mb-1">Halaman terkini</p>
                  <h3 class="mb-0">m/s {{ $maxPage }}</h3>
                </div>
                <div>
                  <p class="small text-body-secondary mb-1">Sesi terakhir</p>
                  <h6 class="mb-0">{{ date('d/m/Y', strtotime($latest->booking_datetime)) }}</h6>
                </div>
                <div>
                  <p class="small text-body-secondary mb-1">Status</p>
                  <span class="badge bg-label-info">{{ $latest->session_status }}</span>
                </div>
                <button type="button" wire:click="$set('section', 'jadual')" class="btn btn-sm btn-label-primary ms-auto">Lihat jadual</button>
              </div>
            </div>
          </div>
        @endif
      @endif

      @if($section === 'sijil' && $this->isCourseCompleted())
        <div class="card">
          <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div>
              <h5 class="mb-0">{{ __('sispaq.course_detail.certificate_title') }}</h5>
              <p class="mb-0 text-body-secondary small">{{ __('sispaq.course_detail.certificate_subtitle') }}</p>
            </div>
            <a href="{{ route('student.certificates') }}" class="btn btn-sm btn-label-secondary">
              <i class="ti tabler-certificate me-1"></i> {{ __('sispaq.course_detail.all_certificates') }}
            </a>
          </div>
          <div class="card-body">
            @if($certificate)
              <div class="row justify-content-center">
                <div class="col-md-8 col-lg-6">
                  <div class="card card-border-shadow-success h-100 mb-0">
                    <div class="card-body text-center">
                      <span class="avatar avatar-lg mb-3">
                        <span class="avatar-initial rounded bg-label-success"><i class="ti tabler-certificate ti-lg"></i></span>
                      </span>
                      <h6 class="fw-bold mb-1">{{ $enrollment->course->name }}</h6>
                      <span class="badge bg-label-success mb-3">{{ $this->certificateTypeLabel($certificate->certificate_type) }}</span>
                      <dl class="text-start small mb-4">
                        <dt class="text-body-secondary">{{ __('sispaq.course_detail.certificate_number') }}</dt>
                        <dd class="font-monospace fw-medium mb-2">{{ $certificate->certificate_number }}</dd>
                        <dt class="text-body-secondary">{{ __('sispaq.course_detail.certificate_issued') }}</dt>
                        <dd class="mb-0">{{ date('d M Y', strtotime($certificate->issue_date)) }}</dd>
                      </dl>
                      <a href="{{ route('verify.certificate', ['hash' => $certificate->qr_code_hash]) }}" class="btn btn-success w-100" target="_blank" rel="noopener">
                        <i class="ti tabler-search me-1"></i> {{ __('sispaq.course_detail.verify_qr') }}
                      </a>
                    </div>
                  </div>
                </div>
              </div>
            @else
              <div class="text-center py-4">
                <span class="avatar avatar-lg mb-3">
                  <span class="avatar-initial rounded bg-label-warning"><i class="ti tabler-clock ti-lg"></i></span>
                </span>
                <p class="text-body-secondary mb-0 mx-auto" style="max-width: 28rem;">{{ __('sispaq.course_detail.certificate_pending') }}</p>
              </div>
            @endif
          </div>
        </div>
      @endif
    </div>

    <div class="col-lg-4">
      @include('livewire.student.partials.course-module-progress', [
        'enrollment' => $enrollment,
        'academicRecords' => $academicRecords,
      ])

      @if($this->isCourseCompleted())
        <div class="card border border-success mb-4">
          <div class="card-body">
            <p class="small text-success mb-2 fw-medium"><i class="ti tabler-circle-check me-1"></i> {{ __('sispaq.status.enrollment.completed') }}</p>
            @if($certificate)
              <p class="small text-body-secondary mb-2">{{ $certificate->certificate_number }}</p>
              <button type="button" wire:click="$set('section', 'sijil')" class="btn btn-sm btn-success w-100">
                <i class="ti tabler-certificate me-1"></i> {{ __('sispaq.course_detail.certificate') }}
              </button>
            @else
              <p class="small text-body-secondary mb-0">{{ __('sispaq.course_detail.certificate_pending') }}</p>
            @endif
          </div>
        </div>
      @endif

      @if($this->nextSession)
        <div class="card border border-primary mb-0">
          <div class="card-body">
            <p class="small text-primary mb-2 fw-medium"><i class="ti tabler-bell me-1"></i> Seterusnya</p>
            @if($isTalaqqi)
              <h6 class="mb-1">Talaqqi · m/s {{ $this->nextSession->current_page }}</h6>
              <p class="small text-body-secondary mb-2">{{ $this->nextSession->teacher?->name ?? '—' }}</p>
              <p class="mb-0 fw-medium">{{ date('d/m/Y H:i', strtotime($this->nextSession->booking_datetime)) }}</p>
            @else
              <h6 class="mb-1">Kelas Modul {{ $this->nextSession->module_number }}</h6>
              <p class="small text-body-secondary mb-2">{{ $this->nextSession->location }}</p>
              <p class="mb-0 fw-medium">{{ date('d/m/Y H:i', strtotime($this->nextSession->schedule_datetime)) }}</p>
            @endif
          </div>
        </div>
      @endif
    </div>
  </div>
</div>
