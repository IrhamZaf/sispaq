<div>
  <x-sispaq.page-header title="Kehadiran Kelas" subtitle="Rekod kehadiran dan QR simulasi" icon="tabler-user-check" />

  @if (session()->has('success'))<div class="alert alert-success mb-4">{{ session('success') }}</div>@endif

  <div class="row">
    <div class="col-md-4 mb-4">
      <div class="card">
        <div class="card-header"><h6 class="mb-0">Pilih Sesi</h6></div>
        <ul class="list-group list-group-flush">
          @foreach($schedules as $s)
            <li class="list-group-item">
              <button wire:click="loadAttendanceForClass({{ $s->id }})" class="btn btn-sm btn-outline-primary w-100 text-start">
                {{ $s->course->code }} — {{ date('d/m/Y', strtotime($s->schedule_datetime)) }}
              </button>
            </li>
          @endforeach
        </ul>
      </div>
    </div>
    <div class="col-md-8 mb-4">
      @if($activeScheduleId)
        <div class="card">
          <div class="card-body">
            <div class="alert alert-secondary small font-monospace mb-3">QR Simulasi: {{ $qrPayload }}</div>
            @foreach($enrolledStudents as $enroll)
              <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                <span>{{ $enroll->student->name }}</span>
                <select wire:model="attendanceStatuses.{{ $enroll->id }}" class="form-select form-select-sm w-auto">
                  <option value="hadir">Hadir</option>
                  <option value="lewat">Lewat</option>
                  <option value="bersebab">Bersebab</option>
                  <option value="tidak_hadir">Tidak Hadir</option>
                </select>
              </div>
            @endforeach
            <button wire:click="saveAttendance" class="btn btn-primary">Simpan Kehadiran</button>
          </div>
        </div>
      @else
        <x-sispaq.empty-state message="Pilih sesi kelas untuk rekod kehadiran." />
      @endif
    </div>
  </div>
</div>
