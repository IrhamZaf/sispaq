<?php

namespace App\Livewire\Teacher;

use App\Models\Attendance;
use App\Models\Certificate;
use App\Models\StudentEnrollment;
use App\Models\TalaqqiBooking;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class TalaqqiSession extends Component
{
    public $bookings = [];
    public $activeBookingId = null;
    
    // Active session fields
    public $activeBooking = null;
    public $currentPage = '';
    public $sessionStatus = 'Lulus'; // Lulus, Perlu Ulang
    public $sessionNotes = '';
    
    public $studentHistory = [];

    protected $queryString = ['booking_id' => ['except' => '', 'as' => 'booking_id']];
    public $booking_id = '';

    public function mount()
    {
        $this->refreshData();

        // If booking_id passed in URL parameters, start it immediately
        if ($this->booking_id) {
            $this->startSession($this->booking_id);
        }
    }

    public function refreshData()
    {
        $this->bookings = TalaqqiBooking::with(['student', 'enrollment.course'])
            ->where('teacher_id', Auth::id())
            ->orderBy('booking_datetime', 'asc')
            ->get();
    }

    public function startSession($bookingId)
    {
        $this->activeBookingId = $bookingId;
        $this->activeBooking = TalaqqiBooking::with(['student', 'enrollment.course'])
            ->where('id', $bookingId)
            ->where('teacher_id', Auth::id())
            ->firstOrFail();

        // Find last completed page for this student enrollment
        $lastCompleted = TalaqqiBooking::where('enrollment_id', $this->activeBooking->enrollment_id)
            ->where('session_status', 'Lulus')
            ->orderBy('current_page', 'desc')
            ->first();
        
        $this->currentPage = $lastCompleted ? ($lastCompleted->current_page + 1) : 1;
        $this->sessionStatus = 'Lulus';
        $this->sessionNotes = '';

        // Load student talaqqi session history
        $this->studentHistory = TalaqqiBooking::where('enrollment_id', $this->activeBooking->enrollment_id)
            ->where('id', '!=', $bookingId)
            ->orderBy('booking_datetime', 'desc')
            ->get();
    }

    public function saveSession()
    {
        $this->validate([
            'currentPage' => 'required|integer|between:1,604',
            'sessionStatus' => 'required|in:Lulus,Perlu Ulang',
            'sessionNotes' => 'nullable|string',
        ], [
            'currentPage.required' => 'Halaman Al-Quran wajib diisi.',
            'currentPage.integer' => 'Halaman mestilah nombor.',
            'currentPage.between' => 'Halaman mestilah antara 1 hingga 604.',
        ]);

        $booking = TalaqqiBooking::findOrFail($this->activeBookingId);
        $enrollment = StudentEnrollment::findOrFail($booking->enrollment_id);

        $booking->update([
            'current_page' => $this->currentPage,
            'session_status' => $this->sessionStatus,
            'notes' => $this->sessionNotes,
        ]);

        // Auto record attendance as present
        Attendance::updateOrCreate(
            [
                'talaqqi_booking_id' => $booking->id,
                'enrollment_id' => $enrollment->id,
            ],
            [
                'status' => 'hadir',
                'recorded_at' => now(),
            ]
        );

        // Check if student completed page 604 and passed
        if ($this->currentPage == 604 && $this->sessionStatus === 'Lulus') {
            $enrollment->update(['status' => 'completed']);

            // Issue Certificate
            $certNo = 'SANAD-KUTAB-' . date('Y') . '-' . rand(1000, 9999);
            $qrHash = md5($enrollment->student_id . '-' . $enrollment->course_id . '-' . time());

            Certificate::updateOrCreate(
                [
                    'student_id' => $enrollment->student_id,
                    'course_id' => $enrollment->course_id,
                ],
                [
                    'certificate_type' => 'sanad',
                    'certificate_number' => $certNo,
                    'issue_date' => now()->toDateString(),
                    'qr_code_hash' => $qrHash,
                ]
            );

            session()->flash('success', 'Alhamdulillah! Pelajar telah menamatkan talaqqi 30 Juzuk (Halaman 604). Sijil Sanad Al-Quran telah dijana.');
        } else {
            session()->flash('success', 'Kemajuan Sesi Talaqqi berjaya direkodkan.');
        }

        $this->activeBookingId = null;
        $this->activeBooking = null;
        $this->booking_id = '';
        $this->refreshData();
    }

    public function render()
    {
        return view('livewire.teacher.talaqqi-session')
            ->layout('layouts.contentNavbarLayout');
    }
}
