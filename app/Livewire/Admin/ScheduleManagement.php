<?php

namespace App\Livewire\Admin;

use App\Models\ClassSchedule;
use Livewire\Component;

class ScheduleManagement extends Component
{
    public $schedules;

    public function mount()
    {
        $this->schedules = ClassSchedule::with('course')
            ->orderByDesc('schedule_datetime')
            ->get();
    }

    public function render()
    {
        return view('livewire.admin.schedule-management')
            ->layout('layouts.contentNavbarLayout');
    }
}
