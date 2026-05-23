<?php

namespace App\Livewire\Student;

use App\Models\AcademicRecord;
use App\Models\StudentEnrollment;
use App\Models\TalaqqiBooking;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class MyAcademic extends Component
{
    public $enrollments;
    public $academicRecords;
    public $talaqqiBookings;

    public function mount()
    {
        $userId = Auth::id();
        $this->enrollments = StudentEnrollment::with('course')
            ->where('student_id', $userId)
            ->get();
        $this->academicRecords = AcademicRecord::with('enrollment.course')
            ->whereHas('enrollment', fn ($q) => $q->where('student_id', $userId))
            ->orderByDesc('module_number')
            ->get();
        $this->talaqqiBookings = TalaqqiBooking::with(['enrollment.course', 'teacher'])
            ->where('student_id', $userId)
            ->orderByDesc('booking_datetime')
            ->get();
    }

    public function render()
    {
        return view('livewire.student.my-academic')
            ->layout('layouts.contentNavbarLayout');
    }
}
