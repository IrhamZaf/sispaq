<?php

namespace App\Livewire\Student;

use App\Models\StudentEnrollment;
use App\Models\TalaqqiBooking;
use App\Models\User;
use App\Models\Attendance;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class TalaqqiScheduler extends Component
{
    public $talaqqiEnrollments = [];
    public $selectedEnrollmentId = '';
    public $teachers = [];
    public $selectedTeacherId = '';
    public $bookingDate = '';
    public $bookingTime = ''; // 15-min slots
    
    public $existingBookings = [];

    protected $rules = [
        'selectedEnrollmentId' => 'required',
        'selectedTeacherId' => 'required',
        'bookingDate' => 'required|date|after_or_equal:today',
        'bookingTime' => 'required',
    ];

    public function mount()
    {
        $userId = Auth::id();
        
        // Fetch student enrollments for Talaqqi courses (Pra KuTAB, KuTAB, KTKT)
        $this->talaqqiEnrollments = StudentEnrollment::with('course')
            ->where('student_id', $userId)
            ->where('status', 'active')
            ->whereHas('course', function ($q) {
                $q->whereIn('code', ['Pra KuTAB', 'KuTAB', 'KTKT']);
            })
            ->get();

        if ($this->talaqqiEnrollments->isNotEmpty()) {
            $this->selectedEnrollmentId = $this->talaqqiEnrollments->first()->id;
        }

        // Fetch teachers
        $this->teachers = User::where('role', 'teacher')->get();
        if ($this->teachers->isNotEmpty()) {
            $this->selectedTeacherId = $this->teachers->first()->id;
        }

        $this->bookingDate = date('Y-m-d');
        $this->refreshBookings();
    }

    public function refreshBookings()
    {
        $this->existingBookings = TalaqqiBooking::with(['teacher', 'enrollment.course'])
            ->where('student_id', Auth::id())
            ->orderBy('booking_datetime', 'desc')
            ->get();
    }

    public function bookSlot()
    {
        $this->validate();

        $bookingDatetimeStr = $this->bookingDate . ' ' . $this->bookingTime;
        $bookingDatetime = date('Y-m-d H:i:s', strtotime($bookingDatetimeStr));

        // Check if teacher is already booked at that slot
        $conflict = TalaqqiBooking::where('teacher_id', $this->selectedTeacherId)
            ->where('booking_datetime', $bookingDatetime)
            ->first();

        if ($conflict) {
            session()->flash('error', 'Slot masa tersebut telah ditempah oleh pelajar lain. Sila pilih slot lain.');
            return;
        }

        // Check if student has already booked a slot at that exact time
        $studentConflict = TalaqqiBooking::where('student_id', Auth::id())
            ->where('booking_datetime', $bookingDatetime)
            ->first();

        if ($studentConflict) {
            session()->flash('error', 'Anda telah menempah slot lain pada masa yang sama.');
            return;
        }

        // Create booking
        $enrollment = StudentEnrollment::findOrFail($this->selectedEnrollmentId);
        
        $booking = TalaqqiBooking::create([
            'enrollment_id' => $enrollment->id,
            'student_id' => Auth::id(),
            'teacher_id' => $this->selectedTeacherId,
            'booking_datetime' => $bookingDatetime,
            'session_status' => 'Belum Selesai',
        ]);

        // Auto-create class attendance as 'tidak_hadir' until completed
        Attendance::create([
            'enrollment_id' => $enrollment->id,
            'talaqqi_booking_id' => $booking->id,
            'status' => 'tidak_hadir',
            'recorded_at' => $bookingDatetime,
        ]);

        session()->flash('success', 'Tempahan slot Talaqqi 15-minit anda berjaya dijadualkan.');
        
        $this->refreshBookings();
    }

    public function cancelBooking($bookingId)
    {
        $booking = TalaqqiBooking::where('id', $bookingId)
            ->where('student_id', Auth::id())
            ->firstOrFail();

        if ($booking->session_status === 'Belum Selesai') {
            $booking->delete();
            session()->flash('success', 'Tempahan slot Talaqqi berjaya dibatalkan.');
            $this->refreshBookings();
        } else {
            session()->flash('error', 'Sesi yang telah selesai tidak boleh dibatalkan.');
        }
    }

    public function render()
    {
        return view('livewire.student.talaqqi-scheduler')
            ->layout('layouts.contentNavbarLayout');
    }
}
