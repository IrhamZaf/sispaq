<div>
  <x-sispaq.page-header title="Kelas & Jadual" subtitle="Gambaran semua jadual kelas" icon="tabler-calendar" />

  <div class="card">
    <div class="card-body p-0">
      <table class="table mb-0">
        <thead class="table-light"><tr><th>Kursus</th><th>Modul</th><th>Masa</th><th>Lokasi</th><th>Hybrid</th></tr></thead>
        <tbody>
          @foreach($schedules as $s)
            <tr>
              <td>{{ $s->course->code }}</td>
              <td>{{ $s->module_number }}</td>
              <td>{{ date('d/m/Y H:i', strtotime($s->schedule_datetime)) }}</td>
              <td>{{ $s->location }}</td>
              <td>{{ $s->is_hybrid ? 'Ya' : 'Tidak' }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>
