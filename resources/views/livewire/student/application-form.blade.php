<div class="row">
  <div class="col-12">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('student.dashboard') }}">Portal Pelajar</a></li>
        <li class="breadcrumb-item active" aria-current="page">Permohonan Program</li>
      </ol>
    </nav>

    <!-- Header Section -->
    <div class="card mb-4 bg-primary text-white border-0">
      <div class="card-body py-4">
        <h4 class="fw-bold mb-1 text-white">Borang Permohonan Kemasukan</h4>
        <p class="mb-0 text-white-50">Program: {{ $course->name }} ({{ $course->code }})</p>
      </div>
    </div>

    <!-- Step Progress Bar -->
    <div class="card mb-4">
      <div class="card-body">
        <div class="d-flex justify-content-around text-center">
          <div class="step-item">
            <span class="badge rounded-circle p-2 {{ $step == 1 ? 'bg-primary' : 'bg-secondary' }}" style="width: 35px; height: 35px; display: inline-flex; align-items: center; justify-content: center;">1</span>
            <p class="mt-2 mb-0 fw-bold {{ $step == 1 ? 'text-primary' : 'text-muted' }} small">Maklumat Profil</p>
          </div>
          <div class="align-self-center flex-grow-1 border-top mx-3" style="border-width: 2px !important;"></div>
          <div class="step-item">
            <span class="badge rounded-circle p-2 {{ $step == 2 ? 'bg-primary' : 'bg-secondary' }}" style="width: 35px; height: 35px; display: inline-flex; align-items: center; justify-content: center;">2</span>
            <p class="mt-2 mb-0 fw-bold {{ $step == 2 ? 'text-primary' : 'text-muted' }} small">Dokumen & Sokongan</p>
          </div>
        </div>
      </div>
    </div>

    @if (session()->has('error'))
      <div class="alert alert-danger border-0 shadow-sm mb-4">
        {{ session('error') }}
      </div>
    @endif

    <div class="card shadow-sm border-0">
      <div class="card-body">
        <form wire:submit.prevent="submitApplication">

          <!-- STEP 1: Profil Khusus Program -->
          @if ($step === 1)
            <div>
              <h5 class="fw-bold border-bottom pb-2 mb-4"><i class="ti ti-user me-2"></i>Maklumat Tambahan Program</h5>
              
              <!-- Personal Info Review -->
              <div class="row mb-4">
                <div class="col-md-6 mb-3">
                  <label class="form-label text-muted small">Nama Penuh Pemohon</label>
                  <input type="text" class="form-control" value="{{ auth()->user()->name }}" disabled>
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label text-muted small">No. Kad Pengenalan</label>
                  <input type="text" class="form-control" value="{{ auth()->user()->ic_number }}" disabled>
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label text-muted small">E-mel</label>
                  <input type="text" class="form-control" value="{{ auth()->user()->email }}" disabled>
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label text-muted small">No. Telefon</label>
                  <input type="text" class="form-control" value="{{ auth()->user()->phone }}" disabled>
                </div>
              </div>

              <!-- Kiblat Specific Category -->
              @if ($course->code === 'KIBLAT')
                <div class="mb-4">
                  <label class="form-label fw-bold text-dark">Kategori Kelayakan Falak <span class="text-danger">*</span></label>
                  <div class="row mt-2">
                    <div class="col-md-6 mb-3">
                      <div class="card border {{ $kiblat_category === 'A' ? 'border-primary bg-label-primary' : '' }} cursor-pointer h-100" 
                           onclick="document.getElementById('catA').click();" style="cursor: pointer;">
                        <div class="card-body p-3">
                          <div class="form-check">
                            <input class="form-check-input" type="radio" wire:model.live="kiblat_category" id="catA" value="A">
                            <label class="form-check-label fw-bold" for="catA">
                              Kategori A (RM1,000 / Modul)
                            </label>
                            <span class="d-block text-muted small mt-1">Mempunyai kelayakan/latar belakang dalam ilmu falak (Sijil/Diploma berkaitan).</span>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-6 mb-3">
                      <div class="card border {{ $kiblat_category === 'B' ? 'border-primary bg-label-primary' : '' }} cursor-pointer h-100" 
                           onclick="document.getElementById('catB').click();" style="cursor: pointer;">
                        <div class="card-body p-3">
                          <div class="form-check">
                            <input class="form-check-input" type="radio" wire:model.live="kiblat_category" id="catB" value="B">
                            <label class="form-check-label fw-bold" for="catB">
                              Kategori B (RM1,200 / Modul)
                            </label>
                            <span class="d-block text-muted small mt-1">Tiada sebarang latar belakang ilmu falak (Terbuka kepada awam).</span>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  @error('kiblat_category') <span class="text-danger small">{{ $message }}</span> @enderror
                </div>
              @endif

              <!-- Talaqqi Specific Level -->
              @if (in_array($course->code, ['Pra KuTAB', 'KuTAB']))
                <div class="mb-4">
                  <label class="form-label fw-bold">Tahap Kemahiran Pembacaan Al-Quran Anda <span class="text-danger">*</span></label>
                  <select wire:model.live="reading_level" class="form-select">
                    <option value="">-- Pilih Tahap Pembacaan --</option>
                    <option value="Merangkak-rangkak">Merangkak-rangkak / Lemah Asas</option>
                    <option value="Lancar Tanpa Tajwid">Lancar membaca, tetapi kurang mahir tajwid</option>
                    <option value="Lancar Berguru">Lancar membaca dan pernah berguru tajwid</option>
                    <option value="Sangat Lancar &amp; Bertajwid">Sangat lancar, mahir tajwid &amp; mampu talaqqi sanad</option>
                  </select>
                  <div class="form-text small text-muted">
                    Nota: Permohonan Talaqqi Bersanad memerlukan anda menduduki Ujian Penempatan Al-Quran sebelum tawaran rasmi dikeluarkan.
                  </div>
                  @error('reading_level') <span class="text-danger small">{{ $message }}</span> @enderror
                </div>
              @endif

              <!-- Terms alert -->
              <div class="alert alert-warning border-0 rounded-3 mb-4">
                <h6 class="alert-heading fw-bold mb-1">Nota Penting Pengajian</h6>
                <p class="mb-0 small">
                  Jadual kelas: <strong>{{ $course->schedule_day_time ?? 'Fleksibel' }}</strong>. Sila pastikan anda bersedia meluangkan masa mengikut ketetapan program.
                </p>
              </div>

              <div class="d-flex justify-content-end">
                <button type="button" class="btn btn-primary" wire:click="nextStep">
                  Seterusnya <i class="ti ti-chevron-right ms-1"></i>
                </button>
              </div>
            </div>
          @endif

          <!-- STEP 2: Upload Dokumen -->
          @if ($step === 2)
            <div>
              <h5 class="fw-bold border-bottom pb-2 mb-4"><i class="ti ti-file-upload me-2"></i>Muat Naik Dokumen Sokongan</h5>

              <!-- IC Upload -->
              <div class="mb-4">
                <label class="form-label fw-bold">Salinan Kad Pengenalan (IC) <span class="text-danger">*</span></label>
                <input type="file" class="form-control" wire:model="ic_document">
                <div class="form-text small text-muted">Sila muat naik fail berformat PDF, JPG, PNG atau JPEG (Maksimum: 2MB).</div>
                @error('ic_document') <span class="text-danger small">{{ $message }}</span> @enderror

                @if ($ic_document)
                  <div class="mt-2 text-success small">
                    <i class="ti ti-check me-1"></i> Fail dipilih: {{ $ic_document->getClientOriginalName() }}
                  </div>
                @endif
              </div>

              <!-- Supporting Doc Upload -->
              <div class="mb-4">
                <label class="form-label fw-bold">Sijil Kelayakan / Dokumen Sokongan Lain (Jika ada)</label>
                <input type="file" class="form-control" wire:model="supporting_documents">
                <div class="form-text small text-muted">Muat naik slip keputusan SPM, Sijil Pengajian Falak, atau kelayakan Al-Quran yang berkaitan (Maksimum: 2MB).</div>
                @error('supporting_documents') <span class="text-danger small">{{ $message }}</span> @enderror

                @if ($supporting_documents)
                  <div class="mt-2 text-success small">
                    <i class="ti ti-check me-1"></i> Fail dipilih: {{ $supporting_documents->getClientOriginalName() }}
                  </div>
                @endif
              </div>

              <!-- Declarations -->
              <div class="card bg-light border-0 mb-4 rounded-3">
                <div class="card-body p-3">
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="declaration" required>
                    <label class="form-check-label small text-muted" for="declaration">
                      Saya mengaku bahawa semua maklumat dan dokumen yang dikemukakan adalah benar dan sahih. Pihak Akademi Pengajian Islam Universiti Malaya (APIUM) berhak membatalkan permohonan sekiranya maklumat didapati palsu.
                    </label>
                  </div>
                </div>
              </div>

              <div class="d-flex justify-content-between">
                <button type="button" class="btn btn-label-secondary" wire:click="prevStep">
                  <i class="ti ti-chevron-left me-1"></i> Kembali
                </button>
                <button type="submit" class="btn btn-success" wire:loading.attr="disabled">
                  <span wire:loading.remove><i class="ti ti-send me-1"></i> Hantar Permohonan</span>
                  <span wire:loading><span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Menghantar...</span>
                </button>
              </div>
            </div>
          @endif

        </form>
      </div>
    </div>
  </div>
</div>
