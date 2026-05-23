<div class="row">
  <x-sispaq.welcome-card
    title="Selamat Kembali, Urusetia SISPAQ!"
    subtitle="Panel Pentadbir Utama — aliran permohonan hingga pensijilan"
    badge="Hak Akses: PENTADBIR"
    variant="admin"
  />

  @if (session()->has('success'))
    <div class="col-12 mb-4">
      <div class="alert alert-success alert-dismissible">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    </div>
  @endif

  <div class="col-sm-6 col-lg-3 mb-4">
    <x-sispaq.stat-card :value="$pendingApplicationsCount" label="Permohonan Baru" icon="tabler-users" color="warning" :href="route('admin.applications')" />
  </div>
  <div class="col-sm-6 col-lg-3 mb-4">
    <x-sispaq.stat-card :value="$scheduledTestsCount" label="Ujian Penempatan" icon="tabler-microphone" color="info" :href="route('admin.placement-tests')" />
  </div>
  <div class="col-sm-6 col-lg-3 mb-4">
    <x-sispaq.stat-card :value="$pendingPaymentsCount" label="Inbois Belum Bayar" icon="tabler-credit-card" color="danger" :href="route('admin.payments')" />
  </div>
  <div class="col-sm-6 col-lg-3 mb-4">
    <x-sispaq.stat-card :value="$totalStudents" label="Pelajar Berdaftar" icon="tabler-school" color="success" :href="route('admin.enrollments')" />
  </div>

  <div class="col-lg-7 mb-4">
    <x-sispaq.data-card title="Tugasan Menunggu Tindakan" icon="tabler-clipboard-list" action-label="Lihat Semua" :action-href="route('admin.applications')" padding="none" class="h-100">
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light">
            <tr><th>Pemohon</th><th>Program</th><th class="text-end">Tindakan</th></tr>
          </thead>
          <tbody>
            @forelse($pendingApplications as $app)
              <tr>
                <td>
                  <div class="d-flex align-items-center gap-3">
                    <x-sispaq.user-avatar :name="$app->user->name" size="sm" />
                    <div>
                      <strong class="d-block">{{ $app->user->name }}</strong>
                      <small class="text-body-secondary">IC: {{ $app->user->ic_number }}</small>
                    </div>
                  </div>
                </td>
                <td>
                  <span class="badge bg-label-primary mb-1">{{ $app->course->code }}</span>
                  <span class="d-block small">{{ $app->course->name }}</span>
                </td>
                <td class="text-end">
                  @if(in_array($app->course->code, ['Pra KuTAB', 'KuTAB']))
                    <a href="{{ route('admin.placement-tests') }}" class="btn btn-sm btn-label-primary">Ujian</a>
                  @else
                    <button wire:click="approveDirect({{ $app->id }})" class="btn btn-sm btn-success">Beri Tawaran</button>
                  @endif
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="3" class="text-center py-5 text-body-secondary">
                  <i class="ti tabler-circle-check ti-xl d-block mb-2 opacity-50"></i>
                  Tiada permohonan menunggu semakan.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </x-sispaq.data-card>
  </div>

  <div class="col-lg-5 mb-4">
    <x-sispaq.data-card title="Ujian Penempatan Dijadualkan" icon="tabler-microphone" action-label="Urus" :action-href="route('admin.placement-tests')" class="h-100">
      @forelse($upcomingPlacementTests as $test)
        <div class="sispaq-timeline-item">
          <x-sispaq.user-avatar :name="$test->student->name" size="sm" color="info" />
          <div class="sispaq-timeline-meta">
            <strong class="d-block">{{ $test->student->name }}</strong>
            <small class="text-body-secondary d-block">{{ $test->application->course->code }} · {{ $test->examiner?->name }}</small>
            <span class="badge bg-label-info mt-1">{{ date('d/m/Y H:i', strtotime($test->test_date_time)) }}</span>
          </div>
        </div>
      @empty
        <x-sispaq.empty-state icon="tabler-microphone-off" message="Tiada ujian dijadualkan." />
      @endforelse
    </x-sispaq.data-card>
  </div>
</div>
