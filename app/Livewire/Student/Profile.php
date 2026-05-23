<?php

namespace App\Livewire\Student;

use App\Models\Payment;
use App\Models\StudentEnrollment;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Url;
use Livewire\Component;

class Profile extends Component
{
    public $user;

    public $activeCoursesCount = 0;

    public $pendingPaymentsCount = 0;

    public Collection $payments;

    public string $paymentFilter = 'all';

    public function boot(): void
    {
        $this->payments = collect();
    }

    public int $pendingCount = 0;

    public float $paidTotal = 0;

    #[Url]
    public string $tab = 'profil';

    public function mount(): void
    {
        if (! in_array($this->tab, ['profil', 'bayaran'], true)) {
            $this->tab = 'profil';
        }

        $this->user = Auth::user();
        $userId = $this->user->id;

        $this->activeCoursesCount = StudentEnrollment::where('student_id', $userId)
            ->where('status', 'active')
            ->count();

        $this->pendingPaymentsCount = Payment::where('student_id', $userId)
            ->where('payment_status', 'pending')
            ->count();

        $this->loadPayments();
    }

    public function updatedPaymentFilter(): void
    {
        $this->loadPayments();
    }

    public function setTab(string $tab): void
    {
        if (in_array($tab, ['profil', 'bayaran'], true)) {
            $this->tab = $tab;
        }
    }

    public function loadPayments(): void
    {
        $query = Payment::with('course')
            ->where('student_id', Auth::id())
            ->orderByDesc('created_at');

        if ($this->paymentFilter !== 'all') {
            $query->where('payment_status', $this->paymentFilter);
        }

        $this->payments = $query->get();

        $all = Payment::where('student_id', Auth::id())->get();
        $this->pendingCount = $all->where('payment_status', 'pending')->count();
        $this->paidTotal = (float) $all->where('payment_status', 'paid')->sum('amount');
        $this->pendingPaymentsCount = $this->pendingCount;
    }

    public function render()
    {
        return view('livewire.student.profile')
            ->layout('layouts.contentNavbarLayout');
    }
}
