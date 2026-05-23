<div class="row">
  <div class="col-12">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Halaman Pentadbir</a></li>
        <li class="breadcrumb-item active" aria-current="page">Laporan &amp; Analitik</li>
      </ol>
    </nav>
  </div>

  <!-- Stats Grid -->
  <div class="col-sm-6 col-lg-3 mb-4">
    <div class="card border-0 shadow-sm">
      <div class="card-body d-flex align-items-center">
        <div class="badge rounded bg-label-primary p-3 me-3">
          <i class="ti ti-users fs-3"></i>
        </div>
        <div>
          <h4 class="fw-bold mb-0">{{ $totalStudents }}</h4>
          <span class="text-muted small">Jumlah Pelajar</span>
        </div>
      </div>
    </div>
  </div>

  <div class="col-sm-6 col-lg-3 mb-4">
    <div class="card border-0 shadow-sm">
      <div class="card-body d-flex align-items-center">
        <div class="badge rounded bg-label-warning p-3 me-3">
          <i class="ti ti-file-text fs-3"></i>
        </div>
        <div>
          <h4 class="fw-bold mb-0">{{ $totalApplications }}</h4>
          <span class="text-muted small">Permohonan Kemasukan</span>
        </div>
      </div>
    </div>
  </div>

  <div class="col-sm-6 col-lg-3 mb-4">
    <div class="card border-0 shadow-sm">
      <div class="card-body d-flex align-items-center">
        <div class="badge rounded bg-label-success p-3 me-3">
          <i class="ti ti-currency-dollar fs-3"></i>
        </div>
        <div>
          <h4 class="fw-bold mb-0">RM{{ number_format($totalRevenue) }}</h4>
          <span class="text-muted small">Jumlah Kutipan Yuran</span>
        </div>
      </div>
    </div>
  </div>

  <div class="col-sm-6 col-lg-3 mb-4">
    <div class="card border-0 shadow-sm">
      <div class="card-body d-flex align-items-center">
        <div class="badge rounded bg-label-info p-3 me-3">
          <i class="ti ti-award fs-3"></i>
        </div>
        <div>
          <h4 class="fw-bold mb-0">{{ $totalCertificates }}</h4>
          <span class="text-muted small">Sijil Dianugerahkan</span>
        </div>
      </div>
    </div>
  </div>

  <!-- Left: Course Performance & Revenue Table -->
  <div class="col-md-8 mb-4">
    <div class="card border-0 shadow-sm h-100">
      <div class="card-header border-bottom py-3">
        <h5 class="card-title mb-0 fw-bold"><i class="ti ti-chart-bar text-primary me-2"></i>Prestasi Pengajian Mengikut Program</h5>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
              <tr>
                <th>Kod</th>
                <th>Program Pengajian</th>
                <th class="text-center">Pelajar Aktif</th>
                <th class="text-center">Selesai (Alumni)</th>
                <th class="text-end">Kutipan Yuran</th>
              </tr>
            </thead>
            <tbody>
              @foreach($courseStats as $stat)
                <tr>
                  <td><span class="badge bg-label-primary">{{ $stat['code'] }}</span></td>
                  <td><strong class="text-dark small d-block">{{ $stat['name'] }}</strong></td>
                  <td class="text-center"><span class="badge bg-info rounded-pill">{{ $stat['active'] }}</span></td>
                  <td class="text-center"><span class="badge bg-success rounded-pill">{{ $stat['completed'] }}</span></td>
                  <td class="text-end fw-bold text-success font-monospace">RM{{ number_format($stat['revenue'], 2) }}</td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <!-- Right: Active Student Distribution graph & Recent Transactions -->
  <div class="col-md-4 mb-4">
    <!-- Distribution Graph -->
    <div class="card border-0 shadow-sm mb-4">
      <div class="card-header border-bottom py-3">
        <h5 class="card-title mb-0 fw-bold"><i class="ti ti-activity text-primary me-2"></i>Taburan Pelajar Aktif</h5>
      </div>
      <div class="card-body pt-4">
        @php
          $maxActive = max(array_column($courseStats, 'active')) ?: 1;
        @endphp
        @foreach($courseStats as $stat)
          @php
            $percentage = ($stat['active'] / $maxActive) * 100;
          @endphp
          <div class="mb-3">
            <div class="d-flex justify-content-between align-items-center mb-1">
              <span class="fw-bold small text-dark">{{ $stat['code'] }}</span>
              <span class="text-muted small">{{ $stat['active'] }} Pelajar</span>
            </div>
            <div class="progress" style="height: 8px;">
              <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $percentage }}%" aria-valuenow="{{ $percentage }}" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
          </div>
        @endforeach
      </div>
    </div>

    <!-- Recent Transactions -->
    <div class="card border-0 shadow-sm">
      <div class="card-header border-bottom py-3">
        <h5 class="card-title mb-0 fw-bold"><i class="ti ti-receipt text-primary me-2"></i>Transaksi Pembayaran Terkini</h5>
      </div>
      <div class="card-body p-3">
        <div class="timeline">
          @forelse($recentPayments as $pay)
            <div class="mb-3 border-bottom pb-2">
              <div class="d-flex justify-content-between align-items-center mb-1">
                <strong class="small text-dark text-truncate" style="max-width: 140px;">{{ $pay->student->name }}</strong>
                <span class="badge bg-label-success small-badge">Paid</span>
              </div>
              <div class="d-flex justify-content-between align-items-center text-muted small">
                <span>{{ $pay->course->code }}</span>
                <span class="fw-bold text-success font-monospace">RM{{ number_format($pay->amount) }}</span>
              </div>
            </div>
          @empty
            <p class="text-center text-muted py-3 mb-0 small">Tiada sebarang transaksi direkodkan lagi.</p>
          @endforelse
        </div>
      </div>
    </div>

  </div>
</div>
