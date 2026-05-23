<div>
  <x-sispaq.page-header title="Pelajar Saya" subtitle="Senarai pelajar mengikut kursus" icon="tabler-users" />

  <div class="mb-4">
    <select wire:model.live="selectedCourseId" class="form-select w-auto">
      <option value="">Semua Kursus</option>
      @foreach($courses as $c)<option value="{{ $c->id }}">{{ $c->code }}</option>@endforeach
    </select>
  </div>

  <div class="card mb-4">
    <div class="card-header"><h6 class="mb-0">Pelajar Berdaftar</h6></div>
    <div class="card-body p-0">
      <table class="table mb-0">
        <thead class="table-light"><tr><th>Nama</th><th>Kursus</th><th>Modul</th></tr></thead>
        <tbody>
          @forelse($enrollments as $e)
            <tr>
              <td>{{ $e->student->name }}</td>
              <td>{{ $e->course->code }}</td>
              <td>{{ $e->current_module }}</td>
            </tr>
          @empty
            <tr><td colspan="3" class="text-center text-muted py-4">Tiada pelajar.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  @if($talaqqiStudents->isNotEmpty())
    <div class="card">
      <div class="card-header"><h6 class="mb-0">Talaqqi Aktif</h6></div>
      <div class="card-body p-0">
        <table class="table mb-0">
          @foreach($talaqqiStudents as $b)
            <tr>
              <td>{{ $b->student->name }}</td>
              <td>{{ $b->enrollment->course->code }}</td>
              <td>{{ date('d/m/Y H:i', strtotime($b->booking_datetime)) }}</td>
            </tr>
          @endforeach
        </table>
      </div>
    </div>
  @endif
</div>
