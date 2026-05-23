<?php

namespace App\Livewire\Concerns;

use App\Models\Payment;
use App\Models\StudentApplication;
use App\Models\StudentEnrollment;
use Illuminate\Support\Facades\Auth;

trait AcceptsStudentOffer
{
    public function acceptOffer($applicationId)
    {
        $application = StudentApplication::where('id', $applicationId)
            ->where('user_id', Auth::id())
            ->where('status', 'offered')
            ->firstOrFail();

        $enrollment = StudentEnrollment::create([
            'student_id' => Auth::id(),
            'course_id' => $application->course_id,
            'current_module' => 1,
            'status' => 'active',
        ]);

        $fee = $application->course->fee_per_module;
        if ($application->course->code === 'KIBLAT') {
            $fee = $application->kiblat_category === 'A'
                ? $application->course->kiblat_cat_a_fee
                : $application->course->kiblat_cat_b_fee;
        }

        $payment = Payment::create([
            'enrollment_id' => $enrollment->id,
            'student_id' => Auth::id(),
            'course_id' => $application->course_id,
            'module_number' => 1,
            'amount' => $fee,
            'payment_status' => 'pending',
        ]);

        $application->update(['status' => 'approved']);

        session()->flash('success', 'Tawaran telah diterima! Sila selesaikan yuran untuk pendaftaran penuh.');

        return redirect()->route('student.pay', ['payment_id' => $payment->id]);
    }
}
