<?php

namespace App\Livewire\Student;

use App\Models\ClassSchedule;
use App\Models\Course;
use App\Models\StudentEnrollment;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class MySchedule extends Component
{
    public $schedules;

    public $courses;

    public array $calendarEvents = [];

    public array $courseColorMap = [];

    private const LABEL_COLORS = ['primary', 'success', 'info', 'warning', 'danger', 'secondary'];

    public function mount(): void
    {
        $courseIds = StudentEnrollment::where('student_id', Auth::id())
            ->where('status', 'active')
            ->pluck('course_id');

        $this->courses = Course::whereIn('id', $courseIds)->orderBy('code')->get();

        $this->courseColorMap = $this->courses->values()->mapWithKeys(
            fn ($course, $index) => [$course->id => self::LABEL_COLORS[$index % count(self::LABEL_COLORS)]]
        )->all();

        $this->schedules = ClassSchedule::with('course')
            ->whereIn('course_id', $courseIds)
            ->orderBy('schedule_datetime')
            ->get();

        $this->calendarEvents = $this->buildCalendarEvents($this->schedules);
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

    public function render()
    {
        return view('livewire.student.my-schedule', [
            'courseColorMap' => $this->courseColorMap,
        ])->layout('layouts.contentNavbarLayout');
    }
}
