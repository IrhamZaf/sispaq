<?php

namespace App\Livewire\Student;

use App\Livewire\Concerns\AcceptsStudentOffer;
use App\Livewire\Concerns\CalculatesAttendance;
use App\Models\Course;
use App\Models\StudentApplication;
use App\Models\StudentEnrollment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Attributes\Url;
use Livewire\Component;

class CourseCatalog extends Component
{
    use AcceptsStudentOffer;
    use CalculatesAttendance;

    #[Url]
    public $tab = 'mine';

    public $courses;

    public $appliedCourseIds = [];

    public $enrolledCourseIds = [];

    public $search = '';

    public $catalogStatus = 'all';

    public $programType = 'all';

    public $moduleRange = 'all';

    public $scheduleType = 'all';

    public $placementFilter = 'all';

    public $feeRange = 'all';

    public $sortBy = 'code_asc';

    public $enrollments;

    public $applications;

    public $filter = 'all';

    public ?int $viewingApplicationId = null;

    public function mount(): void
    {
        if (! in_array($this->tab, ['mine', 'explore'], true)) {
            $this->tab = 'mine';
        }

        $this->loadCatalogData();
        $this->loadEnrollments();
        $this->loadApplications();
    }

    public function updatedFilter(): void
    {
        $this->loadEnrollments();
    }

    public function loadCatalogData(): void
    {
        $userId = Auth::id();
        $this->courses = Course::orderBy('code')->get();
        $this->appliedCourseIds = StudentApplication::where('user_id', $userId)
            ->whereNotIn('status', ['rejected'])
            ->pluck('course_id')
            ->toArray();
        $this->enrolledCourseIds = StudentEnrollment::where('student_id', $userId)
            ->pluck('course_id')
            ->toArray();
    }

    public function resetCatalogFilters(): void
    {
        $this->search = '';
        $this->catalogStatus = 'all';
        $this->programType = 'all';
        $this->moduleRange = 'all';
        $this->scheduleType = 'all';
        $this->placementFilter = 'all';
        $this->feeRange = 'all';
        $this->sortBy = 'code_asc';
    }

    public function courseAvailability(Course $course): string
    {
        if (in_array($course->id, $this->enrolledCourseIds, true)) {
            return 'enrolled';
        }

        if (in_array($course->id, $this->appliedCourseIds, true)) {
            return 'applied';
        }

        return 'open';
    }

    public function applicationForCourse(int $courseId): ?StudentApplication
    {
        return $this->applications->firstWhere('course_id', $courseId);
    }

    public function openApplication(int $applicationId): void
    {
        $exists = $this->applications->contains('id', $applicationId);
        if ($exists) {
            $this->viewingApplicationId = $applicationId;
        }
    }

    public function closeApplication(): void
    {
        $this->viewingApplicationId = null;
    }

    public function getViewingApplicationProperty(): ?StudentApplication
    {
        if (! $this->viewingApplicationId) {
            return null;
        }

        return $this->applications->firstWhere('id', $this->viewingApplicationId);
    }

    public function loadEnrollments(): void
    {
        $query = StudentEnrollment::with(['course', 'payments', 'academicRecords'])
            ->where('student_id', Auth::id());

        if ($this->filter === 'active') {
            $query->where('status', 'active');
        } elseif ($this->filter === 'completed') {
            $query->where('status', 'completed');
        }

        $this->enrollments = $query->orderByDesc('updated_at')->get();
    }

    public function loadApplications(): void
    {
        $this->applications = StudentApplication::with(['course', 'placementTest.examiner'])
            ->where('user_id', Auth::id())
            ->whereNotIn('status', ['rejected', 'approved'])
            ->orderByDesc('created_at')
            ->get();
    }

    public function getFilteredCoursesProperty()
    {
        $filtered = $this->courses->filter(function (Course $course) {
            if ($this->search !== '') {
                $q = Str::lower($this->search);
                $haystack = Str::lower(implode(' ', [
                    $course->code,
                    $course->name,
                    $course->description ?? '',
                    $course->requirements ?? '',
                    $course->schedule_day_time ?? '',
                ]));
                if (! str_contains($haystack, $q)) {
                    return false;
                }
            }

            $availability = $this->courseAvailability($course);
            if ($this->catalogStatus === 'open' && $availability !== 'open') {
                return false;
            }
            if ($this->catalogStatus === 'enrolled' && $availability !== 'enrolled') {
                return false;
            }
            if ($this->catalogStatus === 'applied' && $availability !== 'applied') {
                return false;
            }

            if ($this->programType !== 'all' && $course->programCategory() !== $this->programType) {
                return false;
            }

            $modules = (int) ($course->total_modules ?? 0);
            if ($this->moduleRange === 'short' && ($modules < 1 || $modules > 3)) {
                return false;
            }
            if ($this->moduleRange === 'medium' && ($modules < 4 || $modules > 6)) {
                return false;
            }
            if ($this->moduleRange === 'long' && $modules < 7) {
                return false;
            }

            $schedule = Str::lower($course->schedule_day_time ?? '');
            if ($this->scheduleType === 'weekend' && ! str_contains($schedule, 'sabtu')) {
                return false;
            }
            if ($this->scheduleType === 'talaqqi' && ! $course->isTalaqqi()) {
                return false;
            }
            if ($this->scheduleType === 'flexible' && ! str_contains($schedule, 'fleksibel') && ! str_contains($schedule, 'hujung')) {
                return false;
            }

            if ($this->placementFilter === 'required' && ! $course->requiresPlacementTest()) {
                return false;
            }
            if ($this->placementFilter === 'none' && $course->requiresPlacementTest()) {
                return false;
            }

            $fee = $course->displayFee();
            if ($this->feeRange === 'under_600' && ($fee === null || $fee >= 600)) {
                return false;
            }
            if ($this->feeRange === '600_900' && ($fee === null || $fee < 600 || $fee > 900)) {
                return false;
            }
            if ($this->feeRange === 'over_900' && ($fee === null || $fee <= 900)) {
                return false;
            }

            return true;
        });

        $filtered = $filtered->values();

        $key = fn (Course $course) => match ($this->sortBy) {
            'name_asc', 'name_desc' => Str::lower($course->name),
            'modules_asc', 'modules_desc' => $course->total_modules ?? 0,
            'fee_asc', 'fee_desc' => $course->displayFee() ?? 99999,
            default => Str::lower($course->code),
        };

        return str_ends_with($this->sortBy, '_desc')
            ? $filtered->sortByDesc($key)->values()
            : $filtered->sortBy($key)->values();
    }

    public function getHasActiveCatalogFiltersProperty(): bool
    {
        return $this->search !== ''
            || $this->catalogStatus !== 'all'
            || $this->programType !== 'all'
            || $this->moduleRange !== 'all'
            || $this->scheduleType !== 'all'
            || $this->placementFilter !== 'all'
            || $this->feeRange !== 'all'
            || $this->sortBy !== 'code_asc';
    }

    public function moduleProgress($enrollment): int
    {
        if ($enrollment->course->total_modules < 1) {
            return 0;
        }

        return (int) min(100, round(($enrollment->current_module / $enrollment->course->total_modules) * 100));
    }

    public function render()
    {
        return view('livewire.student.course-catalog')
            ->layout('layouts.contentNavbarLayout');
    }
}
