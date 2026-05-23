<div>
  <x-sispaq.page-header title="Kehadiran" subtitle="Laporan peratus kehadiran pelajar" icon="tabler-chart-dots" />

  <div class="mb-4">
    <select wire:model.live="selectedCourseId" class="form-select w-auto">
      <option value="">Semua Kursus</option>
      @foreach($courses as $c)<option value="{{ $c->id }}">{{ $c->code }}</option>@endforeach
    </select>
  </div>

  <div class="card">
    <div class="card-body p-0">
      <table class="table mb-0">
        <thead class="table-light"><tr><th>Pelajar</th><th>Kursus</th><th>Kehadiran</th><th>Ambang</th></tr></thead>
        <tbody>
          @foreach($enrollments as $e)
            @php $rate = $this->getAttendanceRate($e->id); @endphp
            <tr>
              <td>{{ $e->student->name }}</td>
              <td>{{ $e->course->code }}</td>
              <td>
                <strong class="{{ $rate < $e->course->attendanceThreshold() ? 'text-danger' : 'text-success' }}">{{ $rate }}%</strong>
              </td>
              <td>{{ $e->course->attendanceThreshold() }}%</td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>
