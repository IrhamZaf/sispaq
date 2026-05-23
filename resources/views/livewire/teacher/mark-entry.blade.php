<div class="row">
  <div class="col-12">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('teacher.dashboard') }}">Halaman Pensyarah</a></li>
        <li class="breadcrumb-item active" aria-current="page">Kemasukan Markah</li>
      </ol>
    </nav>

    <!-- Header -->
    <div class="card mb-4 bg-primary text-white border-0">
      <div class="card-body py-4">
        <h4 class="fw-bold mb-1 text-white">Borang Penilaian &amp; Gred Modul</h4>
        <p class="mb-0 text-white-50">Sila masukkan keputusan markah peperiksaan akhir modul untuk melayakkan pelajar naik ke modul pengajian seterusnya.</p>
      </div>
    </div>

    @if (session()->has('success'))
      <div class="alert alert-success border-0 shadow-sm mb-4">
        {{ session('success') }}
      </div>
    @endif

    <!-- Selector Card -->
    <div class="card shadow-sm border-0 mb-4">
      <div class="card-body">
        <div class="row align-items-end">
          <div class="col-md-5 mb-3 mb-md-0">
            <label class="form-label fw-bold text-dark">Pilih Program Ikhtisas</label>
            <select wire:model.live="selectedCourseId" wire:change="loadStudents" class="form-select">
              <option value="">-- Pilih Program --</option>
              @foreach($courses as $c)
                <option value="{{ $c->id }}">{{ $c->code }} - {{ $c->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-4 mb-3 mb-md-0">
            <label class="form-label fw-bold text-dark">Modul Pengajian</label>
            <select wire:model.live="selectedModule" wire:change="loadStudents" class="form-select">
              @for($i = 1; $i <= 12; $i++)
                <option value="{{ $i }}">Modul {{ $i }}</option>
              @endfor
            </select>
          </div>
          <div class="col-md-3">
            <button wire:click="loadStudents" class="btn btn-primary w-100"><i class="ti ti-refresh me-1"></i> Segarkan Senarai</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Student Grades Entry Card -->
    <div class="card shadow-sm border-0">
      <div class="card-header border-bottom py-3">
        <h5 class="card-title mb-0 fw-bold"><i class="ti ti-checklist me-2 text-primary"></i>Senarai Pelajar Penilaian</h5>
      </div>
      <div class="card-body p-0">
        @if(!$selectedCourseId)
          <div class="text-center py-5 text-muted">
            <i class="ti ti-book-upload fs-1 d-block mb-3 text-secondary"></i>
            Sila pilih program ikhtisas dan modul di atas untuk memaparkan senarai pelajar.
          </div>
        @elseif(empty($students))
          <div class="text-center py-5 text-muted">
            <i class="ti ti-user-x fs-1 d-block mb-3 text-secondary"></i>
            Tiada pelajar aktif direkodkan dalam modul {{ $selectedModule }} bagi kursus yang dipilih.
          </div>
        @else
          <form wire:submit.prevent="saveGrades">
            <div class="table-responsive">
              <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                  <tr>
                    <th>Pelajar</th>
                    <th>Kemajuan Modul</th>
                    <th style="width: 200px;">Markah (0 - 100)</th>
                    <th>Status Kelulusan</th>
                    <th>Catatan Pensyarah</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($students as $enroll)
                    <tr>
                      <td>
                        <div class="d-flex align-items-center">
                          <div class="avatar avatar-md me-3">
                            <span class="avatar-initial rounded-circle bg-label-success fw-bold">{{ substr($enroll->student->name, 0, 2) }}</span>
                          </div>
                          <div>
                            <strong class="d-block text-dark">{{ $enroll->student->name }}</strong>
                            <small class="text-muted d-block">IC: {{ $enroll->student->ic_number }}</small>
                          </div>
                        </div>
                      </td>
                      <td>
                        <span class="badge bg-label-info">Modul {{ $enroll->current_module }} Aktif</span>
                      </td>
                      <td>
                        <div class="input-group input-group-sm">
                          <input type="number" wire:model.live="marks.{{ $enroll->id }}" class="form-control text-center font-monospace fw-bold" min="0" max="100" placeholder="Markah">
                          <span class="input-group-text">/ 100</span>
                        </div>
                        @error('marks.' . $enroll->id) <span class="text-danger small d-block mt-1">{{ $message }}</span> @enderror
                      </td>
                      <td>
                        @if(isset($marks[$enroll->id]) && $marks[$enroll->id] !== '')
                          @if($marks[$enroll->id] >= 40)
                            <span class="badge bg-label-success"><i class="ti ti-check me-1"></i> LULUS</span>
                          @else
                            <span class="badge bg-label-danger"><i class="ti ti-x me-1"></i> GAGAL</span>
                          @endif
                        @else
                          <span class="badge bg-label-secondary">Belum dinilai</span>
                        @endif
                      </td>
                      <td>
                        @if(isset($marks[$enroll->id]) && $marks[$enroll->id] !== '')
                          @if($marks[$enroll->id] >= 40)
                            <small class="text-muted text-success-50">Pelajar layak naik ke modul seterusnya.</small>
                          @else
                            <small class="text-danger">Pelajar perlu mengulang semula modul ini.</small>
                          @endif
                        @else
                          <span class="text-muted small">-</span>
                        @endif
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>

            <div class="card-footer border-top py-3 d-flex justify-content-end bg-light">
              <button type="submit" class="btn btn-success px-4 shadow-sm"><i class="ti ti-device-floppy me-1"></i> Simpan &amp; Hantar Keputusan</button>
            </div>
          </form>
        @endif
      </div>
    </div>
  </div>
</div>
