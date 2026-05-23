<?php

namespace App\Livewire\Teacher;

use App\Models\Attendance;
use App\Models\ClassSchedule;
use App\Models\Course;
use App\Models\StudentEnrollment;
use Livewire\Component;

class ClassSchedules extends Component
{
    public $courses;
    public $schedules;
    public $selectedCourseId = '';
    public $selectedModule = 1;
    public $classDatetime = '';
    public $classLocation = 'Dewan Kuliah APIUM';
    public $isHybrid = false;

    public function mount()
    {
        $this->courses = Course::where('teacher_id', auth()->id())
            ->whereNotIn('code', ['Pra KuTAB', 'KuTAB'])
            ->orderBy('code')
            ->get();
        $this->classDatetime = date('Y-m-d\TH:i');
        $this->refreshSchedules();
    }

    public function refreshSchedules()
    {
        $this->schedules = ClassSchedule::with('course')
            ->orderByDesc('schedule_datetime')
            ->take(20)
            ->get();
    }

    public function createSchedule()
    {
        $this->validate([
            'selectedCourseId' => 'required|exists:courses,id',
            'selectedModule' => 'required|integer|min:1',
            'classDatetime' => 'required',
            'classLocation' => 'required|string',
        ]);

        $schedule = ClassSchedule::create([
            'course_id' => $this->selectedCourseId,
            'module_number' => $this->selectedModule,
            'schedule_datetime' => $this->classDatetime,
            'location' => $this->classLocation,
            'is_hybrid' => $this->isHybrid,
        ]);

        $students = StudentEnrollment::where('course_id', $this->selectedCourseId)
            ->where('status', 'active')
            ->get();

        foreach ($students as $student) {
            Attendance::create([
                'enrollment_id' => $student->id,
                'class_schedule_id' => $schedule->id,
                'status' => 'tidak_hadir',
                'recorded_at' => $schedule->schedule_datetime,
            ]);
        }

        session()->flash('success', 'Jadual kelas baru dijana.');
        $this->refreshSchedules();
    }

    public function render()
    {
        return view('livewire.teacher.class-schedules')
            ->layout('layouts.contentNavbarLayout');
    }
}
