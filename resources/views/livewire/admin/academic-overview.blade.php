<div>
  <x-sispaq.page-header title="Akademik" subtitle="Gambaran markah semua pelajar" icon="tabler-school" />

  <div class="card">
    <div class="card-body p-0">
      <table class="table mb-0">
        <thead class="table-light"><tr><th>Pelajar</th><th>Kursus</th><th>Modul</th><th>Markah</th><th>Status</th><th>Guru</th></tr></thead>
        <tbody>
          @foreach($records as $r)
            <tr>
              <td>{{ $r->enrollment->student->name }}</td>
              <td>{{ $r->enrollment->course->code }}</td>
              <td>{{ $r->module_number }}</td>
              <td>{{ $r->marks }}</td>
              <td>{{ $r->status }}</td>
              <td>{{ $r->recorder?->name ?? '—' }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>
