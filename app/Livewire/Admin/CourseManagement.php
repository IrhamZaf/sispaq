<?php

namespace App\Livewire\Admin;

use App\Models\Course;
use Livewire\Component;

class CourseManagement extends Component
{
    public $courses;

    public function mount()
    {
        $this->courses = Course::with('teacher')->orderBy('code')->get();
    }

    public function render()
    {
        return view('livewire.admin.course-management')
            ->layout('layouts.contentNavbarLayout');
    }
}
