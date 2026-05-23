<div class="row">
  <x-sispaq.welcome-card
    :title="'Selamat Kembali, ' . auth()->user()->name . '!'"
    subtitle="Portal Pensyarah & Guru — jadual, kehadiran, markah & talaqqi"
    badge="PENSYARAH / GURU"
    variant="teacher"
  />

  @if (session()->has('success'))
    <div class="col-12 mb-4"><div class="alert alert-success alert-dismissible">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div></div>
  @endif

  <div class="col-sm-6 col-lg-4 mb-4">
    <x-sispaq.stat-card :value="$pendingTestsCount" label="Ujian Penempatan" icon="tabler-microphone" color="warning" :href="route('teacher.placement-tests')" />
  </div>
  <div class="col-sm-6 col-lg-4 mb-4">
    <x-sispaq.stat-card :value="$upcomingBookings->count()" label="Sesi Talaqqi" icon="tabler-book" color="info" :href="route('teacher.talaqqi.session')" />
  </div>
  <div class="col-sm-6 col-lg-4 mb-4">
    <x-sispaq.stat-card :value="$upcomingClasses->count()" label="Kelas Akan Datang" icon="tabler-calendar" color="primary" :href="route('teacher.schedules')" />
  </div>

  <div class="col-md-6 mb-4">
    <x-sispaq.data-card title="Talaqqi Akan Datang" icon="tabler-book" action-label="Sesi" :action-href="route('teacher.talaqqi.session')" class="h-100">
      @forelse($upcomingBookings as $b)
        <div class="sispaq-timeline-item">
          <x-sispaq.user-avatar :name="$b->student->name" size="sm" color="success" />
          <div class="sispaq-timeline-meta">
            <strong>{{ $b->student->name }}</strong>
            <small class="d-block text-body-secondary">{{ $b->enrollment->course->code }}</small>
            <span class="badge bg-label-primary mt-1">{{ date('d/m/Y h:i A', strtotime($b->booking_datetime)) }}</span>
          </div>
        </div>
      @empty
        <x-sispaq.empty-state message="Tiada sesi talaqqi dijadualkan." />
      @endforelse
    </x-sispaq.data-card>
  </div>

  <div class="col-md-6 mb-4">
    <x-sispaq.data-card title="Kelas Akan Datang" icon="tabler-calendar" action-label="Jadual" :action-href="route('teacher.schedules')" class="h-100">
      @forelse($upcomingClasses as $c)
        <div class="sispaq-timeline-item">
          <span class="avatar avatar-sm">
            <span class="avatar-initial rounded bg-label-primary"><i class="ti tabler-chalkboard"></i></span>
          </span>
          <div class="sispaq-timeline-meta">
            <strong>{{ $c->course->code }}</strong> <span class="text-body-secondary">Modul {{ $c->module_number }}</span>
            <small class="d-block text-body-secondary">{{ date('d/m/Y h:i A', strtotime($c->schedule_datetime)) }}</small>
            <small class="text-body-secondary">{{ $c->location }}</small>
          </div>
        </div>
      @empty
        <x-sispaq.empty-state message="Tiada kelas dijadualkan." />
      @endforelse
    </x-sispaq.data-card>
  </div>

  <div class="col-12">
    <div class="card">
      <div class="card-body d-flex flex-wrap gap-2">
        <a href="{{ route('teacher.schedules') }}" class="btn btn-primary btn-sm"><i class="ti tabler-calendar-plus me-1"></i> Jadual Kelas</a>
        <a href="{{ route('teacher.attendance') }}" class="btn btn-label-primary btn-sm"><i class="ti tabler-user-check me-1"></i> Kehadiran</a>
        <a href="{{ route('teacher.grades') }}" class="btn btn-label-primary btn-sm"><i class="ti tabler-license me-1"></i> Markah</a>
        <a href="{{ route('teacher.students') }}" class="btn btn-label-primary btn-sm"><i class="ti tabler-users me-1"></i> Pelajar</a>
      </div>
    </div>
  </div>
</div>
