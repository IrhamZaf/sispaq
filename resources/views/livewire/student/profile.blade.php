@push('page-style')
  @vite(['resources/assets/vendor/scss/pages/page-profile.scss', 'resources/assets/vendor/scss/pages/page-user-view.scss'])
@endpush

<div>
  {{-- Vuexy User Profile header --}}
  <div class="row">
    <div class="col-12">
      <div class="card mb-6">
        <div class="user-profile-header-banner">
          <div class="rounded-top bg-label-primary" style="min-height: 120px;"></div>
        </div>
        <div class="user-profile-header d-flex flex-column flex-lg-row text-sm-start text-center mb-5">
          <div class="flex-shrink-0 mt-n2 mx-sm-0 mx-auto">
            <span class="avatar avatar-xl d-block ms-0 ms-sm-6">
              <span class="avatar-initial rounded-circle bg-label-primary fs-2">
                {{ strtoupper(substr($user->name, 0, 2)) }}
              </span>
            </span>
          </div>
          <div class="flex-grow-1 mt-3 mt-lg-5">
            <div class="d-flex align-items-md-end align-items-sm-start align-items-center justify-content-md-between justify-content-start mx-5 flex-md-row flex-column gap-4">
              <div class="user-profile-info">
                <h4 class="mb-2 mt-lg-6">{{ $user->name }}</h4>
                <ul class="list-inline mb-0 d-flex align-items-center flex-wrap justify-content-sm-start justify-content-center gap-4 my-2">
                  <li class="list-inline-item d-flex gap-2 align-items-center">
                    <i class="icon-base ti tabler-school icon-lg"></i>
                    <span class="fw-medium">Pelajar SISPAQ</span>
                  </li>
                  <li class="list-inline-item d-flex gap-2 align-items-center">
                    <i class="icon-base ti tabler-id icon-lg"></i>
                    <span class="fw-medium">{{ $user->ic_number ?? '—' }}</span>
                  </li>
                  <li class="list-inline-item d-flex gap-2 align-items-center">
                    <i class="icon-base ti tabler-calendar icon-lg"></i>
                    <span class="fw-medium">Daftar {{ $user->created_at?->format('M Y') }}</span>
                  </li>
                </ul>
              </div>
              <span class="badge bg-label-success mb-1">Aktif</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-12">
      <div class="nav-align-top">
        <ul class="nav nav-pills flex-column flex-sm-row mb-6 gap-sm-0 gap-2">
          <li class="nav-item">
            <button type="button" class="nav-link {{ $tab === 'profil' ? 'active' : '' }}" wire:click="setTab('profil')">
              <i class="icon-base ti tabler-user-check icon-sm me-1_5"></i> {{ __('sispaq.nav.profile') }}
            </button>
          </li>
          <li class="nav-item">
            <button type="button" class="nav-link {{ $tab === 'bayaran' ? 'active' : '' }}" wire:click="setTab('bayaran')">
              <i class="icon-base ti tabler-credit-card icon-sm me-1_5"></i> {{ __('sispaq.nav.payments') }}
              @if($pendingCount > 0)
                <span class="badge rounded-pill bg-danger ms-1">{{ $pendingCount }}</span>
              @endif
            </button>
          </li>
        </ul>
      </div>
    </div>
  </div>

  @if($tab === 'profil')
    <div class="row">
      <div class="col-xl-4 col-lg-5 col-md-5">
        <div class="card mb-6">
          <div class="card-body">
            <p class="card-text text-uppercase text-body-secondary small mb-0">Perihal</p>
            <ul class="list-unstyled my-3 py-1">
              <li class="d-flex align-items-center mb-4">
                <i class="icon-base ti tabler-user icon-lg"></i>
                <span class="fw-medium mx-2">Nama:</span>
                <span>{{ $user->name }}</span>
              </li>
              <li class="d-flex align-items-center mb-4">
                <i class="icon-base ti tabler-check icon-lg"></i>
                <span class="fw-medium mx-2">Status:</span>
                <span>Aktif</span>
              </li>
              <li class="d-flex align-items-center mb-4">
                <i class="icon-base ti tabler-crown icon-lg"></i>
                <span class="fw-medium mx-2">Peranan:</span>
                <span>Pelajar</span>
              </li>
            </ul>
            <p class="card-text text-uppercase text-body-secondary small mb-0">Hubungan</p>
            <ul class="list-unstyled my-3 py-1">
              <li class="d-flex align-items-center mb-4">
                <i class="icon-base ti tabler-phone-call icon-lg"></i>
                <span class="fw-medium mx-2">Telefon:</span>
                <span>{{ $user->phone ?? '—' }}</span>
              </li>
              <li class="d-flex align-items-center mb-4">
                <i class="icon-base ti tabler-mail icon-lg"></i>
                <span class="fw-medium mx-2">E-mel:</span>
                <span>{{ $user->email }}</span>
              </li>
              <li class="d-flex align-items-center mb-2">
                <i class="icon-base ti tabler-map-pin icon-lg"></i>
                <span class="fw-medium mx-2">Alamat:</span>
                <span>{{ $user->address ?? '—' }}</span>
              </li>
            </ul>
          </div>
        </div>

        <div class="card mb-6">
          <div class="card-body">
            <p class="card-text text-uppercase text-body-secondary small">Ringkasan</p>
            <ul class="list-unstyled mb-0">
              <li class="d-flex align-items-center mb-4">
                <i class="icon-base ti tabler-book-2 icon-lg"></i>
                <span class="fw-medium mx-2">Kursus Aktif:</span>
                <span>{{ $activeCoursesCount }}</span>
              </li>
              <li class="d-flex align-items-center mb-4">
                <i class="icon-base ti tabler-credit-card icon-lg"></i>
                <span class="fw-medium mx-2">Bayaran Tertunggak:</span>
                <span>{{ $pendingPaymentsCount }}</span>
              </li>
            </ul>
            @if($pendingPaymentsCount > 0)
              <button type="button" wire:click="setTab('bayaran')" class="btn btn-sm btn-label-danger w-100 mt-2">
                <i class="ti tabler-credit-card me-1"></i> Lihat bayaran
              </button>
            @endif
          </div>
        </div>
      </div>

      <div class="col-xl-8 col-lg-7 col-md-7">
        <div class="card mb-6">
          <div class="card-header">
            <h5 class="mb-0">Tetapan Akaun</h5>
            <small class="text-body-secondary">Maklumat asas (paparan sahaja)</small>
          </div>
          <div class="card-body pt-4">
            <div class="row gy-4 gx-6">
              <div class="col-md-6">
                <label class="form-label">Nama Penuh</label>
                <input type="text" class="form-control" value="{{ $user->name }}" readonly />
              </div>
              <div class="col-md-6">
                <label class="form-label">E-mel</label>
                <input type="email" class="form-control" value="{{ $user->email }}" readonly />
              </div>
              <div class="col-md-6">
                <label class="form-label">No. IC</label>
                <input type="text" class="form-control" value="{{ $user->ic_number ?? '' }}" readonly />
              </div>
              <div class="col-md-6">
                <label class="form-label">Telefon</label>
                <input type="text" class="form-control" value="{{ $user->phone ?? '' }}" readonly />
              </div>
              <div class="col-12">
                <label class="form-label">Alamat</label>
                <textarea class="form-control" rows="2" readonly>{{ $user->address ?? '' }}</textarea>
              </div>
            </div>
            <p class="text-body-secondary small mt-4 mb-0">
              Untuk kemaskini maklumat, sila hubungi pejabat APIUM.
            </p>
          </div>
        </div>
      </div>
    </div>
  @else
    <div class="row">
      <div class="col-xl-4 col-lg-5 order-1 order-lg-0 mb-4">
        <div class="card mb-4">
          <div class="card-body text-center">
            <div class="d-flex justify-content-around flex-wrap gap-3 my-2">
              <div>
                <h4 class="mb-0 text-danger">{{ $pendingCount }}</h4>
                <small class="text-body-secondary">Tertunggak</small>
              </div>
              <div>
                <h4 class="mb-0 text-success">RM{{ number_format($paidTotal, 0) }}</h4>
                <small class="text-body-secondary">Jumlah bayar</small>
              </div>
            </div>
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

      <div class="col-xl-8 col-lg-7 order-0 order-lg-1">
        <div class="card mb-4">
          <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
            <h5 class="mb-0">Invois Yuran</h5>
            <select wire:model.live="paymentFilter" class="form-select form-select-sm w-auto">
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

        <div class="card">
          <div class="card-header">
            <h5 class="mb-0">Kaedah Bayaran (Demo)</h5>
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
  @endif
</div>
