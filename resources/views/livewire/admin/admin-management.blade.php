<div>
  <!-- Page Header -->
  <x-sispaq.page-header 
    title="Senarai Pentadbir" 
    subtitle="Urus rekod pendaftaran, butiran peribadi, dan kawalan akses sistem bagi pentadbir (u urusetia)." 
    icon="tabler-settings" 
    :breadcrumb="[
      ['label' => 'Halaman Pentadbir', 'url' => route('admin.dashboard')],
      ['label' => 'Senarai Pentadbir']
    ]"
  />

  @if (session()->has('success'))
    <div class="alert alert-success border-0 shadow-sm mb-4 alert-dismissible">
      {{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  @endif

  @if (session()->has('error'))
    <div class="alert alert-danger border-0 shadow-sm mb-4 alert-dismissible">
      {{ session('error') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  @endif

  <!-- Admins Table Card -->
  <div class="card shadow-sm border-0">
    <div class="card-body p-0">
      <!-- Secondary Action Bar -->
      <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 p-3 border-bottom">
        <div class="d-flex align-items-center">
          <div class="input-group input-group-merge" style="width: 250px;">
            <span class="input-group-text"><i class="ti tabler-search text-muted"></i></span>
            <input type="text" wire:model.live.debounce.300ms="search" class="form-control" placeholder="Cari Pentadbir..." />
          </div>
        </div>
        <div class="d-flex align-items-center gap-2">
          <select wire:model.live="perPage" class="form-select w-auto">
            <option value="7">7</option>
            <option value="10">10</option>
            <option value="25">25</option>
            <option value="50">50</option>
          </select>
          
          <button wire:click="openCreateModal" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#saveAdminModal">
            <i class="ti tabler-plus me-1"></i> Tambah Pentadbir
          </button>
        </div>
      </div>

      <div class="table-responsive">
        <table class="table table-hover table-striped align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th>Nama Pentadbir</th>
              <th>No. IC</th>
              <th>No. Telefon</th>
              <th>Alamat Kediaman</th>
              <th class="text-end" style="width: 120px; padding-right: 1.5rem;">Actions</th>
            </tr>
          </thead>
          <tbody>
            @forelse($admins as $a)
              <tr>
                <td>
                  <div class="d-flex align-items-center">
                    <div class="avatar avatar-md me-3">
                      <span class="avatar-initial rounded-circle bg-label-primary fw-bold">{{ substr($a->name, 0, 2) }}</span>
                    </div>
                    <div>
                      <strong class="d-block text-dark">
                        {{ $a->name }} 
                        @if($a->id === auth()->id())
                          <span class="badge bg-label-info ms-1 small">Anda</span>
                        @endif
                      </strong>
                      <small class="text-muted d-block">{{ $a->email }}</small>
                    </div>
                  </div>
                </td>
                <td class="font-monospace text-dark">{{ $a->ic_number }}</td>
                <td>{{ $a->phone }}</td>
                <td class="small text-muted" style="max-width: 250px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $a->address }}">
                  {{ $a->address ?? '—' }}
                </td>
                <td class="text-end">
                  <div class="d-flex align-items-center justify-content-end gap-1 pe-3">
                    <!-- Edit button -->
                    <button wire:click="openEditModal({{ $a->id }})" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#saveAdminModal" title="Kemas Kini Pentadbir">
                      <i class="ti tabler-edit"></i>
                    </button>
                    
                    <!-- Delete button -->
                    @if($a->id !== auth()->id())
                      <button onclick="confirm('Adakah anda pasti mahu memadam akaun pentadbir ini?') || event.stopImmediatePropagation()" wire:click="deleteAdmin({{ $a->id }})" class="btn btn-sm btn-danger" title="Padam Pentadbir">
                        <i class="ti tabler-trash"></i>
                      </button>
                    @else
                      <button class="btn btn-sm btn-danger disabled" title="Akaun anda sendiri tidak boleh dipadam" disabled>
                        <i class="ti tabler-trash"></i>
                      </button>
                    @endif
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="5" class="text-center py-5 text-muted">
                  <i class="ti tabler-user-x fs-1 d-block mb-3 text-secondary"></i>
                  Tiada rekod pentadbir ditemui.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      <!-- Table Pagination Footer -->
      <div class="card-footer d-flex justify-content-between align-items-center flex-wrap gap-2 border-top py-3">
        <div class="small text-muted">
          Menunjukkan {{ $admins->firstItem() ?? 0 }} hingga {{ $admins->lastItem() ?? 0 }} daripada {{ $admins->total() ?? 0 }} pentadbir
        </div>
        <div>
          {{ $admins->links() }}
        </div>
      </div>
    </div>
  </div>

  <!-- Modal: Save Admin -->
  <div wire:ignore.self class="modal fade" id="saveAdminModal" tabindex="-1" aria-labelledby="saveAdminModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header border-bottom">
          <h5 class="modal-title fw-bold" id="saveAdminModalLabel">
            {{ $userId ? 'Kemas Kini Akaun Pentadbir' : 'Daftar Pentadbir Baharu' }}
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form wire:submit.prevent="saveAdmin">
          <div class="modal-body">
            <!-- Name -->
            <div class="mb-3">
              <label class="form-label fw-bold">Nama Penuh <span class="text-danger">*</span></label>
              <input type="text" wire:model="name" class="form-control" placeholder="cth. Ahmad Bin Ali" required>
              @error('name') <span class="text-danger small">{{ $message }}</span> @enderror
            </div>

            <!-- Email -->
            <div class="mb-3">
              <label class="form-label fw-bold">Alamat E-mel <span class="text-danger">*</span></label>
              <input type="email" wire:model="email" class="form-control" placeholder="cth. ahmad@gmail.com" required>
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
                <input type="text" wire:model="ic_number" class="form-control" placeholder="cth. 850101-14-1234" required>
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
    const modalElement = document.getElementById('saveAdminModal');
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
