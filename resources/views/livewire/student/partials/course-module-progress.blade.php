@props(['enrollment', 'academicRecords', 'fillHeight' => false])

<div class="card {{ $fillHeight ? 'h-100 mb-0' : 'mb-4' }}">
  <div class="card-body d-flex flex-column {{ $fillHeight ? 'h-100' : '' }}">
    <h6 class="mb-3">Kemajuan Modul</h6>
    <div class="d-flex justify-content-between mb-2">
      <span class="text-body-secondary small">Modul {{ $enrollment->current_module }} / {{ $enrollment->course->total_modules }}</span>
      <span class="fw-bold">{{ $this->moduleProgress() }}%</span>
    </div>
    <div class="progress mb-4" style="height: 10px">
      <div class="progress-bar" style="width: {{ $this->moduleProgress() }}%"></div>
    </div>
    <div class="{{ $fillHeight ? 'flex-grow-1 overflow-auto' : 'overflow-auto' }}" @if(!$fillHeight) style="max-height: 280px" @endif>
      @for($m = 1; $m <= $enrollment->course->total_modules; $m++)
        @php
          $rec = $academicRecords->firstWhere('module_number', $m);
          $state = $m < $enrollment->current_module ? 'done' : ($m == $enrollment->current_module ? 'current' : 'pending');
        @endphp
        <div class="d-flex align-items-center gap-2 py-2 border-bottom">
          <span class="avatar avatar-xs">
            <span class="avatar-initial rounded bg-label-{{ $state === 'done' ? 'success' : ($state === 'current' ? 'primary' : 'secondary') }}">
              @if($state === 'done')<i class="ti tabler-check ti-xs"></i>
              @elseif($state === 'current')<i class="ti tabler-player-play ti-xs"></i>
              @else<span class="small">{{ $m }}</span>@endif
            </span>
          </span>
          <div class="flex-grow-1">
            <span class="small fw-medium">Modul {{ $m }}</span>
          </div>
          @if($rec)
            <span class="badge bg-label-{{ $rec->status === 'lulus' ? 'success' : 'danger' }}">{{ $rec->marks }}%</span>
          @else
            <span class="badge bg-label-secondary">—</span>
          @endif
        </div>
      @endfor
    </div>
  </div>
</div>
