<div>
  <x-sispaq.page-header title="Ujian Penempatan" subtitle="Jadualkan ujian Pra KuTAB / KuTAB" icon="tabler-microphone" />

  @if (session()->has('success'))<div class="alert alert-success mb-4">{{ session('success') }}</div>@endif

  @if($selectedAppId)
    <div class="card mb-4">
      <div class="card-body">
        <form wire:submit.prevent="scheduleTest">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Guru Pemeriksa</label>
              <select wire:model="examinerId" class="form-select">
                @foreach($teachers as $t)<option value="{{ $t->id }}">{{ $t->name }}</option>@endforeach
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">Tarikh & Masa</label>
              <input type="datetime-local" wire:model="testDateTime" class="form-control">
            </div>
          </div>
          <button type="submit" class="btn btn-primary mt-3">Jadualkan</button>
          <button type="button" wire:click="$set('selectedAppId', null)" class="btn btn-outline-secondary mt-3 ms-2">Batal</button>
        </form>
      </div>
    </div>
  @endif

  <div class="card mb-4">
    <div class="card-header">Permohonan Perlu Ujian</div>
    <div class="card-body p-0">
      <table class="table mb-0">
        @foreach($pendingApps as $app)
          <tr>
            <td>{{ $app->user->name }}</td>
            <td>{{ $app->course->code }}</td>
            <td><button wire:click="selectAppForTest({{ $app->id }})" class="btn btn-sm btn-warning">Jadualkan</button></td>
          </tr>
        @endforeach
      </table>
    </div>
  </div>

  <div class="card">
    <div class="card-header">Semua Ujian</div>
    <div class="card-body p-0">
      <table class="table mb-0">
        <thead class="table-light"><tr><th>Pelajar</th><th>Guru</th><th>Tarikh</th><th>Status</th></tr></thead>
        <tbody>
          @foreach($tests as $t)
            <tr>
              <td>{{ $t->student->name }}</td>
              <td>{{ $t->examiner?->name }}</td>
              <td>{{ date('d/m/Y H:i', strtotime($t->test_date_time)) }}</td>
              <td>{{ $t->status }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>
