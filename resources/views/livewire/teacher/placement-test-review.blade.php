<div>
  <x-sispaq.page-header title="Ujian Penempatan" subtitle="Rekod keputusan bacaan Al-Quran" icon="tabler-microphone" />

  @if (session()->has('success'))<div class="alert alert-success mb-4">{{ session('success') }}</div>@endif

  <div class="row">
    <div class="col-lg-7 mb-4">
      <div class="card">
        <div class="card-body p-0">
          <table class="table mb-0">
            <thead class="table-light"><tr><th>Pelajar</th><th>Kursus</th><th>Tarikh</th><th>Status</th><th></th></tr></thead>
            <tbody>
              @foreach($tests as $t)
                <tr>
                  <td>{{ $t->student->name }}</td>
                  <td>{{ $t->application->course->code }}</td>
                  <td>{{ date('d/m/Y H:i', strtotime($t->test_date_time)) }}</td>
                  <td>{{ $t->status }}</td>
                  <td><button wire:click="selectTest({{ $t->id }})" class="btn btn-xs btn-primary">Rekod</button></td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
    @if($selectedTestId)
      <div class="col-lg-5 mb-4">
        <div class="card">
          <div class="card-body">
            <h6 class="fw-bold mb-3">Keputusan Ujian</h6>
            <div class="mb-3">
              <label class="form-label">Markah Bacaan (0-100)</label>
              <input type="number" wire:model="readingScore" class="form-control" min="0" max="100">
            </div>
            <div class="mb-3">
              <label class="form-label">Kursus Disyorkan</label>
              <select wire:model="recommendedCourseId" class="form-select">
                @foreach($courses as $c)<option value="{{ $c->id }}">{{ $c->code }}</option>@endforeach
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label">Catatan</label>
              <textarea wire:model="testNotes" class="form-control" rows="3"></textarea>
            </div>
            <button wire:click="recordResult" class="btn btn-success">Simpan & Tawarkan</button>
          </div>
        </div>
      </div>
    @endif
  </div>
</div>
