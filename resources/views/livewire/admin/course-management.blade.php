<div>
  <x-sispaq.page-header title="Pengurusan Kursus" subtitle="8 program ikhtisas — paparan (baca sahaja)" icon="tabler-books" />

  <div class="card">
    <div class="card-body p-0">
      <table class="table mb-0">
        <thead class="table-light">
          <tr><th>Kod</th><th>Nama</th><th>Tenaga Pengajar</th><th>Modul</th><th>Yuran/Modul</th><th>Tempoh</th><th>Jadual</th></tr>
        </thead>
        <tbody>
          @foreach($courses as $c)
            <tr>
              <td><strong>{{ $c->code }}</strong></td>
              <td>{{ $c->name }}</td>
              <td>
                @if($c->teacher)
                  <div class="d-flex align-items-center">
                    <x-sispaq.user-avatar :name="$c->teacher->name" size="sm" class="me-2" />
                    <span>{{ $c->teacher->name }}</span>
                  </div>
                @else
                  <span class="text-muted small">Belum Ditentu</span>
                @endif
              </td>
              <td>{{ $c->total_modules }}</td>
              <td>
                @if($c->fee_per_module)
                  RM{{ number_format($c->fee_per_module, 0) }}
                @else
                  RM{{ number_format($c->kiblat_cat_a_fee, 0) }} (A) / RM{{ number_format($c->kiblat_cat_b_fee, 0) }} (B)
                @endif
              </td>
              <td>{{ $c->duration_per_module }}</td>
              <td class="small">{{ $c->schedule_day_time ?? 'Talaqqi' }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>
