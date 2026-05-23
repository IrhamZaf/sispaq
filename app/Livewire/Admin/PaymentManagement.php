<?php

namespace App\Livewire\Admin;

use App\Models\Payment;
use Livewire\Component;

class PaymentManagement extends Component
{
    public $payments;
    public $filter = 'all';

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
        $query = Payment::with(['student', 'course'])->orderByDesc('created_at');
        if ($this->filter !== 'all') {
            $query->where('payment_status', $this->filter);
        }
        $this->payments = $query->get();
    }

    public function markPaid($paymentId)
    {
        $payment = Payment::findOrFail($paymentId);
        $payment->update([
            'payment_status' => 'paid',
            'payment_method' => $payment->payment_method ?? 'manual',
            'transaction_id' => 'TXN-MANUAL-' . $payment->id,
            'receipt_number' => 'REC-MANUAL-' . str_pad((string) $payment->id, 6, '0', STR_PAD_LEFT),
            'paid_at' => now(),
        ]);
        session()->flash('success', 'Bayaran ditandakan sebagai lunas (simulasi).');
        $this->loadPayments();
    }

    public function render()
    {
        return view('livewire.admin.payment-management')
            ->layout('layouts.contentNavbarLayout');
    }
}
