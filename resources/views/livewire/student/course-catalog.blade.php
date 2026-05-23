@push('page-style')
  @vite('resources/assets/vendor/scss/pages/app-academy.scss')
@endpush

<div class="app-academy">
  @if (session()->has('success'))
    <div class="alert alert-success alert-dismissible mb-4">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
  @endif

  <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
      <h4 class="mb-1 text-heading">{{ __('sispaq.courses.title') }}</h4>
      <p class="mb-0 text-body-secondary">{{ __('sispaq.courses.subtitle') }}</p>
    </div>
  </div>

  <ul class="nav nav-pills mb-4 flex-wrap gap-2">
    <li class="nav-item">
      <button type="button" class="nav-link {{ $tab === 'mine' ? 'active' : '' }}" wire:click="$set('tab', 'mine')">
        {{ __('sispaq.courses.my_courses') }} ({{ $enrollments->count() }})
      </button>
    </li>
    <li class="nav-item">
      <button type="button" class="nav-link {{ $tab === 'explore' ? 'active' : '' }}" wire:click="$set('tab', 'explore')">
        {{ __('sispaq.courses.explore') }}
      </button>
    </li>
  </ul>

  @if($tab === 'mine')
    <div class="card mb-4">
      <div class="card-header d-flex flex-wrap justify-content-between gap-3">
        <div>
          <h5 class="mb-0">{{ __('sispaq.courses.enrollment_list') }}</h5>
          <p class="mb-0 text-body-secondary">{{ __('sispaq.courses.enrollments_count', ['count' => $enrollments->count()]) }}</p>
        </div>
        <select wire:model.live="filter" class="form-select w-auto">
          <option value="all">{{ __('sispaq.common.all') }}</option>
          <option value="active">{{ __('sispaq.common.active') }}</option>
          <option value="completed">{{ __('sispaq.common.completed') }}</option>
        </select>
      </div>
      <div class="card-body">
        <div class="row gy-4">
          @forelse($enrollments as $enroll)
            @php
              $pct = $this->moduleProgress($enroll);
              $att = $this->getAttendanceRate($enroll->id);
            @endphp
            <div class="col-sm-6 col-lg-4">
              <div class="card p-2 h-100 shadow-none border">
                @include('livewire.student.partials.course-cover', ['course' => $enroll->course])
                <div class="card-body p-4 pt-2">
                  <div class="d-flex justify-content-between mb-2">
                    <span class="badge bg-label-primary">{{ $enroll->course->code }}</span>
                    <x-sispaq.status-badge :status="$enroll->status" type="enrollment" />
                  </div>
                  <a href="{{ route('student.course.detail', $enroll->id) }}" class="h5 d-block mb-2">{{ $enroll->course->name }}</a>
                  <p class="small text-body-secondary mb-2">{{ __('sispaq.courses.module_attendance', ['module' => $enroll->current_module, 'rate' => $att]) }}</p>
                  <div class="progress mb-3" style="height: 8px">
                    <div class="progress-bar" style="width: {{ $pct }}%"></div>
                  </div>
                  <div class="d-flex gap-2">
                    <a href="{{ route('student.course.detail', $enroll->id) }}" class="btn btn-label-primary flex-grow-1 btn-sm">{{ __('sispaq.courses.continue') }}</a>
                    @if($enroll->course->isTalaqqi())
                      <a href="{{ route('student.talaqqi.book') }}" class="btn btn-primary btn-sm">{{ __('sispaq.courses.talaqqi') }}</a>
                    @endif
                  </div>
                </div>
              </div>
            </div>
          @empty
            <div class="col-12 text-center py-5">
              <x-sispaq.empty-state
                icon="tabler-book-off"
                :message="__('sispaq.courses.no_courses')"
                :action-label="__('sispaq.courses.explore_courses')"
                :action-href="route('student.courses', ['tab' => 'explore'])"
              />
            </div>
          @endforelse
        </div>
      </div>
    </div>
  @else
    @if($applications->isNotEmpty())
      <div class="alert alert-warning d-flex align-items-center gap-2 mb-4 py-3">
        <i class="ti tabler-clipboard-list flex-shrink-0"></i>
        <span class="small mb-0">
          <strong>{{ __('sispaq.courses.active_applications', ['count' => $applications->count()]) }}</strong>
          {{ __('sispaq.courses.active_applications_hint') }}
        </span>
      </div>
    @endif

    <div class="card mb-4">
      <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
        <div>
          <h5 class="mb-0">{{ __('sispaq.courses.filter_search') }}</h5>
          <p class="mb-0 text-body-secondary">{{ __('sispaq.courses.filter_subtitle') }}</p>
        </div>
        @if($this->hasActiveCatalogFilters)
          <button type="button" wire:click="resetCatalogFilters" class="btn btn-sm btn-label-secondary">
            <i class="ti tabler-filter-off me-1"></i> {{ __('sispaq.common.reset') }}
          </button>
        @endif
      </div>
      <div class="card-body">
        <div class="row g-3 mb-2">
          <div class="col-12">
            <label class="form-label small text-body-secondary mb-1">{{ __('sispaq.courses.search') }}</label>
            <div class="input-group">
              <span class="input-group-text"><i class="ti tabler-search"></i></span>
              <input type="search" wire:model.live.debounce.300ms="search" placeholder="{{ __('sispaq.courses.search_placeholder') }}" class="form-control" />
            </div>
          </div>
          <div class="col-md-6 col-lg-4">
            <label class="form-label small text-body-secondary mb-1">{{ __('sispaq.courses.filter_status') }}</label>
            <select wire:model.live="catalogStatus" class="form-select">
              <option value="all">{{ __('sispaq.courses.status_all') }}</option>
              <option value="open">{{ __('sispaq.courses.status_open') }}</option>
              <option value="applied">{{ __('sispaq.courses.status_applied') }}</option>
              <option value="enrolled">{{ __('sispaq.courses.status_enrolled') }}</option>
            </select>
          </div>
          <div class="col-md-6 col-lg-4">
            <label class="form-label small text-body-secondary mb-1">{{ __('sispaq.courses.filter_category') }}</label>
            <select wire:model.live="programType" class="form-select">
              <option value="all">{{ __('sispaq.courses.category_all') }}</option>
              <option value="quran_hadith">{{ __('sispaq.courses.category_quran_hadith') }}</option>
              <option value="bahasa">{{ __('sispaq.courses.category_bahasa') }}</option>
              <option value="talaqqi">{{ __('sispaq.courses.category_talaqqi') }}</option>
              <option value="perubatan">{{ __('sispaq.courses.category_perubatan') }}</option>
              <option value="falak">{{ __('sispaq.courses.category_falak') }}</option>
            </select>
          </div>
          <div class="col-md-6 col-lg-4">
            <label class="form-label small text-body-secondary mb-1">{{ __('sispaq.courses.filter_modules') }}</label>
            <select wire:model.live="moduleRange" class="form-select">
              <option value="all">{{ __('sispaq.common.all') }}</option>
              <option value="short">{{ __('sispaq.courses.modules_short') }}</option>
              <option value="medium">{{ __('sispaq.courses.modules_medium') }}</option>
              <option value="long">{{ __('sispaq.courses.modules_long') }}</option>
            </select>
          </div>
          <div class="col-md-6 col-lg-4">
            <label class="form-label small text-body-secondary mb-1">{{ __('sispaq.courses.filter_schedule') }}</label>
            <select wire:model.live="scheduleType" class="form-select">
              <option value="all">{{ __('sispaq.courses.schedule_all') }}</option>
              <option value="weekend">{{ __('sispaq.courses.schedule_weekend') }}</option>
              <option value="talaqqi">{{ __('sispaq.courses.schedule_talaqqi') }}</option>
              <option value="flexible">{{ __('sispaq.courses.schedule_flexible') }}</option>
            </select>
          </div>
          <div class="col-md-6 col-lg-4">
            <label class="form-label small text-body-secondary mb-1">{{ __('sispaq.courses.filter_placement') }}</label>
            <select wire:model.live="placementFilter" class="form-select">
              <option value="all">{{ __('sispaq.courses.placement_all') }}</option>
              <option value="required">{{ __('sispaq.courses.placement_required') }}</option>
              <option value="none">{{ __('sispaq.courses.placement_none') }}</option>
            </select>
          </div>
          <div class="col-md-6 col-lg-4">
            <label class="form-label small text-body-secondary mb-1">{{ __('sispaq.courses.filter_fee') }}</label>
            <select wire:model.live="feeRange" class="form-select">
              <option value="all">{{ __('sispaq.courses.fee_all') }}</option>
              <option value="under_600">{{ __('sispaq.courses.fee_under_600') }}</option>
              <option value="600_900">{{ __('sispaq.courses.fee_600_900') }}</option>
              <option value="over_900">{{ __('sispaq.courses.fee_over_900') }}</option>
            </select>
          </div>
          <div class="col-md-6 col-lg-4">
            <label class="form-label small text-body-secondary mb-1">{{ __('sispaq.courses.filter_sort') }}</label>
            <select wire:model.live="sortBy" class="form-select">
              <option value="code_asc">{{ __('sispaq.courses.sort_code_asc') }}</option>
              <option value="code_desc">{{ __('sispaq.courses.sort_code_desc') }}</option>
              <option value="name_asc">{{ __('sispaq.courses.sort_name_asc') }}</option>
              <option value="name_desc">{{ __('sispaq.courses.sort_name_desc') }}</option>
              <option value="modules_asc">{{ __('sispaq.courses.sort_modules_asc') }}</option>
              <option value="modules_desc">{{ __('sispaq.courses.sort_modules_desc') }}</option>
              <option value="fee_asc">{{ __('sispaq.courses.sort_fee_asc') }}</option>
              <option value="fee_desc">{{ __('sispaq.courses.sort_fee_desc') }}</option>
            </select>
          </div>
        </div>
      </div>
    </div>

    <div class="card mb-4">
      <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
        <div>
          <h5 class="mb-0">{{ __('sispaq.courses.course_list') }}</h5>
          <p class="mb-0 text-body-secondary">{{ __('sispaq.courses.programs_count', ['shown' => $this->filteredCourses->count(), 'total' => $courses->count()]) }}</p>
        </div>
      </div>
      <div class="card-body">
        <div class="row gy-4">
          @forelse($this->filteredCourses as $course)
            @php
              $status = $this->courseAvailability($course);
              $application = $status === 'applied' ? $this->applicationForCourse($course->id) : null;
            @endphp
            <div class="col-sm-6 col-lg-4">
              <div class="card p-2 h-100 shadow-none border {{ $application ? 'border-warning' : '' }}">
                @include('livewire.student.partials.course-cover', ['course' => $course])
                <div class="card-body p-3 pt-2 d-flex flex-column">
                  <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="badge bg-label-primary">{{ $course->code }}</span>
                    @if($application)
                      <x-sispaq.status-badge :status="$application->status" type="application" />
                    @else
                      <small class="text-body-secondary">{{ $course->total_modules }} {{ __('sispaq.common.modules') }}</small>
                    @endif
                  </div>
                  <h6 class="mb-1">{{ $course->name }}</h6>
                  <p class="small text-body-secondary mb-2">{{ \Illuminate\Support\Str::limit($course->description ?? '', 80) }}</p>
                  <p class="small mb-3"><i class="ti tabler-clock me-1"></i>{{ $course->schedule_day_time ?? __('sispaq.courses.talaqqi_schedule') }}</p>
                  <div class="mt-auto">
                    @if($status === 'enrolled')
                      <button type="button" wire:click="$set('tab', 'mine')" class="btn btn-label-success w-100 btn-sm">{{ __('sispaq.courses.already_enrolled') }}</button>
                    @elseif($application)
                      <button type="button" wire:click="openApplication({{ $application->id }})" class="btn btn-label-warning w-100 btn-sm">
                        <i class="ti tabler-eye me-1"></i> {{ __('sispaq.courses.view_application') }}
                      </button>
                    @else
                      <a href="{{ route('student.apply', $course->id) }}" class="btn btn-primary w-100 btn-sm">
                        <span>{{ __('sispaq.courses.apply_now') }}</span><i class="ti tabler-chevron-right ms-1"></i>
                      </a>
                    @endif
                  </div>
                </div>
              </div>
            </div>
          @empty
            <div class="col-12 text-center py-5">
              <x-sispaq.empty-state icon="tabler-filter-off" :message="__('sispaq.courses.no_match')" />
              <button type="button" wire:click="resetCatalogFilters" class="btn btn-sm btn-label-primary mt-3">
                <i class="ti tabler-filter-off me-1"></i> {{ __('sispaq.common.reset_filters') }}
              </button>
            </div>
          @endforelse
        </div>
      </div>
    </div>

    @if($this->viewingApplication)
      @php $app = $this->viewingApplication; @endphp
      <div class="modal fade show d-block" tabindex="-1" role="dialog" aria-modal="true" style="background-color: rgba(0,0,0,.5);" wire:click="closeApplication">
        <div class="modal-dialog modal-dialog-centered" wire:click.stop>
          <div class="modal-content">
            <div class="modal-header border-bottom">
              <div>
                <h5 class="modal-title mb-1">{{ __('sispaq.courses.application_modal_title') }}</h5>
                <p class="mb-0 small text-body-secondary">{{ $app->course->code }} — {{ $app->course->name }}</p>
              </div>
              <button type="button" class="btn-close" wire:click="closeApplication" aria-label="{{ __('sispaq.common.close') }}"></button>
            </div>
            <div class="modal-body">
              <div class="d-flex justify-content-between align-items-center mb-4">
                <span class="text-body-secondary small">{{ __('sispaq.courses.application_status') }}</span>
                <x-sispaq.status-badge :status="$app->status" type="application" />
              </div>

              <ul class="list-unstyled mb-0">
                <li class="d-flex justify-content-between py-2 border-bottom">
                  <span class="text-body-secondary small">{{ __('sispaq.courses.applied_at') }}</span>
                  <span class="small fw-medium">{{ $app->created_at->format('d/m/Y H:i') }}</span>
                </li>
                @if($app->kiblat_category)
                  <li class="d-flex justify-content-between py-2 border-bottom">
                    <span class="text-body-secondary small">{{ __('sispaq.courses.kiblat_category') }}</span>
                    <span class="small fw-medium">{{ $app->kiblat_category }}</span>
                  </li>
                @endif
                @if($app->reading_level)
                  <li class="d-flex justify-content-between py-2 border-bottom">
                    <span class="text-body-secondary small">{{ __('sispaq.courses.reading_level') }}</span>
                    <span class="small fw-medium">{{ $app->reading_level }}</span>
                  </li>
                @endif
                <li class="d-flex justify-content-between py-2 border-bottom">
                  <span class="text-body-secondary small">{{ __('sispaq.courses.placement_test') }}</span>
                  <span class="small fw-medium text-end">
                    @if($app->placementTest)
                      {{ date('d/m/Y H:i', strtotime($app->placementTest->test_date_time)) }}
                      @if($app->placementTest->examiner)
                        <br><span class="text-body-secondary">{{ $app->placementTest->examiner->name }}</span>
                      @endif
                    @else
                      {{ __('sispaq.courses.placement_not_scheduled') }}
                    @endif
                  </span>
                </li>
                @if($app->placementTest?->status)
                  <li class="d-flex justify-content-between py-2 border-bottom">
                    <span class="text-body-secondary small">{{ __('sispaq.courses.test_status') }}</span>
                    <span class="badge bg-label-info">{{ ucfirst($app->placementTest->status) }}</span>
                  </li>
                @endif
              </ul>

              @if($app->status === 'offered')
                <div class="alert alert-success mt-4 mb-0">
                  <p class="small mb-0">{{ __('sispaq.courses.offer_congrats') }}</p>
                </div>
              @elseif(in_array($app->status, ['pending', 'under_review'], true))
                <div class="alert alert-secondary mt-4 mb-0 small">{{ __('sispaq.courses.offer_pending') }}</div>
              @elseif($app->status === 'test_scheduled')
                <div class="alert alert-primary mt-4 mb-0 small">{{ __('sispaq.courses.offer_test') }}</div>
              @endif
            </div>
            <div class="modal-footer border-top">
              <button type="button" class="btn btn-label-secondary" wire:click="closeApplication">{{ __('sispaq.common.close') }}</button>
              @if($app->status === 'offered')
                <button type="button" wire:click="acceptOffer({{ $app->id }})" class="btn btn-success">
                  <i class="ti tabler-check me-1"></i> {{ __('sispaq.courses.accept_offer') }}
                </button>
              @endif
            </div>
          </div>
        </div>
      </div>
    @endif
  @endif
</div>
