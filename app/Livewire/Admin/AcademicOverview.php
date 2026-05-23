<?php

namespace App\Livewire\Admin;

use App\Models\AcademicRecord;
use Livewire\Component;

class AcademicOverview extends Component
{
    public $records;

    public function mount()
    {
        $this->records = AcademicRecord::with(['enrollment.student', 'enrollment.course', 'recorder'])
            ->orderByDesc('created_at')
            ->get();
    }

    public function render()
    {
        return view('livewire.admin.academic-overview')
            ->layout('layouts.contentNavbarLayout');
    }
}
