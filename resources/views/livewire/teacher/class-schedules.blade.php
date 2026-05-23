<div>
  <x-sispaq.page-header title="Jadual Kelas" subtitle="Jana sesi kelas baharu" icon="tabler-calendar-plus" />

  @if (session()->has('success'))<div class="alert alert-success mb-4">{{ session('success') }}</div>@endif

  <div class="card mb-4">
    <div class="card-body">
      <form wire:submit.prevent="createSchedule">
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label">Kursus</label>
            <select wire:model="selectedCourseId" class="form-select" required>
              <option value="">-- Pilih --</option>
              @foreach($courses as $c)<option value="{{ $c->id }}">{{ $c->code }}</option>@endforeach
            </select>
          </div>
          <div class="col-md-3">
            <label class="form-label">Modul</label>
            <input type="number" wire:model="selectedModule" class="form-control" min="1">
          </div>
          <div class="col-md-3">
            <label class="form-label">Hybrid</label>
            <div class="form-check form-switch mt-2">
              <input type="checkbox" wire:model="isHybrid" class="form-check-input">
            </div>
          </div>
          <div class="col-md-6">
            <label class="form-label">Tarikh & Masa</label>
            <input type="datetime-local" wire:model="classDatetime" class="form-control">
          </div>
          <div class="col-md-6">
            <label class="form-label">Lokasi</label>
            <input type="text" wire:model="classLocation" class="form-control">
          </div>
        </div>
        <button type="submit" class="btn btn-primary mt-3">Jana Jadual</button>
      </form>
    </div>
  </div>

  <div class="card">
    <div class="card-header"><h5 class="mb-0">Jadual Terkini</h5></div>
    <div class="card-body p-0">
      <table class="table mb-0">
        <thead class="table-light"><tr><th>Kursus</th><th>Modul</th><th>Masa</th><th>Lokasi</th></tr></thead>
        <tbody>
          @foreach($schedules as $s)
            <tr>
              <td>{{ $s->course->code }}</td>
              <td>{{ $s->module_number }}</td>
              <td>{{ date('d/m/Y H:i', strtotime($s->schedule_datetime)) }}</td>
              <td>{{ $s->location }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>
