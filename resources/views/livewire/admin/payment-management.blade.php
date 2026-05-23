<div>
  <x-sispaq.page-header title="Bayaran & Invois" subtitle="Pengurusan yuran modul (simulasi)" icon="tabler-credit-card" />

  @if (session()->has('success'))<div class="alert alert-success mb-4">{{ session('success') }}</div>@endif

  <div class="mb-4">
    <select wire:model.live="filter" class="form-select w-auto">
      <option value="all">Semua</option>
      <option value="pending">Belum Bayar</option>
      <option value="paid">Lunas</option>
    </select>
  </div>

  <div class="card">
    <div class="card-body p-0">
      <table class="table mb-0">
        <thead class="table-light"><tr><th>Pelajar</th><th>Kursus</th><th>Modul</th><th>Jumlah</th><th>Status</th><th></th></tr></thead>
        <tbody>
          @foreach($payments as $p)
            <tr>
              <td>{{ $p->student->name }}</td>
              <td>{{ $p->course->code }}</td>
              <td>{{ $p->module_number }}</td>
              <td>RM{{ number_format($p->amount, 2) }}</td>
              <td><x-sispaq.status-badge :status="$p->payment_status" type="payment" /></td>
              <td class="text-end">
                @if($p->payment_status === 'pending')
                  <button wire:click="markPaid({{ $p->id }})" class="btn btn-sm btn-success">Tandakan Lunas</button>
                @endif
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>
