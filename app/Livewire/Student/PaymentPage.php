<?php

namespace App\Livewire\Student;

use App\Models\Payment;
use App\Models\StudentEnrollment;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class PaymentPage extends Component
{
    public $payment;
    public $paymentMethod = 'fpx';
    
    // FPX fields
    public $selectedBank = '';
    
    // Card fields
    public $cardNumber = '';
    public $cardExpiry = '';
    public $cardCvv = '';

    public $success = false;

    public function mount($payment_id)
    {
        $this->payment = Payment::with(['course', 'enrollment'])
            ->where('id', $payment_id)
            ->where('student_id', Auth::id())
            ->firstOrFail();

        if ($this->payment->payment_status === 'paid') {
            $this->success = true;
        }
    }

    public function processPayment()
    {
        if ($this->paymentMethod === 'fpx') {
            $this->validate([
                'selectedBank' => 'required',
            ], [
                'selectedBank.required' => 'Sila pilih bank anda.',
            ]);
        } else {
            $this->validate([
                'cardNumber' => 'required|numeric|digits:16',
                'cardExpiry' => 'required|regex:/^(0[1-9]|1[0-2])\/[0-9]{2}$/',
                'cardCvv' => 'required|numeric|digits:3',
            ], [
                'cardNumber.required' => 'Nombor kad diperlukan.',
                'cardNumber.numeric' => 'Nombor kad mestilah nombor.',
                'cardNumber.digits' => 'Nombor kad mestilah 16 digit.',
                'cardExpiry.required' => 'Tarikh luput diperlukan.',
                'cardExpiry.regex' => 'Tarikh luput mestilah dalam format MM/YY.',
                'cardCvv.required' => 'Kod CVV diperlukan.',
                'cardCvv.numeric' => 'Kod CVV mestilah nombor.',
                'cardCvv.digits' => 'Kod CVV mestilah 3 digit.',
            ]);
        }

        // Simulating processing delay
        sleep(1);

        $transactionId = 'TXN-' . strtoupper(uniqid());
        $receiptNumber = 'REC-' . date('Ymd') . '-' . rand(1000, 9999);

        // Update payment database record
        $this->payment->update([
            'payment_method' => $this->paymentMethod,
            'payment_status' => 'paid',
            'transaction_id' => $transactionId,
            'receipt_number' => $receiptNumber,
            'paid_at' => now(),
        ]);

        $this->success = true;

        session()->flash('success', 'Pembayaran yuran modul berjaya diselesaikan.');
    }

    public function render()
    {
        return view('livewire.student.payment-page')
            ->layout('layouts.contentNavbarLayout');
    }
}
