<div class="row">
  <div class="col-12">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('teacher.dashboard') }}">Halaman Pensyarah</a></li>
        <li class="breadcrumb-item active" aria-current="page">Sesi Talaqqi</li>
      </ol>
    </nav>
  </div>

  @if (session()->has('success'))
    <div class="col-12 mb-4">
      <div class="alert alert-success border-0 shadow-sm">
        {{ session('success') }}
      </div>
    </div>
  @endif

  @if($activeBooking)
    <!-- Active Talaqqi Live Session Dashboard -->
    <div class="col-md-7 mb-4">
      <div class="card border-0 shadow-sm">
        <div class="card-header border-bottom py-3 d-flex justify-content-between align-items-center">
          <h5 class="card-title mb-0 fw-bold"><i class="ti ti-microphone text-success me-2"></i>Sesi Talaqqi Aktif</h5>
          <span class="badge bg-label-success">{{ $activeBooking->enrollment->course->code }}</span>
        </div>
        <div class="card-body pt-4">
          <!-- Student Info Header -->
          <div class="d-flex align-items-center mb-4 p-3 bg-light rounded-3">
            <div class="avatar avatar-md me-3">
              <span class="avatar-initial rounded-circle bg-success fw-bold">{{ substr($activeBooking->student->name, 0, 2) }}</span>
            </div>
            <div>
              <h5 class="fw-bold mb-0 text-dark">{{ $activeBooking->student->name }}</h5>
              <small class="text-muted">No. IC: {{ $activeBooking->student->ic_number }} | Slot: {{ date('h:i A', strtotime($activeBooking->booking_datetime)) }}</small>
            </div>
          </div>

          <form wire:submit.prevent="saveSession">
            <div class="row">
              <div class="col-sm-6 mb-3">
                <label class="form-label fw-bold">Nombor Halaman Al-Quran (1-604) <span class="text-danger">*</span></label>
                <input type="number" wire:model="currentPage" class="form-control text-center font-monospace h4 fw-bold mb-0" min="1" max="604" placeholder="Contoh: 1">
                @error('currentPage') <span class="text-danger small">{{ $message }}</span> @enderror
              </div>
              
              <div class="col-sm-6 mb-3">
                <label class="form-label fw-bold">Penilaian Sesi Ini <span class="text-danger">*</span></label>
                <div class="d-flex gap-3 mt-2">
                  <div class="form-check">
                    <input class="form-check-input" type="radio" wire:model="sessionStatus" id="statusLulus" value="Lulus">
                    <label class="form-check-label fw-bold text-success" for="statusLulus">
                      <i class="ti ti-check me-1"></i> LULUS (Muka Surat Lepas)
                    </label>
                  </div>
                  <div class="form-check">
                    <input class="form-check-input" type="radio" wire:model="sessionStatus" id="statusUlang" value="Perlu Ulang">
                    <label class="form-check-label fw-bold text-danger" for="statusUlang">
                      <i class="ti ti-rotate me-1"></i> Perlu Ulang Halaman
                    </label>
                  </div>
                </div>
                @error('sessionStatus') <span class="text-danger small d-block mt-1">{{ $message }}</span> @enderror
              </div>

              <div class="col-12 mb-4">
                <label class="form-label fw-bold">Nota Hukum Tajwid / Maklum Balas Mukhraj</label>
                <textarea wire:model="sessionNotes" class="form-control" rows="4" placeholder="Masukkan kesilapan makhraj huruf, dengung atau catatan tajwid di sini..."></textarea>
                @error('sessionNotes') <span class="text-danger small">{{ $message }}</span> @enderror
              </div>
            </div>

            <div class="d-flex justify-content-between border-top pt-3">
              <button type="button" wire:click="$set('activeBooking', null)" class="btn btn-outline-secondary">Batal Sesi</button>
              <button type="submit" class="btn btn-success"><i class="ti ti-circle-check-filled me-1"></i> Tamatkan &amp; Simpan Rekod</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Student Talaqqi History -->
    <div class="col-md-5 mb-4">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-header border-bottom py-3">
          <h5 class="card-title mb-0 fw-bold"><i class="ti ti-history text-success me-2"></i>Sejarah Talaqqi Pelajar</h5>
        </div>
        <div class="card-body p-3">
          <div class="timeline" style="max-height: 400px; overflow-y: auto;">
            @forelse($studentHistory as $history)
              <div class="timeline-item border-bottom pb-2 mb-2">
                <div class="d-flex justify-content-between align-items-center mb-1">
                  <span class="badge bg-label-info small-badge font-monospace">Hlm {{ $history->current_page }}</span>
                  <span class="small text-muted">{{ date('d/m/Y', strtotime($history->booking_datetime)) }}</span>
                </div>
                <div class="small">
                  Status: 
                  @if($history->session_status === 'Lulus')
                    <span class="text-success fw-bold">Lulus</span>
                  @else
                    <span class="text-danger fw-bold">Perlu Ulang</span>
                  @endif
                </div>
                @if($history->notes)
                  <p class="mb-0 text-muted small mt-1"><em>"{{ $history->notes }}"</em></p>
                @endif
              </div>
            @empty
              <p class="text-center text-muted py-5 small mb-0">Tiada sejarah sesi terdahulu dijumpai.</p>
            @endforelse
          </div>
        </div>
      </div>
    </div>
  @else
    <!-- Bookings Queue Table -->
    <div class="col-12 mb-4">
      <div class="card border-0 shadow-sm">
        <div class="card-header border-bottom py-3">
          <h5 class="card-title mb-0 fw-bold"><i class="ti ti-list text-success me-2"></i>Senarai Slot Tempahan Talaqqi Pelajar</h5>
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
              <thead class="table-light">
                <tr>
                  <th>Pelajar</th>
                  <th>Program Talaqqi</th>
                  <th>Tarikh &amp; Masa Slot</th>
                  <th>Status Sesi</th>
                  <th class="text-end">Tindakan</th>
                </tr>
              </thead>
              <tbody>
                @forelse($bookings as $b)
                  <tr>
                    <td>
                      <div class="d-flex align-items-center">
                        <div class="avatar avatar-md me-3">
                          <span class="avatar-initial rounded-circle bg-label-success fw-bold">{{ substr($b->student->name, 0, 2) }}</span>
                        </div>
                        <div>
                          <strong class="d-block text-dark">{{ $b->student->name }}</strong>
                          <small class="text-muted d-block">IC: {{ $b->student->ic_number }}</small>
                        </div>
                      </div>
                    </td>
                    <td>
                      <span class="badge bg-label-success mb-1">{{ $b->enrollment->course->code }}</span>
                      <strong class="d-block small text-dark">{{ $b->enrollment->course->name }}</strong>
                    </td>
                    <td>
                      <strong class="d-block text-dark">{{ date('d/m/Y', strtotime($b->booking_datetime)) }}</strong>
                      <span class="text-muted small">{{ date('h:i A', strtotime($b->booking_datetime)) }}</span>
                    </td>
                    <td>
                      @if($b->session_status === 'Lulus')
                        <span class="badge bg-success">Lulus</span>
                      @elseif($b->session_status === 'Perlu Ulang')
                        <span class="badge bg-danger">Perlu Ulang</span>
                      @else
                        <span class="badge bg-warning">Menunggu Sesi</span>
                      @endif
                    </td>
                    <td class="text-end">
                      @if($b->session_status === 'Belum Selesai')
                        <button wire:click="startSession({{ $b->id }})" class="btn btn-sm btn-success"><i class="ti ti-player-play me-1"></i> Mulakan Sesi</button>
                      @else
                        <span class="text-muted small">Selesai</span>
                      @endif
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="5" class="text-center py-5 text-muted">
                      <i class="ti ti-calendar-off fs-1 d-block mb-3 text-secondary"></i>
                      Tiada sebarang slot tempahan talaqqi aktif ditemui di bawah nama anda.
                    </td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  @endif
</div>
