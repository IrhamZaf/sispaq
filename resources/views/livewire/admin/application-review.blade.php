@php use Illuminate\Support\Facades\Storage; @endphp
<div class="row">
  <div class="col-12">
    <!-- Page Header -->
    <x-sispaq.page-header 
      title="Permohonan Pelajar" 
      subtitle="Semakan & Penilaian permohonan kemasukan kursus, penjadualan ujian penempatan talaqqi, dan tawaran modul." 
      icon="tabler-users" 
      :breadcrumb="[
        ['label' => 'Halaman Pentadbir', 'url' => route('admin.dashboard')],
        ['label' => 'Permohonan Pelajar']
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
            <label class="form-label fw-semibold text-muted">Status</label>
            <select wire:model.live="statusFilter" class="form-select">
              <option value="all">Semua Status</option>
              <option value="pending">Menunggu Semakan</option>
              <option value="under_review">Dalam Semakan</option>
              <option value="test_scheduled">Ujian Dijadual</option>
              <option value="offered">Tawaran Diberi</option>
              <option value="approved">Tawaran Diterima</option>
              <option value="rejected">Ditolak</option>
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

    <!-- Applications Table Card -->
    <div class="card shadow-sm border-0">
      <div class="card-body p-0">
        <!-- Secondary Action Bar -->
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 p-3 border-bottom">
          <div class="d-flex align-items-center">
            <div class="input-group input-group-merge" style="width: 250px;">
              <span class="input-group-text"><i class="ti tabler-search text-muted"></i></span>
              <input type="text" wire:model.live.debounce.300ms="search" class="form-control" placeholder="Cari Pemohon..." />
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
            
            <button class="btn btn-primary" title="Tambah Permohonan (Pelajar Baru)" onclick="alert('Permohonan baharu perlu didaftarkan oleh pelajar melalui halaman utama.')">
              <i class="ti tabler-plus me-1"></i> Tambah
            </button>
          </div>
        </div>

        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
              <tr>
                <th>Pemohon</th>
                <th>Program Dipohon</th>
                <th>Dokumen</th>
                <th>Ujian Penempatan</th>
                <th>Status</th>
                <th class="text-end" style="min-width: 180px;">Actions</th>
              </tr>
            </thead>
            <tbody>
              @forelse($applications as $app)
                <tr>
                  <td>
                    <div class="d-flex align-items-center">
                      <div class="avatar avatar-md me-3">
                        <span class="avatar-initial rounded-circle bg-label-primary fw-bold">{{ substr($app->user->name, 0, 2) }}</span>
                      </div>
                      <div>
                        <strong class="d-block text-dark">{{ $app->user->name }}</strong>
                        <small class="text-muted d-block">IC: {{ $app->user->ic_number }}</small>
                        <small class="text-muted d-block">Tel: {{ $app->user->phone }}</small>
                      </div>
                    </div>
                  </td>
                  <td>
                    <span class="badge bg-label-primary mb-1">{{ $app->course->code }}</span>
                    <strong class="d-block small text-dark">{{ $app->course->name }}</strong>
                    @if($app->kiblat_category)
                      <small class="text-success d-block">Kategori {{ $app->kiblat_category }}</small>
                    @endif
                    @if($app->reading_level)
                      <small class="text-info d-block">Tahap Quran: {{ $app->reading_level }}</small>
                    @endif
                  </td>
                  <td>
                    <div class="d-flex flex-column gap-1">
                      @if($app->ic_document)
                        <a href="{{ Storage::disk('public')->url($app->ic_document) }}" target="_blank" class="btn btn-xs btn-outline-secondary px-2 text-start">
                          <i class="ti tabler-id me-1"></i> Salinan IC
                        </a>
                      @else
                        <span class="text-danger small">Tiada IC</span>
                      @endif

                      @if($app->supporting_documents)
                        <a href="{{ Storage::disk('public')->url($app->supporting_documents) }}" target="_blank" class="btn btn-xs btn-outline-secondary px-2 text-start">
                          <i class="ti tabler-file-text me-1"></i> Sijil Sokongan
                        </a>
                      @endif
                    </div>
                  </td>
                  <td>
                    @if($app->placementTest)
                      <div class="border p-2 rounded bg-light">
                        <small class="d-block"><strong>Penguji:</strong> {{ $app->placementTest->examiner?->name ?? 'Belum Ditentu' }}</small>
                        <small class="d-block"><strong>Masa:</strong> {{ date('d/m/Y h:i A', strtotime($app->placementTest->test_date_time)) }}</small>
                        <small class="d-block">
                          <strong>Hasil:</strong> 
                          @if($app->placementTest->status === 'completed')
                            <span class="text-success">{{ $app->placementTest->reading_score }}</span> (Syor: {{ $app->placementTest->recommendedCourse?->code }})
                          @else
                            <span class="text-warning">Dijadualkan</span>
                          @endif
                        </small>
                      </div>
                    @else
                      <span class="text-muted small">Tidak berkenaan / Belum dijadual</span>
                    @endif
                  </td>
                  <td>
                    @if($app->status === 'pending')
                      <span class="badge bg-label-warning text-capitalize">Menunggu Semakan</span>
                    @elseif($app->status === 'under_review')
                      <span class="badge bg-label-info text-capitalize">Dalam Semakan</span>
                    @elseif($app->status === 'test_scheduled')
                      <span class="badge bg-label-secondary text-capitalize">Ujian Dijadual</span>
                    @elseif($app->status === 'offered')
                      <span class="badge bg-label-success text-capitalize">Tawaran Diberi</span>
                    @elseif($app->status === 'approved')
                      <span class="badge bg-label-success text-capitalize">Tawaran Diterima</span>
                    @elseif($app->status === 'rejected')
                      <span class="badge bg-label-danger text-capitalize">Ditolak</span>
                    @else
                      <span class="badge bg-label-dark text-capitalize">{{ $app->status }}</span>
                    @endif
                  </td>
                  <td class="text-end">
                    <div class="d-flex align-items-center justify-content-end gap-1">
                      <!-- Status-specific actions -->
                      @if($app->status === 'pending' || $app->status === 'under_review')
                        @if(in_array($app->course->code, ['Pra KuTAB', 'KuTAB']))
                          <button wire:click="selectAppForTest({{ $app->id }})" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#scheduleTestModal">
                            <i class="ti tabler-calendar me-1"></i> Ujian
                          </button>
                        @else
                          <button wire:click="changeStatus({{ $app->id }}, 'offered')" class="btn btn-sm btn-success">
                            <i class="ti tabler-check me-1"></i> Terima
                          </button>
                        @endif
                        <button wire:click="changeStatus({{ $app->id }}, 'rejected')" class="btn btn-sm btn-danger">
                          <i class="ti tabler-x me-1"></i> Tolak
                        </button>
                      @elseif($app->status === 'test_scheduled' && $app->placementTest && $app->placementTest->status !== 'completed')
                        <button wire:click="selectTestForResult({{ $app->placementTest->id }})" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#recordResultModal">
                          <i class="ti tabler-edit me-1"></i> Rekod
                        </button>
                      @endif

                      <!-- Edit button -->
                      <button wire:click="editApplication({{ $app->id }})" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#editApplicationModal" title="Edit">
                        <i class="ti tabler-edit"></i>
                      </button>
                      
                      <!-- Delete button -->
                      <button onclick="confirm('Adakah anda pasti mahu memadam permohonan ini?') || event.stopImmediatePropagation()" wire:click="deleteApplication({{ $app->id }})" class="btn btn-sm btn-danger" title="Padam">
                        <i class="ti tabler-trash"></i>
                      </button>
                    </div>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="6" class="text-center py-5 text-muted">
                    <i class="ti tabler-user-x fs-1 d-block mb-3 text-secondary"></i>
                    Tiada sebarang permohonan kemasukan ditemui.
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        <!-- Table Pagination Footer -->
        <div class="card-footer d-flex justify-content-between align-items-center flex-wrap gap-2 border-top py-3">
          <div class="small text-muted">
            Menunjukkan {{ $applications->firstItem() ?? 0 }} hingga {{ $applications->lastItem() ?? 0 }} daripada {{ $applications->total() ?? 0 }} permohonan
          </div>
          <div>
            {{ $applications->links() }}
          </div>
        </div>
      </div>
    </div>

    <!-- Modal 1: Schedule Placement Test -->
    <div wire:ignore.self class="modal fade" id="scheduleTestModal" tabindex="-1" aria-labelledby="scheduleTestModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header border-bottom">
            <h5 class="modal-title fw-bold" id="scheduleTestModalLabel">Jadualkan Ujian Penempatan Al-Quran</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <form wire:submit.prevent="scheduleTest">
            <div class="modal-body">
              <div class="mb-3">
                <label class="form-label fw-bold">Pilih Guru / Penguji <span class="text-danger">*</span></label>
                <select wire:model="examinerId" class="form-select">
                  <option value="">-- Pilih Penguji --</option>
                  @foreach($teachers as $t)
                    <option value="{{ $t->id }}">{{ $t->name }}</option>
                  @endforeach
                </select>
                @error('examinerId') <span class="text-danger small">{{ $message }}</span> @enderror
              </div>
              <div class="mb-3">
                <label class="form-label fw-bold">Tarikh &amp; Masa Ujian <span class="text-danger">*</span></label>
                <input type="datetime-local" wire:model="testDateTime" class="form-control">
                @error('testDateTime') <span class="text-danger small">{{ $message }}</span> @enderror
              </div>
            </div>
            <div class="modal-footer border-top">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
              <button type="submit" class="btn btn-primary" data-bs-dismiss="modal"><i class="ti tabler-calendar-plus me-1"></i> Simpan Jadual</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Modal 2: Record Test Result -->
    <div wire:ignore.self class="modal fade" id="recordResultModal" tabindex="-1" aria-labelledby="recordResultModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header border-bottom">
            <h5 class="modal-title fw-bold" id="recordResultModalLabel">Rekod Hasil Ujian Penempatan</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <form wire:submit.prevent="recordTestResult">
            <div class="modal-body">
              <div class="mb-3">
                <label class="form-label fw-bold">Markah / Penilaian Tahap Tajwid <span class="text-danger">*</span></label>
                <select wire:model="readingScore" class="form-select">
                  <option value="">-- Pilih Tahap Penilaian --</option>
                  <option value="Sangat Baik">Sangat Baik (Lancar &amp; Fasih)</option>
                  <option value="Sederhana Baik">Sederhana Baik (Boleh Talaqqi Bersanad)</option>
                  <option value="Kurang Memuaskan">Kurang Memuaskan (Syor Pra Talaqqi)</option>
                  <option value="Lemah">Lemah / Merangkak (Syor Pra Talaqqi)</option>
                </select>
                @error('readingScore') <span class="text-danger small">{{ $message }}</span> @enderror
              </div>

              <div class="mb-3">
                <label class="form-label fw-bold">Program Disyorkan <span class="text-danger">*</span></label>
                <select wire:model="recommendedCourseId" class="form-select">
                  <option value="">-- Pilih Program Disyorkan --</option>
                  @foreach($courses->whereIn('code', ['Pra KuTAB', 'KuTAB']) as $c)
                    <option value="{{ $c->id }}">{{ $c->code }} - {{ $c->name }}</option>
                  @endforeach
                </select>
                @error('recommendedCourseId') <span class="text-danger small">{{ $message }}</span> @enderror
              </div>

              <div class="mb-3">
                <label class="form-label fw-bold">Nota &amp; Ulasan Ujian</label>
                <textarea wire:model="testNotes" class="form-control" rows="3" placeholder="Masukkan ulasan makhraj huruf, hukum tajwid atau pencapaian pelajar..."></textarea>
              </div>
            </div>
            <div class="modal-footer border-top">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
              <button type="submit" class="btn btn-success" data-bs-dismiss="modal"><i class="ti tabler-check me-1"></i> Simpan &amp; Hantar Tawaran</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Modal 3: Edit Application -->
    <div wire:ignore.self class="modal fade" id="editApplicationModal" tabindex="-1" aria-labelledby="editApplicationModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header border-bottom">
            <h5 class="modal-title fw-bold" id="editApplicationModalLabel">Kemas Kini Permohonan Pelajar</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <form wire:submit.prevent="updateApplication">
            <div class="modal-body">
              <!-- Select Program -->
              <div class="mb-3">
                <label class="form-label fw-bold">Program Pengajian <span class="text-danger">*</span></label>
                <select wire:model.live="editCourseId" class="form-select">
                  @foreach($courses as $c)
                    <option value="{{ $c->id }}">{{ $c->code }} - {{ $c->name }}</option>
                  @endforeach
                </select>
                @error('editCourseId') <span class="text-danger small">{{ $message }}</span> @enderror
              </div>

              <!-- Conditional Fields depending on course code -->
              @php
                $selectedCourse = $courses->firstWhere('id', $editCourseId);
              @endphp

              @if($selectedCourse && $selectedCourse->code === 'KIBLAT')
                <div class="mb-3">
                  <label class="form-label fw-bold">Kategori Kiblat <span class="text-danger">*</span></label>
                  <select wire:model="editKiblatCategory" class="form-select">
                    <option value="">-- Pilih Kategori --</option>
                    <option value="A">Kategori A (Yuran RM1000)</option>
                    <option value="B">Kategori B (Yuran RM1200)</option>
                  </select>
                  @error('editKiblatCategory') <span class="text-danger small">{{ $message }}</span> @enderror
                </div>
              @endif

              @if($selectedCourse && in_array($selectedCourse->code, ['Pra KuTAB', 'KuTAB']))
                <div class="mb-3">
                  <label class="form-label fw-bold">Tahap Pembacaan Al-Quran <span class="text-danger">*</span></label>
                  <select wire:model="editReadingLevel" class="form-select">
                    <option value="">-- Pilih Tahap --</option>
                    <option value="Sangat Baik">Sangat Baik (Lancar &amp; Fasih)</option>
                    <option value="Sederhana Baik">Sederhana Baik (Boleh Talaqqi Bersanad)</option>
                    <option value="Kurang Memuaskan">Kurang Memuaskan (Syor Pra Talaqqi)</option>
                    <option value="Lemah">Lemah / Merangkak (Syor Pra Talaqqi)</option>
                  </select>
                  @error('editReadingLevel') <span class="text-danger small">{{ $message }}</span> @enderror
                </div>
              @endif

              <!-- Select Status -->
              <div class="mb-3">
                <label class="form-label fw-bold">Status Permohonan <span class="text-danger">*</span></label>
                <select wire:model="editStatus" class="form-select">
                  <option value="pending">Menunggu Semakan</option>
                  <option value="under_review">Dalam Semakan</option>
                  <option value="test_scheduled">Ujian Dijadual</option>
                  <option value="offered">Tawaran Diberi</option>
                  <option value="approved">Tawaran Diterima</option>
                  <option value="rejected">Ditolak</option>
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

  </div>
</div>
