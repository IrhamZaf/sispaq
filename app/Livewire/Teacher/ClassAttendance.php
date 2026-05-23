<?php

namespace App\Livewire\Teacher;

use App\Models\Attendance;
use App\Models\ClassSchedule;
use App\Models\StudentEnrollment;
use Livewire\Component;

class ClassAttendance extends Component
{
    public $schedules;
    public $activeScheduleId = null;
    public $enrolledStudents = [];
    public $attendanceStatuses = [];
    public $qrPayload = '';

    public function mount()
    {
        $this->schedules = ClassSchedule::with('course')
            ->orderByDesc('schedule_datetime')
            ->take(15)
            ->get();
    }

    public function loadAttendanceForClass($scheduleId)
    {
        $this->activeScheduleId = $scheduleId;
        $schedule = ClassSchedule::findOrFail($scheduleId);
        $this->qrPayload = json_encode([
            'schedule_id' => $scheduleId,
            'course_id' => $schedule->course_id,
        ]);

        $this->enrolledStudents = StudentEnrollment::with('student')
            ->where('course_id', $schedule->course_id)
            ->where('status', 'active')
            ->get();

        foreach ($this->enrolledStudents as $enroll) {
            $att = Attendance::where('class_schedule_id', $scheduleId)
                ->where('enrollment_id', $enroll->id)
                ->first();
            $this->attendanceStatuses[$enroll->id] = $att ? $att->status : 'tidak_hadir';
        }
    }

    public function saveAttendance()
    {
        if (! $this->activeScheduleId) {
            return;
        }

        foreach ($this->attendanceStatuses as $enrollId => $status) {
            Attendance::updateOrCreate(
                [
                    'class_schedule_id' => $this->activeScheduleId,
                    'enrollment_id' => $enrollId,
                ],
                [
                    'status' => $status,
                    'recorded_at' => now(),
                ]
            );
        }

        session()->flash('success', 'Rekod kehadiran berjaya dikemas kini.');
        $this->activeScheduleId = null;
        $this->enrolledStudents = [];
    }

    public function render()
    {
        return view('livewire.teacher.class-attendance')
            ->layout('layouts.contentNavbarLayout');
    }
}
