<div>
  <x-sispaq.page-header title="Pensijilan" subtitle="Sijil dikeluarkan — pratonton HTML" icon="tabler-certificate">
    <x-slot:actions>
      <a href="{{ route('verify.certificate', ['hash' => 'semak']) }}" class="btn btn-outline-primary btn-sm">Semakan Awam</a>
    </x-slot:actions>
  </x-sispaq.page-header>

  @if (session()->has('success'))<div class="alert alert-success mb-4">{{ session('success') }}</div>@endif

  <div class="row">
    <div class="col-lg-7 mb-4">
      <div class="card">
        <div class="card-body p-0">
          <table class="table mb-0">
            <thead class="table-light"><tr><th>No. Sijil</th><th>Pelajar</th><th>Kursus</th><th>Jenis</th><th></th></tr></thead>
            <tbody>
              @foreach($certificates as $c)
                <tr>
                  <td class="small font-monospace">{{ $c->certificate_number }}</td>
                  <td>{{ $c->student->name }}</td>
                  <td>{{ $c->course->code }}</td>
                  <td>{{ $c->certificate_type }}</td>
                  <td><button wire:click="preview({{ $c->id }})" class="btn btn-xs btn-primary">Pratonton</button></td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
    @if($previewCertificate)
      <div class="col-lg-5 mb-4">
        <div class="card border-primary">
          <div class="card-body text-center p-4">
            <h5 class="text-primary fw-bold">SIJIL SISPAQ</h5>
            <p class="mb-1">{{ $previewCertificate->student->name }}</p>
            <p class="mb-1">{{ $previewCertificate->course->name }}</p>
            <p class="small text-muted">{{ $previewCertificate->certificate_number }}</p>
            <p class="small">{{ date('d F Y', strtotime($previewCertificate->issue_date)) }}</p>
            <a href="{{ route('verify.certificate', ['hash' => $previewCertificate->qr_code_hash]) }}" class="btn btn-sm btn-outline-primary" target="_blank">Sahkan QR</a>
          </div>
        </div>
      </div>
    @endif
  </div>
</div>
