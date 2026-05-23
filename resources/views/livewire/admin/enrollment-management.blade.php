<div>
  <!-- Page Header -->
  <x-sispaq.page-header 
    title="Senarai Pelajar" 
    subtitle="Urus rekod pendaftaran aktif, tamat belajar, dan penangguhan/pemberhentian program pelajar." 
    icon="tabler-school" 
    :breadcrumb="[
      ['label' => 'Halaman Pentadbir', 'url' => route('admin.dashboard')],
      ['label' => 'Senarai Pelajar']
    ]"
  />

  @if (session()->has('success'))
    <div class="alert alert-success border-0 shadow-sm mb-4 alert-dismissible">
      {{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  @endif

  <!-- Filters Section -->
  <div class="card mb-4 border-0 shadow-sm">
    <div class="card-body">
      <h5 class="card-title mb-3 fw-bold text-dark">Filter</h5>
      <div class="row g-3">
        <!-- Status Filter -->
        <div class="col-md-6">
          <label class="form-label fw-semibold text-muted">Status Pendaftaran</label>
          <select wire:model.live="statusFilter" class="form-select">
            <option value="all">Semua Status</option>
            <option value="active">Aktif</option>
            <option value="completed">Selesai / Tamat</option>
            <option value="dropped">Diberhentikan</option>
          </select>
        </div>
        <!-- Program Filter -->
        <div class="col-md-6">
          <label class="form-label fw-semibold text-muted">Program Pengajian</label>
          <select wire:model.live="courseFilter" class="form-select">
            <option value="all">Semua Program</option>
            @foreach($courses as $c)
              <option value="{{ $c->id }}">{{ $c->code }} - {{ $c->name }}</option>
            @endforeach
          </select>
        </div>
      </div>
    </div>
  </div>

  <!-- Offered Applications (Pending Acceptance) -->
  @if($offeredApplications->isNotEmpty())
    <div class="card mb-4 border-0 shadow-sm">
      <div class="card-header border-bottom py-3 d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0 fw-bold text-warning"><i class="ti tabler-mail-forward me-2 text-warning"></i>Tawaran Menunggu Penerimaan</h5>
        <span class="badge bg-label-warning">{{ $offeredApplications->count() }} Menunggu</span>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
              <tr>
                <th>Pemohon</th>
                <th>Program Ditawarkan</th>
                <th>Tarikh Tawaran</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              @foreach($offeredApplications as $app)
                <tr>
                  <td>
                    <div class="d-flex align-items-center">
                      <div class="avatar avatar-sm me-2">
                        <span class="avatar-initial rounded-circle bg-label-warning fw-bold">{{ substr($app->user->name, 0, 2) }}</span>
                      </div>
                      <div>
                        <strong class="small text-dark">{{ $app->user->name }}</strong>
                      </div>
                    </div>
                  </td>
                  <td>
                    <span class="badge bg-label-warning mb-1">{{ $app->course->code }}</span>
                    <small class="d-block text-muted">{{ $app->course->name }}</small>
                  </td>
                  <td class="small">{{ $app->updated_at->format('d/m/Y h:i A') }}</td>
                  <td><span class="badge bg-label-warning text-capitalize">Tawaran Dihantar</span></td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
  @endif

  <!-- Student Enrollments Table Card -->
  <div class="card shadow-sm border-0">
    <div class="card-body p-0">
      <!-- Secondary Action Bar -->
      <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 p-3 border-bottom">
        <div class="d-flex align-items-center">
          <div class="input-group input-group-merge" style="width: 250px;">
            <span class="input-group-text"><i class="ti tabler-search text-muted"></i></span>
            <input type="text" wire:model.live.debounce.300ms="search" class="form-control" placeholder="Cari Pelajar..." />
          </div>
        </div>
        <div class="d-flex align-items-center gap-2">
          <select wire:model.live="perPage" class="form-select w-auto">
            <option value="7">7</option>
            <option value="10">10</option>
            <option value="25">25</option>
            <option value="50">50</option>
          </select>
          
          <div class="btn-group">
            <button class="btn btn-label-secondary dropdown-toggle" data-bs-toggle="dropdown">
              <i class="ti tabler-download me-1"></i> Eksport
            </button>
            <ul class="dropdown-menu">
              <li><a class="dropdown-item" href="#"><i class="ti tabler-file-text me-2"></i>PDF</a></li>
              <li><a class="dropdown-item" href="#"><i class="ti tabler-file-spreadsheet me-2"></i>Excel</a></li>
            </ul>
          </div>
          
          <button class="btn btn-primary" title="Daftar Pelajar Baharu" onclick="alert('Pendaftaran pelajar baharu secara manual boleh dibuat melalui kelulusan permohonan kemasukan.')">
            <i class="ti tabler-plus me-1"></i> Daftar
          </button>
        </div>
      </div>

      <div class="table-responsive">
        <table class="table table-hover table-striped align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th style="width: 30%;">Pelajar</th>
              <th style="width: 28%;">Program Pengajian</th>
              <th class="text-center" style="width: 15%;">Modul Semasa</th>
              <th style="width: 15%;">Status</th>
              <th class="text-end" style="width: 12%; min-width: 120px; padding-right: 1.5rem;">Actions</th>
            </tr>
          </thead>
          <tbody>
            @forelse($students as $student)
              <tr>
                 <!-- Student Details Column -->
                <td class="align-top py-3">
                  <div class="d-flex align-items-start">
                    <div class="avatar avatar-md me-3 mt-1">
                      <span class="avatar-initial rounded-circle bg-label-primary fw-bold">{{ substr($student->name, 0, 2) }}</span>
                    </div>
                    <div>
                      <div class="d-flex align-items-center gap-1 flex-wrap">
                        <strong class="text-dark">{{ $student->name }}</strong>
                        <button wire:click="openEditStudentModal({{ $student->id }})" class="btn btn-xs btn-link p-0 text-primary" data-bs-toggle="modal" data-bs-target="#editStudentModal" title="Kemas Kini Profil Pelajar">
                          <i class="ti tabler-edit fs-6"></i>
                        </button>
                      </div>
                      @if($student->student_id)
                        <small class="text-primary d-block fw-bold mb-0.5">ID: {{ $student->student_id }}</small>
                      @endif
                      @if($student->uni_course || $student->uni_faculty)
                        <small class="text-dark d-block mb-0.5" style="font-size: 0.78rem;">
                          <i class="ti tabler-school text-muted me-1"></i>{{ $student->uni_course ?? '—' }} ({{ $student->uni_faculty ?? '—' }})
                        </small>
                      @endif
                      <small class="text-muted d-block">IC: {{ $student->ic_number }}</small>
                      <small class="text-muted d-block">Tel: {{ $student->phone }}</small>
                    </div>
                  </div>
                </td>
                
                <!-- Grouped Course Enrollments Subtable -->
                <td colspan="4" class="p-0 align-top">
                  <table class="table table-sm table-borderless mb-0 align-middle">
                    <tbody>
                      @foreach($student->enrollments as $e)
                        <tr class="{{ $loop->last ? '' : 'border-bottom' }}">
                          <!-- Course -->
                          <td style="width: 40%;" class="py-3">
                            <div class="d-flex align-items-center gap-2">
                              <span class="badge bg-label-primary">{{ $e->course->code }}</span>
                              <strong class="small text-dark mb-0">{{ $e->course->name }}</strong>
                            </div>
                          </td>
                          <!-- Module -->
                          <td style="width: 25%;" class="text-center font-monospace fw-bold text-dark">
                            Modul {{ $e->current_module }}
                          </td>
                          <!-- Status -->
                          <td style="width: 20%;">
                            @if($e->status === 'active')
                              <span class="badge bg-label-success text-capitalize">Aktif</span>
                            @elseif($e->status === 'completed')
                              <span class="badge bg-label-info text-capitalize">Selesai / Tamat</span>
                            @elseif($e->status === 'dropped')
                              <span class="badge bg-label-danger text-capitalize">Diberhentikan</span>
                            @else
                              <span class="badge bg-label-dark text-capitalize">{{ $e->status }}</span>
                            @endif
                          </td>
                          <!-- Action buttons for specific course -->
                          <td style="width: 15%;" class="text-end">
                            <div class="d-flex align-items-center justify-content-end gap-1 pe-3">
                              <!-- Edit button -->
                              <button wire:click="editEnrollment({{ $e->id }})" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#editEnrollmentModal" title="Kemas Kini Pendaftaran">
                                <i class="ti tabler-edit"></i>
                              </button>
                              
                              <!-- Delete button -->
                              <button onclick="confirm('Adakah anda pasti mahu memadam pendaftaran pelajar ini? Tindakan ini akan memadam semua rekod kehadiran, kelas, dan jadual berkaitan!') || event.stopImmediatePropagation()" wire:click="deleteEnrollment({{ $e->id }})" class="btn btn-sm btn-danger" title="Padam Pendaftaran">
                                <i class="ti tabler-trash"></i>
                              </button>
                            </div>
                          </td>
                        </tr>
                      @endforeach
                    </tbody>
                  </table>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="5" class="text-center py-5 text-muted">
                  <i class="ti tabler-user-x fs-1 d-block mb-3 text-secondary"></i>
                  Tiada sebarang rekod pendaftaran ditemui.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      <!-- Table Pagination Footer -->
      <div class="card-footer d-flex justify-content-between align-items-center flex-wrap gap-2 border-top py-3">
        <div class="small text-muted">
          Menunjukkan {{ $students->firstItem() ?? 0 }} hingga {{ $students->lastItem() ?? 0 }} daripada {{ $students->total() ?? 0 }} pelajar
        </div>
        <div>
          {{ $students->links() }}
        </div>
      </div>
    </div>
  </div>

  <!-- Modal: Edit Enrollment -->
  <div wire:ignore.self class="modal fade" id="editEnrollmentModal" tabindex="-1" aria-labelledby="editEnrollmentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header border-bottom">
          <h5 class="modal-title fw-bold" id="editEnrollmentModalLabel">Kemas Kini Pendaftaran Pelajar</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form wire:submit.prevent="updateEnrollment">
          <div class="modal-body">
            <!-- Edit Current Module -->
            <div class="mb-3">
              <label class="form-label fw-bold">Modul Pengajian Semasa <span class="text-danger">*</span></label>
              <input type="number" wire:model="editCurrentModule" class="form-control" min="1" required>
              @error('editCurrentModule') <span class="text-danger small">{{ $message }}</span> @enderror
            </div>

            <!-- Edit Status -->
            <div class="mb-3">
              <label class="form-label fw-bold">Status Pendaftaran <span class="text-danger">*</span></label>
              <select wire:model="editStatus" class="form-select" required>
                <option value="active">Aktif</option>
                <option value="completed">Selesai / Tamat</option>
                <option value="dropped">Diberhentikan</option>
              </select>
              @error('editStatus') <span class="text-danger small">{{ $message }}</span> @enderror
            </div>
          </div>
          <div class="modal-footer border-top">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-primary" data-bs-dismiss="modal"><i class="ti tabler-device-floppy me-1"></i> Simpan Perubahan</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Modal: Edit Student Profile & Uni Info -->
  <div wire:ignore.self class="modal fade" id="editStudentModal" tabindex="-1" aria-labelledby="editStudentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header border-bottom">
          <h5 class="modal-title fw-bold" id="editStudentModalLabel">Kemas Kini Profil Pelajar</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form wire:submit.prevent="updateStudent">
          <div class="modal-body">
            <!-- Name -->
            <div class="mb-3">
              <label class="form-label fw-bold">Nama Penuh <span class="text-danger">*</span></label>
              <input type="text" wire:model="editStudentName" class="form-control" required>
              @error('editStudentName') <span class="text-danger small">{{ $message }}</span> @enderror
            </div>

            <div class="row">
              <!-- IC -->
              <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">No. Kad Pengenalan <span class="text-danger">*</span></label>
                <input type="text" wire:model="editStudentIC" class="form-control" required>
                @error('editStudentIC') <span class="text-danger small">{{ $message }}</span> @enderror
              </div>

              <!-- Phone -->
              <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">No. Telefon <span class="text-danger">*</span></label>
                <input type="text" wire:model="editStudentPhone" class="form-control" required>
                @error('editStudentPhone') <span class="text-danger small">{{ $message }}</span> @enderror
              </div>
            </div>

            <!-- Student ID / Matric No -->
            <div class="mb-3">
              <label class="form-label fw-bold">No. Matrik / Student ID</label>
              <input type="text" wire:model="editStudentIdNum" class="form-control" placeholder="Contoh: U2001234">
              @error('editStudentIdNum') <span class="text-danger small">{{ $message }}</span> @enderror
            </div>

            <div class="row">
              <!-- Course in Uni -->
              <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Kursus Universiti</label>
                <input type="text" wire:model="editStudentCourse" class="form-control" placeholder="Contoh: Sarjana Muda Pengajian Islam">
                @error('editStudentCourse') <span class="text-danger small">{{ $message }}</span> @enderror
              </div>

              <!-- Faculty in Uni -->
              <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Fakulti Universiti</label>
                <input type="text" wire:model="editStudentFaculty" class="form-control" placeholder="Contoh: Akademi Pengajian Islam">
                @error('editStudentFaculty') <span class="text-danger small">{{ $message }}</span> @enderror
              </div>
            </div>
          </div>
          <div class="modal-footer border-top">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-primary"><i class="ti tabler-device-floppy me-1"></i> Simpan Perubahan</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function () {
      Livewire.on('close-modal', () => {
        const editStudentModalEl = document.getElementById('editStudentModal');
        const editEnrollmentModalEl = document.getElementById('editEnrollmentModal');
        
        const modal1 = bootstrap.Modal.getInstance(editStudentModalEl);
        if (modal1) modal1.hide();
        
        const modal2 = bootstrap.Modal.getInstance(editEnrollmentModalEl);
        if (modal2) modal2.hide();
        
        const backdrop = document.querySelector('.modal-backdrop');
        if (backdrop) {
          backdrop.remove();
        }
        document.body.style.overflow = 'auto';
      });
    });
  </script>
</div>
