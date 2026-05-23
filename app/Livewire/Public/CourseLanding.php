<?php

namespace App\Livewire\Public;

use App\Models\Course;
use Livewire\Component;

class CourseLanding extends Component
{
    public $selectedCourse = null;
    public $courses;
    public $closeDate = '2026-06-30 23:59:59'; // Registration closing date

    public function mount()
    {
        $this->courses = Course::all();
    }

    public function render()
    {
        return view('livewire.public.course-landing')
            ->layout('layouts.blankLayout'); // Clean blank layout for the public landing page
    }
}
