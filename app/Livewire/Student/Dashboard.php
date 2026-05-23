<?php

namespace App\Livewire\Student;

use App\Livewire\Concerns\CalculatesAttendance;
use App\Models\AcademicRecord;
use App\Models\ClassSchedule;
use App\Models\Course;
use App\Models\Payment;
use App\Models\StudentEnrollment;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Dashboard extends Component
{
    use CalculatesAttendance;

    public $activeEnrollment;

    public $enrollments;

    public $pendingPaymentsCount = 0;

    public $upcomingSchedules;

    public $attendanceRate = 100;

    public $completedModulesCount = 0;

    public $activeCoursesCount = 0;

    public $courseProgressItems = [];

    public $chartConfig = [];

    public array $calendarEvents = [];

    public $courses;

    public array $courseColorMap = [];

    private const LABEL_COLORS = ['primary', 'success', 'info', 'warning', 'danger', 'secondary'];

    public function mount(): void
    {
        $userId = Auth::id();

        $this->enrollments = StudentEnrollment::with(['course', 'academicRecords'])
            ->where('student_id', $userId)
            ->whereIn('status', ['active', 'completed'])
            ->orderByDesc('updated_at')
            ->get();

        $this->activeEnrollment = $this->enrollments->firstWhere('status', 'active')
            ?? $this->enrollments->first();

        $this->activeCoursesCount = $this->enrollments->where('status', 'active')->count();

        $this->completedModulesCount = AcademicRecord::whereHas('enrollment', fn ($q) => $q->where('student_id', $userId))
            ->where('status', 'lulus')
            ->count();

        $this->pendingPaymentsCount = Payment::where('student_id', $userId)
            ->where('payment_status', 'pending')
            ->count();

        $activeCourseIds = StudentEnrollment::where('student_id', $userId)
            ->where('status', 'active')
            ->pluck('course_id');

        $this->courses = Course::whereIn('id', $activeCourseIds)->orderBy('code')->get();

        $this->courseColorMap = $this->courses->values()->mapWithKeys(
            fn ($course, $index) => [$course->id => self::LABEL_COLORS[$index % count(self::LABEL_COLORS)]]
        )->all();

        $courseIds = $this->enrollments->pluck('course_id');

        $this->upcomingSchedules = ClassSchedule::with('course')
            ->whereIn('course_id', $courseIds)
            ->where('schedule_datetime', '>=', now())
            ->orderBy('schedule_datetime')
            ->limit(5)
            ->get();

        $this->calendarEvents = $this->buildCalendarEvents(
            ClassSchedule::with('course')
                ->whereIn('course_id', $activeCourseIds)
                ->where('schedule_datetime', '>=', now()->subMonth())
                ->orderBy('schedule_datetime')
                ->get()
        );

        if ($this->activeEnrollment) {
            $this->attendanceRate = $this->getAttendanceRate($this->activeEnrollment->id);
        }

        $colors = ['primary', 'success', 'info', 'warning', 'danger', 'secondary'];
        $this->courseProgressItems = $this->enrollments->take(4)->values()->map(function ($enroll, $i) use ($colors) {
            $pct = $this->moduleProgress($enroll);

            return [
                'id' => $enroll->id,
                'name' => $enroll->course->name,
                'code' => $enroll->course->code,
                'pct' => $pct,
                'label' => 'Modul ' . $enroll->current_module . '/' . max(1, $enroll->course->total_modules),
                'color' => $colors[$i % count($colors)],
            ];
        })->all();

        $barLabels = [];
        $barSeries = [];
        foreach ($this->enrollments->take(6) as $enroll) {
            $barLabels[] = $enroll->course->code;
            $barSeries[] = $this->moduleProgress($enroll);
        }
        if (empty($barLabels)) {
            $barLabels = ['Belum daftar'];
            $barSeries = [0];
        }

        $this->chartConfig = [
            'bar' => [
                'labels' => $barLabels,
                'series' => $barSeries,
            ],
        ];
    }

    public function buildCalendarEvents(Collection $schedules): array
    {
        return $schedules->map(function ($s) {
            return [
                'id' => 'class-' . $s->id,
                'title' => $s->course->code . ' M' . $s->module_number,
                'start' => date('c', strtotime($s->schedule_datetime)),
                'display' => 'block',
                'extendedProps' => [
                    'courseId' => $s->course_id,
                    'courseCode' => $s->course->code,
                    'location' => $s->location,
                    'calendar' => (string) $s->course_id,
                ],
            ];
        })->values()->all();
    }

    public function moduleProgress(StudentEnrollment $enrollment): int
    {
        $total = max(1, (int) $enrollment->course->total_modules);

        return (int) min(100, round(($enrollment->current_module / $total) * 100));
    }

    public function render()
    {
        return view('livewire.student.dashboard', [
            'courseColorMap' => $this->courseColorMap,
        ])->layout('layouts.contentNavbarLayout');
    }
}
