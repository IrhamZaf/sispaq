<?php

namespace App\Livewire\Student;

use App\Livewire\Concerns\CalculatesAttendance;
use App\Models\StudentEnrollment;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class MyAttendance extends Component
{
    use CalculatesAttendance;

    public $enrollments;

    public function mount()
    {
        $this->enrollments = StudentEnrollment::with(['course', 'attendances'])
            ->where('student_id', Auth::id())
            ->where('status', 'active')
            ->get();
    }

    public function render()
    {
        return view('livewire.student.my-attendance')
            ->layout('layouts.contentNavbarLayout');
    }
}
