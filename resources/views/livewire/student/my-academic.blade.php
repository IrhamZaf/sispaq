<div>
  <x-sispaq.page-header title="Akademik" subtitle="Markah, modul, dan kemajuan talaqqi" icon="tabler-school" />

  <div class="card mb-4">
    <div class="card-header"><h5 class="mb-0">Rekod Markah</h5></div>
    <div class="card-body p-0">
      <table class="table mb-0">
        <thead class="table-light"><tr><th>Kursus</th><th>Modul</th><th>Markah</th><th>Status</th></tr></thead>
        <tbody>
          @forelse($academicRecords as $r)
            <tr>
              <td>{{ $r->enrollment->course->code }}</td>
              <td>{{ $r->module_number }}</td>
              <td>{{ $r->marks }}/100</td>
              <td><span class="badge bg-label-{{ $r->status === 'lulus' ? 'success' : 'danger' }}">{{ strtoupper($r->status) }}</span></td>
            </tr>
          @empty
            <tr><td colspan="4" class="text-center text-muted py-4">Tiada rekod markah.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  @if($talaqqiBookings->isNotEmpty())
    <div class="card">
      <div class="card-header"><h5 class="mb-0">Kemajuan Talaqqi</h5></div>
      <div class="card-body p-0">
        <table class="table mb-0">
          <thead class="table-light"><tr><th>Kursus</th><th>Halaman</th><th>Status</th><th>Tarikh</th></tr></thead>
          <tbody>
            @foreach($talaqqiBookings->take(10) as $b)
              <tr>
                <td>{{ $b->enrollment->course->code }}</td>
                <td>m/s {{ $b->current_page }}</td>
                <td>{{ $b->session_status }}</td>
                <td>{{ date('d/m/Y', strtotime($b->booking_datetime)) }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  @endif
</div>
