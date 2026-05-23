<div class="d-flex align-items-center justify-content-center min-vh-100 p-4" style="background: #f4f6f9;">
  <div class="card border-0 shadow-lg p-4 w-100" style="max-width: 600px; border-radius: 15px;">
    <!-- Logo & Title -->
    <div class="text-center mb-4 pb-2 border-bottom">
      <h3 class="fw-bold mb-1 text-primary">🏗️ SISPAQ APIUM</h3>
      <h6 class="text-uppercase text-muted fw-bold small">Sistem Pengesahan Sijil Program Ikhtisas</h6>
    </div>

    <!-- Search Input Form -->
    <form wire:submit.prevent="verifyCertificate" class="mb-4">
      <div class="input-group">
        <input type="text" wire:model.live="searchQuery" class="form-control form-control-lg border-primary" placeholder="Masukkan Nombor Sijil (Cth: CERT-KPAQ-...)">
        <button type="submit" class="btn btn-primary px-4"><i class="ti ti-search fs-4"></i> Cari</button>
      </div>
      @error('searchQuery') <span class="text-danger small mt-1 d-block">{{ $message }}</span> @enderror
    </form>

    <!-- Results Block -->
    @if($searched)
      @if($certificate)
        <!-- Valid Certificate details -->
        <div class="card border-success border-2 bg-success-subtle mb-3" style="background-color: #d1e7dd !important; border-color: #badbcc !important;">
          <div class="card-body text-center py-4">
            <span class="badge bg-success rounded-circle p-2 mb-2">
              <i class="ti ti-shield-check fs-2 text-white"></i>
            </span>
            <h4 class="fw-bold text-success mb-1">Status: SIJIL SAH</h4>
            <p class="text-success-emphasis mb-0 small">Sijil ini adalah sah dan berdaftar dalam rekod akademik APIUM.</p>
          </div>
        </div>

        <div class="table-responsive border rounded bg-white">
          <table class="table table-striped align-middle mb-0 text-dark small">
            <tbody>
              <tr>
                <th class="py-3 px-3 text-muted w-40">Nama Pelajar:</th>
                <td class="py-3 px-3 fw-bold text-uppercase">{{ $certificate->student->name }}</td>
              </tr>
              <tr>
                <th class="py-3 px-3 text-muted">No. Kad Pengenalan:</th>
                <td class="py-3 px-3 font-monospace">XXXXXX-XX-{{ substr($certificate->student->ic_number, -4) }}</td>
              </tr>
              <tr>
                <th class="py-3 px-3 text-muted">Program Ikhtisas:</th>
                <td class="py-3 px-3 fw-bold">{{ $certificate->course->name }} ({{ $certificate->course->code }})</td>
              </tr>
              <tr>
                <th class="py-3 px-3 text-muted">Nombor Sijil:</th>
                <td class="py-3 px-3 font-monospace fw-bold text-primary">{{ $certificate->certificate_number }}</td>
              </tr>
              <tr>
                <th class="py-3 px-3 text-muted">Jenis Sijil:</th>
                <td class="py-3 px-3 text-capitalize">
                  @if($certificate->certificate_type === 'kemahiran')
                    Sijil Kemahiran Teknikal Falak
                  @elseif($certificate->certificate_type === 'sanad')
                    Ijazah Sanad Al-Quran Bertulis
                  @else
                    Sijil Kelulusan Akademik
                  @endif
                </td>
              </tr>
              <tr>
                <th class="py-3 px-3 text-muted">Tarikh Dianugerah:</th>
                <td class="py-3 px-3 fw-bold">{{ date('d/m/Y', strtotime($certificate->issue_date)) }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      @else
        <!-- Invalid Certificate warning -->
        <div class="card border-danger border-2 bg-danger-subtle py-4 px-3 text-center" style="background-color: #f8d7da !important; border-color: #f5c2c7 !important;">
          <span class="badge bg-danger rounded-circle p-2 mb-2 mx-auto" style="width: fit-content;">
            <i class="ti ti-shield-x fs-2 text-white"></i>
          </span>
          <h4 class="fw-bold text-danger mb-1">Status: SIJIL TIDAK SAH</h4>
          <p class="text-danger-emphasis mb-0 small">Nombor rujukan sijil tidak ditemui dalam pangkalan data. Sila semak semula ejaan atau hubungi urusetia program.</p>
        </div>
      @endif
    @else
      <!-- Welcome message -->
      <div class="text-center py-4 text-muted">
        <i class="ti ti-qrcode fs-1 d-block mb-3 text-secondary"></i>
        <p class="mb-0 small">Sila imbas kod QR pada sijil fizikal anda atau masukkan nombor rujukan sijil secara manual di atas untuk membuat semakan kesahihan.</p>
      </div>
    @endif

    <div class="text-center mt-4">
      <a href="{{ route('home') }}" class="btn btn-link text-decoration-none small"><i class="ti ti-arrow-left me-1"></i> Kembali ke Portal Utama</a>
    </div>
  </div>
</div>
