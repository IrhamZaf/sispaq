<div>
  <!-- Page Header -->
  <x-sispaq.page-header 
    title="Senarai Pensyarah" 
    subtitle="Urus rekod pendaftaran, butiran peribadi, dan akses sistem bagi tenaga pengajar (guru/ustaz)." 
    icon="tabler-users" 
    :breadcrumb="[
      ['label' => 'Halaman Pentadbir', 'url' => route('admin.dashboard')],
      ['label' => 'Senarai Pensyarah']
    ]"
  />

  @if (session()->has('success'))
    <div class="alert alert-success border-0 shadow-sm mb-4 alert-dismissible">
      {{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  @endif

  <!-- Lecturers Table Card -->
  <div class="card shadow-sm border-0">
    <div class="card-body p-0">
      <!-- Secondary Action Bar -->
      <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 p-3 border-bottom">
        <div class="d-flex align-items-center">
          <div class="input-group input-group-merge" style="width: 250px;">
            <span class="input-group-text"><i class="ti tabler-search text-muted"></i></span>
            <input type="text" wire:model.live.debounce.300ms="search" class="form-control" placeholder="Cari Pensyarah..." />
          </div>
        </div>
        <div class="d-flex align-items-center gap-2">
          <select wire:model.live="perPage" class="form-select w-auto">
            <option value="7">7</option>
            <option value="10">10</option>
            <option value="25">25</option>
            <option value="50">50</option>
          </select>
          
          <button wire:click="openCreateModal" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#saveLecturerModal">
            <i class="ti tabler-plus me-1"></i> Tambah Pensyarah
          </button>
        </div>
      </div>

      <div class="table-responsive">
        <table class="table table-hover table-striped align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th>Nama Pensyarah</th>
              <th>No. IC</th>
              <th>No. Telefon</th>
              <th>Alamat Kediaman</th>
              <th class="text-end" style="width: 120px; padding-right: 1.5rem;">Actions</th>
            </tr>
          </thead>
          <tbody>
            @forelse($lecturers as $l)
              <tr>
                <td>
                  <div class="d-flex align-items-center">
                    <div class="avatar avatar-md me-3">
                      <span class="avatar-initial rounded-circle bg-label-primary fw-bold">{{ substr($l->name, 0, 2) }}</span>
                    </div>
                    <div>
                      <strong class="d-block text-dark">{{ $l->name }}</strong>
                      <small class="text-muted d-block">{{ $l->email }}</small>
                    </div>
                  </div>
                </td>
                <td class="font-monospace text-dark">{{ $l->ic_number }}</td>
                <td>{{ $l->phone }}</td>
                <td class="small text-muted" style="max-width: 250px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $l->address }}">
                  {{ $l->address ?? '—' }}
                </td>
                <td class="text-end">
                  <div class="d-flex align-items-center justify-content-end gap-1 pe-3">
                    <!-- Edit button -->
                    <button wire:click="openEditModal({{ $l->id }})" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#saveLecturerModal" title="Kemas Kini Pensyarah">
                      <i class="ti tabler-edit"></i>
                    </button>
                    
                    <!-- Delete button -->
                    <button onclick="confirm('Adakah anda pasti mahu memadam akaun pensyarah ini?') || event.stopImmediatePropagation()" wire:click="deleteLecturer({{ $l->id }})" class="btn btn-sm btn-danger" title="Padam Pensyarah">
                      <i class="ti tabler-trash"></i>
                    </button>
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="5" class="text-center py-5 text-muted">
                  <i class="ti tabler-user-x fs-1 d-block mb-3 text-secondary"></i>
                  Tiada rekod pensyarah ditemui.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      <!-- Table Pagination Footer -->
      <div class="card-footer d-flex justify-content-between align-items-center flex-wrap gap-2 border-top py-3">
        <div class="small text-muted">
          Menunjukkan {{ $lecturers->firstItem() ?? 0 }} hingga {{ $lecturers->lastItem() ?? 0 }} daripada {{ $lecturers->total() ?? 0 }} pensyarah
        </div>
        <div>
          {{ $lecturers->links() }}
        </div>
      </div>
    </div>
  </div>

  <!-- Modal: Save Lecturer -->
  <div wire:ignore.self class="modal fade" id="saveLecturerModal" tabindex="-1" aria-labelledby="saveLecturerModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header border-bottom">
          <h5 class="modal-title fw-bold" id="saveLecturerModalLabel">
            {{ $userId ? 'Kemas Kini Akaun Pensyarah' : 'Daftar Pensyarah Baharu' }}
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form wire:submit.prevent="saveLecturer">
          <div class="modal-body">
            <!-- Name -->
            <div class="mb-3">
              <label class="form-label fw-bold">Nama Penuh <span class="text-danger">*</span></label>
              <input type="text" wire:model="name" class="form-control" placeholder="cth. Ustaz Ali Bin Ahmad" required>
              @error('name') <span class="text-danger small">{{ $message }}</span> @enderror
            </div>

            <!-- Email -->
            <div class="mb-3">
              <label class="form-label fw-bold">Alamat E-mel <span class="text-danger">*</span></label>
              <input type="email" wire:model="email" class="form-control" placeholder="cth. ali@gmail.com" required>
              @error('email') <span class="text-danger small">{{ $message }}</span> @enderror
            </div>

            <!-- Password -->
            <div class="mb-3">
              <label class="form-label fw-bold">Kata Laluan {!! $userId ? '<span class="text-muted small">(Biarkan kosong jika tiada perubahan)</span>' : '<span class="text-danger">*</span>' !!}</label>
              <input type="password" wire:model="password" class="form-control" placeholder="Min. 6 aksara" {{ $userId ? '' : 'required' }}>
              @error('password') <span class="text-danger small">{{ $message }}</span> @enderror
            </div>

            <div class="row">
              <!-- IC number -->
              <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">No. Kad Pengenalan <span class="text-danger">*</span></label>
                <input type="text" wire:model="ic_number" class="form-control" placeholder="cth. 800101-14-5555" required>
                @error('ic_number') <span class="text-danger small">{{ $message }}</span> @enderror
              </div>

              <!-- Phone -->
              <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">No. Telefon <span class="text-danger">*</span></label>
                <input type="text" wire:model="phone" class="form-control" placeholder="cth. 012-3456789" required>
                @error('phone') <span class="text-danger small">{{ $message }}</span> @enderror
              </div>
            </div>

            <!-- Address -->
            <div class="mb-3">
              <label class="form-label fw-bold">Alamat Kediaman</label>
              <textarea wire:model="address" class="form-control" rows="3" placeholder="Masukkan alamat kediaman penuh..."></textarea>
              @error('address') <span class="text-danger small">{{ $message }}</span> @enderror
            </div>
          </div>
          <div class="modal-footer border-top">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-primary"><i class="ti tabler-device-floppy me-1"></i> Simpan</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    // Listen for dispatch event to close bootstrap modal
    const modalElement = document.getElementById('saveLecturerModal');
    const bsModal = new bootstrap.Modal(modalElement);
    
    Livewire.on('close-modal', () => {
      // Hide modal via javascript or bootstrap trigger
      const modalInstance = bootstrap.Modal.getInstance(modalElement);
      if(modalInstance) {
        modalInstance.hide();
      }
      // Force remove backdrops if stuck
      const backdrop = document.querySelector('.modal-backdrop');
      if (backdrop) {
        backdrop.remove();
      }
      document.body.style.overflow = 'auto';
    });
  });
</script>
