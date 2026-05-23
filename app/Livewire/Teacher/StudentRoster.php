<?php

namespace App\Livewire\Teacher;

use App\Models\Course;
use App\Models\StudentEnrollment;
use App\Models\TalaqqiBooking;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class StudentRoster extends Component
{
    public $courses;
    public $selectedCourseId = '';
    public $enrollments = [];
    public $talaqqiStudents = [];

    public function mount()
    {
        $this->courses = Course::orderBy('code')->get();
        $this->loadRoster();
    }

    public function updatedSelectedCourseId()
    {
        $this->loadRoster();
    }

    public function loadRoster()
    {
        $query = StudentEnrollment::with(['student', 'course'])
            ->where('status', 'active');

        if ($this->selectedCourseId) {
            $query->where('course_id', $this->selectedCourseId);
        }

        $this->enrollments = $query->get();

        $this->talaqqiStudents = TalaqqiBooking::with(['student', 'enrollment.course'])
            ->where('teacher_id', Auth::id())
            ->where('session_status', 'Belum Selesai')
            ->orderBy('booking_datetime')
            ->get();
    }

    public function render()
    {
        return view('livewire.teacher.student-roster')
            ->layout('layouts.contentNavbarLayout');
    }
}
