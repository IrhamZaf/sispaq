<?php

namespace App\Livewire\Student;

use App\Models\Certificate;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class MyCertificates extends Component
{
    public $certificates;

    public function mount()
    {
        $this->certificates = Certificate::with('course')
            ->where('student_id', Auth::id())
            ->orderByDesc('issue_date')
            ->get();
    }

    public function render()
    {
        return view('livewire.student.my-certificates')
            ->layout('layouts.contentNavbarLayout');
    }
}
