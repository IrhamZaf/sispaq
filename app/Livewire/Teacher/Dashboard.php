<?php

namespace App\Livewire\Teacher;

use App\Models\ClassSchedule;
use App\Models\PlacementTest;
use App\Models\TalaqqiBooking;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Dashboard extends Component
{
    public $upcomingBookings;
    public $upcomingClasses;
    public $pendingTestsCount = 0;

    public function mount()
    {
        $this->upcomingBookings = TalaqqiBooking::with(['student', 'enrollment.course'])
            ->where('teacher_id', Auth::id())
            ->where('session_status', 'Belum Selesai')
            ->where('booking_datetime', '>=', now())
            ->orderBy('booking_datetime')
            ->take(5)
            ->get();

        $this->upcomingClasses = ClassSchedule::with('course')
            ->where('schedule_datetime', '>=', now())
            ->orderBy('schedule_datetime')
            ->take(5)
            ->get();

        $this->pendingTestsCount = PlacementTest::where('examiner_id', Auth::id())
            ->where('status', 'scheduled')
            ->count();
    }

    public function render()
    {
        return view('livewire.teacher.dashboard')
            ->layout('layouts.contentNavbarLayout');
    }
}
