@push('page-style')
  @vite('resources/assets/vendor/scss/pages/page-user-view.scss')
@endpush

<div class="row">
  {{-- Sidebar (Vuexy User View Billing) --}}
  <div class="col-xl-4 col-lg-5 order-1 order-lg-0 mb-4">
    <div class="card mb-4">
      <div class="card-body pt-8 text-center">
        <div class="avatar avatar-xl mx-auto mb-3">
          <span class="avatar-initial rounded-circle bg-label-primary fs-2">{{ substr(auth()->user()->name, 0, 2) }}</span>
        </div>
        <h5 class="mb-1">{{ auth()->user()->name }}</h5>
        <span class="badge bg-label-primary">Pelajar SISPAQ</span>
        <div class="d-flex justify-content-around flex-wrap my-5 gap-3">
          <div>
            <h5 class="mb-0 text-danger">{{ $pendingCount }}</h5>
            <small>Tertunggak</small>
          </div>
          <div>
            <h5 class="mb-0 text-success">RM{{ number_format($paidTotal, 0) }}</h5>
            <small>Jumlah Bayar</small>
          </div>
        </div>
        <h6 class="pb-3 border-bottom text-start">Butiran</h6>
        <ul class="list-unstyled text-start small mb-0">
          <li class="mb-2"><span class="fw-medium">E-mel:</span> {{ auth()->user()->email }}</li>
          <li class="mb-2"><span class="fw-medium">IC:</span> {{ auth()->user()->ic_number ?? '—' }}</li>
          <li class="mb-0"><span class="fw-medium">Telefon:</span> {{ auth()->user()->phone ?? '—' }}</li>
        </ul>
      </div>
    </div>
    <div class="card border border-2 border-primary">
      <div class="card-body">
        <span class="badge bg-label-primary mb-2">Yuran Modul</span>
        <p class="small text-body-secondary mb-3">Bayaran mengikut modul kursus (simulasi FPX/kad).</p>
        @if($pendingCount > 0)
          <div class="alert alert-warning py-2 small mb-0">{{ $pendingCount }} invois menunggu pembayaran.</div>
        @else
          <div class="alert alert-success py-2 small mb-0">Tiada tunggakan semasa.</div>
        @endif
      </div>
    </div>
  </div>

  {{-- Billing content --}}
  <div class="col-xl-8 col-lg-7 order-0 order-lg-1">
    <div class="nav-align-top mb-4">
      <ul class="nav nav-pills flex-wrap gap-2">
        <li class="nav-item"><span class="nav-link active"><i class="ti tabler-credit-card me-1"></i> Bayaran & Invois</span></li>
        <li class="nav-item"><a class="nav-link" href="{{ route('student.profile') }}"><i class="ti tabler-user me-1"></i> Profil</a></li>
      </ul>
    </div>

    <div class="card mb-4">
      <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
        <h5 class="mb-0">Invois Yuran</h5>
        <select wire:model.live="filter" class="form-select form-select-sm w-auto">
          <option value="all">Semua</option>
          <option value="pending">Belum Bayar</option>
          <option value="paid">Lunas</option>
        </select>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
              <tr><th>Kursus</th><th>Modul</th><th>Jumlah</th><th>Status</th><th class="text-end">Tindakan</th></tr>
            </thead>
            <tbody>
              @forelse($payments as $p)
                <tr>
                  <td><span class="badge bg-label-primary">{{ $p->course->code }}</span></td>
                  <td>Modul {{ $p->module_number }}</td>
                  <td class="fw-semibold">RM{{ number_format($p->amount, 2) }}</td>
                  <td><x-sispaq.status-badge :status="$p->payment_status" type="payment" /></td>
                  <td class="text-end">
                    @if($p->payment_status === 'pending')
                      <a href="{{ route('student.pay', $p->id) }}" class="btn btn-sm btn-primary">Bayar</a>
                    @else
                      <small class="font-monospace text-body-secondary">{{ $p->receipt_number }}</small>
                    @endif
                  </td>
                </tr>
              @empty
                <tr><td colspan="5" class="text-center py-5 text-body-secondary">Tiada rekod.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <div class="card card-action">
      <div class="card-header">
        <h5 class="card-action-title mb-0">Kaedah Bayaran (Demo)</h5>
      </div>
      <div class="card-body">
        <div class="row g-3">
          <div class="col-md-6">
            <div class="border rounded p-4 h-100">
              <i class="ti tabler-building-bank text-primary ti-lg mb-2"></i>
              <h6>FPX / Perbankan</h6>
              <p class="small text-body-secondary mb-0">Pilih semasa proses bayaran invois.</p>
            </div>
          </div>
          <div class="col-md-6">
            <div class="border rounded p-4 h-100">
              <i class="ti tabler-credit-card text-primary ti-lg mb-2"></i>
              <h6>Kad Kredit / Debit</h6>
              <p class="small text-body-secondary mb-0">Simulasi pembayaran dalam sistem.</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
