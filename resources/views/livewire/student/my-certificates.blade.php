<div>
  <x-sispaq.page-header title="Sijil Saya" subtitle="Sijil yang telah dikeluarkan" icon="tabler-certificate" :breadcrumb="[['label' => 'Portal Pelajar', 'url' => route('student.dashboard')], ['label' => 'Sijil']]">
    <x-slot:actions>
      <a href="{{ route('verify.certificate', ['hash' => 'semak']) }}" class="btn btn-primary btn-sm"><i class="ti tabler-search me-1"></i> Semak Keaslian</a>
    </x-slot:actions>
  </x-sispaq.page-header>

  <div class="row">
    @forelse($certificates as $cert)
      <div class="col-md-6 col-xl-4 mb-4">
        <div class="card card-border-shadow-primary h-100">
          <div class="card-body text-center">
            <span class="avatar avatar-lg mb-3">
              <span class="avatar-initial rounded bg-label-primary"><i class="ti tabler-certificate ti-lg"></i></span>
            </span>
            <h6 class="fw-bold mb-1">{{ $cert->course->name }}</h6>
            <span class="badge bg-label-secondary mb-2">{{ strtoupper($cert->certificate_type) }}</span>
            <p class="small font-monospace text-body-secondary mb-2">{{ $cert->certificate_number }}</p>
            <p class="small text-body-secondary mb-3">{{ date('d M Y', strtotime($cert->issue_date)) }}</p>
            <a href="{{ route('verify.certificate', ['hash' => $cert->qr_code_hash]) }}" class="btn btn-sm btn-label-primary w-100" target="_blank">Sahkan QR</a>
          </div>
        </div>
      </div>
    @empty
      <div class="col-12"><x-sispaq.empty-state icon="tabler-certificate" message="Tiada sijil dikeluarkan lagi." /></div>
    @endforelse
  </div>
</div>
