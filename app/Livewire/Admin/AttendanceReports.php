<?php

namespace App\Livewire\Admin;

use App\Livewire\Concerns\CalculatesAttendance;
use App\Models\Course;
use App\Models\StudentEnrollment;
use Livewire\Component;

class AttendanceReports extends Component
{
    use CalculatesAttendance;

    public $courses;
    public $selectedCourseId = '';
    public $enrollments = [];

    public function mount()
    {
        $this->courses = Course::orderBy('code')->get();
        $this->loadReport();
    }

    public function updatedSelectedCourseId()
    {
        $this->loadReport();
    }

    public function loadReport()
    {
        $query = StudentEnrollment::with(['student', 'course', 'attendances'])
            ->where('status', 'active');
        if ($this->selectedCourseId) {
            $query->where('course_id', $this->selectedCourseId);
        }
        $this->enrollments = $query->get();
    }

    public function render()
    {
        return view('livewire.admin.attendance-reports')
            ->layout('layouts.contentNavbarLayout');
    }
}
