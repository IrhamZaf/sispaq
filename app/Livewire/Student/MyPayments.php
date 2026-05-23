<?php

namespace App\Livewire\Student;

use App\Models\Payment;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class MyPayments extends Component
{
    public $payments;
    public $filter = 'all';
    public $pendingCount = 0;
    public $paidTotal = 0;

    public function mount()
    {
        $this->loadPayments();
    }

    public function updatedFilter()
    {
        $this->loadPayments();
    }

    public function loadPayments()
    {
        $query = Payment::with('course')
            ->where('student_id', Auth::id())
            ->orderByDesc('created_at');

        if ($this->filter !== 'all') {
            $query->where('payment_status', $this->filter);
        }

        $this->payments = $query->get();
        $all = Payment::where('student_id', Auth::id())->get();
        $this->pendingCount = $all->where('payment_status', 'pending')->count();
        $this->paidTotal = $all->where('payment_status', 'paid')->sum('amount');
    }

    public function render()
    {
        return view('livewire.student.my-payments')
            ->layout('layouts.contentNavbarLayout');
    }
}
