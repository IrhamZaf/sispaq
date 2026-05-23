<div class="row">
  <div class="col-12">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('student.dashboard') }}">Portal Pelajar</a></li>
        <li class="breadcrumb-item active" aria-current="page">Tempahan Talaqqi</li>
      </ol>
    </nav>

    <!-- Header Section -->
    <div class="card mb-4 bg-success text-white border-0">
      <div class="card-body py-4">
        <h4 class="fw-bold mb-1 text-white">Sistem Penjadualan Talaqqi Bersanad</h4>
        <p class="mb-0 text-white-50">Sila tempah slot 15-minit mingguan bersama Ustaz/Guru anda untuk talaqqi Al-Quran atau Kitab Turath.</p>
      </div>
    </div>

    @if (session()->has('success'))
      <div class="alert alert-success border-0 shadow-sm mb-4">
        {{ session('success') }}
      </div>
    @endif

    @if (session()->has('error'))
      <div class="alert alert-danger border-0 shadow-sm mb-4">
        {{ session('error') }}
      </div>
    @endif

    @if($talaqqiEnrollments->isEmpty())
      <div class="card border-0 shadow-sm mb-4 text-center py-5">
        <div class="card-body">
          <i class="ti ti-calendar-off fs-1 text-secondary mb-3"></i>
          <h5 class="fw-bold text-dark">Tiada Program Talaqqi Aktif</h5>
          <p class="text-muted">Pendaftaran scheduler slot talaqqi hanya terbuka kepada pelajar yang berdaftar di bawah program Pra KuTAB, KuTAB, atau Talaqqi Kitab Turath (KTKT).</p>
          <a href="{{ route('home') }}" class="btn btn-primary mt-2">Lihat Profil Kursus</a>
        </div>
      </div>
    @else
      <div class="row">
        <!-- Booking Form Card -->
        <div class="col-md-5 mb-4">
          <div class="card border-0 shadow-sm h-100">
            <div class="card-header border-bottom py-3">
              <h5 class="card-title mb-0 fw-bold"><i class="ti ti-calendar-event text-success me-2"></i>Tempah Slot Baru</h5>
            </div>
            <div class="card-body pt-4">
              <form wire:submit.prevent="bookSlot">
                
                <div class="mb-3">
                  <label class="form-label fw-bold">Pilih Program Talaqqi <span class="text-danger">*</span></label>
                  <select wire:model.live="selectedEnrollmentId" class="form-select">
                    @foreach($talaqqiEnrollments as $enroll)
                      <option value="{{ $enroll->id }}">{{ $enroll->course->code }} - {{ $enroll->course->name }}</option>
                    @endforeach
                  </select>
                  @error('selectedEnrollmentId') <span class="text-danger small">{{ $message }}</span> @enderror
                </div>

                <div class="mb-3">
                  <label class="form-label fw-bold">Pilih Guru / Syeikh <span class="text-danger">*</span></label>
                  <select wire:model.live="selectedTeacherId" class="form-select">
                    @foreach($teachers as $t)
                      <option value="{{ $t->id }}">{{ $t->name }}</option>
                    @endforeach
                  </select>
                  @error('selectedTeacherId') <span class="text-danger small">{{ $message }}</span> @enderror
                </div>

                <div class="mb-3">
                  <label class="form-label fw-bold">Pilih Tarikh <span class="text-danger">*</span></label>
                  <input type="date" wire:model.live="bookingDate" class="form-control" min="{{ date('Y-m-d') }}">
                  @error('bookingDate') <span class="text-danger small">{{ $message }}</span> @enderror
                </div>

                <div class="mb-4">
                  <label class="form-label fw-bold">Pilih Slot Masa (15 Minit) <span class="text-danger">*</span></label>
                  <div class="row g-2">
                    @php
                      $slots = [
                        '09:00:00' => '09:00 - 09:15 AM',
                        '09:15:00' => '09:15 - 09:30 AM',
                        '09:30:00' => '09:30 - 09:45 AM',
                        '09:45:00' => '09:45 - 10:00 AM',
                        '10:00:00' => '10:00 - 10:15 AM',
                        '10:15:00' => '10:15 - 10:30 AM',
                        '10:30:00' => '10:30 - 10:45 AM',
                        '10:45:00' => '10:45 - 11:00 AM',
                      ];
                    @endphp
                    @foreach($slots as $timeValue => $timeLabel)
                      <div class="col-6">
                        <input type="radio" class="btn-check" wire:model="bookingTime" id="slot-{{ $loop->index }}" value="{{ $timeValue }}" autocomplete="off">
                        <label class="btn btn-outline-success btn-sm w-100 py-2" for="slot-{{ $loop->index }}">{{ $timeLabel }}</label>
                      </div>
                    @endforeach
                  </div>
                  @error('bookingTime') <span class="text-danger small d-block mt-2">{{ $message }}</span> @enderror
                </div>

                <div class="d-grid">
                  <button type="submit" class="btn btn-success btn-lg shadow-sm"><i class="ti ti-circle-plus me-1"></i> Tempah Slot Masa</button>
                </div>

              </form>
            </div>
          </div>
        </div>

        <!-- History/List Card -->
        <div class="col-md-7 mb-4">
          <div class="card border-0 shadow-sm h-100">
            <div class="card-header border-bottom py-3">
              <h5 class="card-title mb-0 fw-bold"><i class="ti ti-list text-success me-2"></i>Rekod Tempahan Talaqqi Anda</h5>
            </div>
            <div class="card-body p-0">
              <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                  <thead class="table-light">
                    <tr>
                      <th>Tarikh &amp; Masa</th>
                      <th>Guru / Kursus</th>
                      <th>Kemajuan</th>
                      <th>Status Sesi</th>
                      <th class="text-end">Tindakan</th>
                    </tr>
                  </thead>
                  <tbody>
                    @forelse($existingBookings as $booking)
                      <tr>
                        <td>
                          <strong class="d-block text-dark">{{ date('d/m/Y', strtotime($booking->booking_datetime)) }}</strong>
                          <span class="text-muted small">{{ date('h:i A', strtotime($booking->booking_datetime)) }}</span>
                        </td>
                        <td>
                          <span class="badge bg-label-success mb-1">{{ $booking->enrollment->course->code }}</span>
                          <strong class="d-block small text-dark">{{ $booking->teacher->name }}</strong>
                        </td>
                        <td>
                          @if($booking->current_page)
                            <span class="badge bg-label-info font-monospace">Hlm {{ $booking->current_page }} / 604</span>
                          @else
                            <span class="text-muted small">Belum mula</span>
                          @endif
                        </td>
                        <td>
                          @if($booking->session_status === 'Lulus')
                            <span class="badge bg-success">Lulus (Selesai)</span>
                          @elseif($booking->session_status === 'Perlu Ulang')
                            <span class="badge bg-danger">Perlu Ulang</span>
                          @else
                            <span class="badge bg-warning">Belum Selesai</span>
                          @endif
                        </td>
                        <td class="text-end">
                          @if($booking->session_status === 'Belum Selesai')
                            <button wire:click="cancelBooking({{ $booking->id }})" class="btn btn-xs btn-outline-danger"><i class="ti ti-trash me-1"></i> Batal</button>
                          @else
                            <span class="text-muted small">-</span>
                          @endif
                        </td>
                      </tr>
                    @empty
                      <tr>
                        <td colspan="5" class="text-center py-5 text-muted">
                          <i class="ti ti-calendar-off fs-2 d-block mb-2 text-secondary"></i>
                          Tiada rekod tempahan talaqqi aktif ditemui.
                        </td>
                      </tr>
                    @endforelse
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    @endif
  </div>
</div>
