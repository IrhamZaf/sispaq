<div class="row">
  <div class="col-12">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('student.dashboard') }}">Portal Pelajar</a></li>
        <li class="breadcrumb-item active" aria-current="page">Pembayaran Yuran</li>
      </ol>
    </nav>
  </div>

  @if($success)
    <!-- Payment Success Receipt Layout -->
    <div class="col-md-8 mx-auto">
      <div class="card border-0 shadow-lg mb-4">
        <div class="card-body p-5">
          <div class="text-center mb-5">
            <span class="badge bg-success rounded-circle p-3 mb-3">
              <i class="ti ti-check fs-1 text-white"></i>
            </span>
            <h3 class="fw-bold mb-1">Pembayaran Berjaya!</h3>
            <p class="text-muted">Resit Pembayaran Rasmi SISPAQ APIUM</p>
          </div>

          <div class="border border-dashed p-4 rounded-3 bg-light mb-4">
            <div class="row mb-3">
              <div class="col-sm-6 text-muted">No. Resit:</div>
              <div class="col-sm-6 text-sm-end fw-bold font-monospace">{{ $payment->receipt_number }}</div>
            </div>
            <div class="row mb-3">
              <div class="col-sm-6 text-muted">ID Transaksi:</div>
              <div class="col-sm-6 text-sm-end fw-bold font-monospace">{{ $payment->transaction_id }}</div>
            </div>
            <div class="row mb-3">
              <div class="col-sm-6 text-muted">Tarikh &amp; Masa:</div>
              <div class="col-sm-6 text-sm-end fw-bold">{{ date('d/m/Y h:i A', strtotime($payment->paid_at)) }}</div>
            </div>
            <div class="row mb-3">
              <div class="col-sm-6 text-muted">Kaedah Pembayaran:</div>
              <div class="col-sm-6 text-sm-end fw-bold text-uppercase">{{ $payment->payment_method }}</div>
            </div>
            <hr>
            <div class="row mb-3">
              <div class="col-sm-6 text-muted">Program:</div>
              <div class="col-sm-6 text-sm-end fw-bold text-dark">{{ $payment->course->name }} ({{ $payment->course->code }})</div>
            </div>
            <div class="row mb-3">
              <div class="col-sm-6 text-muted">Peringkat Pengajian:</div>
              <div class="col-sm-6 text-sm-end fw-bold">Modul {{ $payment->module_number }}</div>
            </div>
            <div class="row">
              <div class="col-sm-6 text-muted">Nama Pelajar:</div>
              <div class="col-sm-6 text-sm-end fw-bold text-dark">{{ auth()->user()->name }}</div>
            </div>
          </div>

          <div class="d-flex justify-content-between align-items-center bg-label-success p-3 rounded-3 mb-5">
            <span class="fw-bold">Jumlah Pembayaran</span>
            <span class="h4 fw-bold text-success mb-0">RM{{ number_format($payment->amount, 2) }}</span>
          </div>

          <div class="d-grid gap-2">
            <a href="{{ route('student.dashboard') }}" class="btn btn-primary btn-lg shadow-sm"><i class="ti ti-dashboard me-2"></i> Kembali ke Panel Utama</a>
            <button onclick="window.print();" class="btn btn-outline-secondary"><i class="ti ti-printer me-2"></i> Cetak Resit</button>
          </div>
        </div>
      </div>
    </div>
  @else
    <!-- Payment Form Checkout Page -->
    <div class="col-md-5 mb-4">
      <div class="card border-0 shadow-sm">
        <div class="card-header border-bottom py-3">
          <h5 class="card-title mb-0 fw-bold"><i class="ti ti-receipt text-primary me-2"></i>Ringkasan Inbois</h5>
        </div>
        <div class="card-body pt-4">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
              <span class="badge bg-label-primary mb-1">{{ $payment->course->code }}</span>
              <h6 class="fw-bold text-dark mb-0">{{ $payment->course->name }}</h6>
              <small class="text-muted">Pendaftaran Modul {{ $payment->module_number }}</small>
            </div>
          </div>
          <hr>
          <div class="d-flex justify-content-between align-items-center mb-2">
            <span class="text-muted">Yuran Modul Pengajian</span>
            <span class="fw-bold text-dark">RM{{ number_format($payment->amount, 2) }}</span>
          </div>
          <div class="d-flex justify-content-between align-items-center mb-4">
            <span class="text-muted">Cukai Perkhidmatan (0%)</span>
            <span class="fw-bold text-dark">RM0.00</span>
          </div>
          <hr>
          <div class="d-flex justify-content-between align-items-center bg-light p-3 rounded-3">
            <span class="fw-bold text-dark">Jumlah Perlu Dibayar</span>
            <span class="h4 fw-bold text-primary mb-0">RM{{ number_format($payment->amount, 2) }}</span>
          </div>
        </div>
      </div>
    </div>

    <div class="col-md-7 mb-4">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-header border-bottom py-3">
          <h5 class="card-title mb-0 fw-bold"><i class="ti ti-credit-card text-primary me-2"></i>Gerbang Pembayaran Selamat</h5>
        </div>
        <div class="card-body pt-4">
          <form wire:submit.prevent="processPayment">
            <!-- Payment Methods Tabs -->
            <div class="row mb-4">
              <div class="col-6">
                <div class="card border cursor-pointer text-center py-3 {{ $paymentMethod === 'fpx' ? 'border-primary bg-label-primary' : '' }}" 
                     wire:click="$set('paymentMethod', 'fpx')" style="cursor: pointer;">
                  <i class="ti ti-building-bank fs-2 mb-1"></i>
                  <span class="d-block fw-bold small">FPX Perbankan Atas Talian</span>
                </div>
              </div>
              <div class="col-6">
                <div class="card border cursor-pointer text-center py-3 {{ $paymentMethod === 'card' ? 'border-primary bg-label-primary' : '' }}" 
                     wire:click="$set('paymentMethod', 'card')" style="cursor: pointer;">
                  <i class="ti ti-credit-card fs-2 mb-1"></i>
                  <span class="d-block fw-bold small">Kad Kredit / Debit</span>
                </div>
              </div>
            </div>

            <!-- FPX View -->
            @if($paymentMethod === 'fpx')
              <div class="mb-4">
                <label class="form-label fw-bold">Pilih Bank Anda <span class="text-danger">*</span></label>
                <select wire:model.live="selectedBank" class="form-select">
                  <option value="">-- Pilih Bank --</option>
                  <option value="maybank2u">Maybank2U</option>
                  <option value="cimb_clicks">CIMB Clicks</option>
                  <option value="public_bank">Public Bank</option>
                  <option value="rhb_now">RHB Now</option>
                  <option value="amonline">AmBank</option>
                  <option value="bank_islam">Bank Islam</option>
                </select>
                @error('selectedBank') <span class="text-danger small">{{ $message }}</span> @enderror
              </div>
            @endif

            <!-- Card View -->
            @if($paymentMethod === 'card')
              <div class="mb-4">
                <div class="mb-3">
                  <label class="form-label fw-bold">Nombor Kad Kredit <span class="text-danger">*</span></label>
                  <input type="text" wire:model.live="cardNumber" class="form-control" placeholder="1234 5678 1234 5678" maxlength="16">
                  @error('cardNumber') <span class="text-danger small">{{ $message }}</span> @enderror
                </div>
                <div class="row">
                  <div class="col-6 mb-3">
                    <label class="form-label fw-bold">Tarikh Luput (MM/YY) <span class="text-danger">*</span></label>
                    <input type="text" wire:model.live="cardExpiry" class="form-control" placeholder="12/28" maxlength="5">
                    @error('cardExpiry') <span class="text-danger small">{{ $message }}</span> @enderror
                  </div>
                  <div class="col-6 mb-3">
                    <label class="form-label fw-bold">CVV <span class="text-danger">*</span></label>
                    <input type="password" wire:model.live="cardCvv" class="form-control" placeholder="123" maxlength="3">
                    @error('cardCvv') <span class="text-danger small">{{ $message }}</span> @enderror
                  </div>
                </div>
              </div>
            @endif

            <div class="alert alert-secondary border-0 p-3 mb-4 rounded-3 d-flex align-items-center">
              <i class="ti ti-shield-lock-filled text-success me-2 fs-3"></i>
              <span class="small text-muted">Gerbang pembayaran disokong dengan enkripsi SSL 256-bit bagi menjamin keselamatan maklumat kad/bank anda.</span>
            </div>

            <div class="d-grid">
              <button type="submit" class="btn btn-primary btn-lg shadow" wire:loading.attr="disabled">
                <span wire:loading.remove><i class="ti ti-credit-card me-1"></i> Bayar RM{{ number_format($payment->amount, 2) }}</span>
                <span wire:loading><span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Memproses Pembayaran...</span>
              </button>
            </div>

          </form>
        </div>
      </div>
    </div>
  @endif
</div>
