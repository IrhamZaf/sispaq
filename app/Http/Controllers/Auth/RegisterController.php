<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class RegisterController extends Controller
{

    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'phone' => ['required', 'string', 'max:20'],
            'ic_number' => ['required', 'string', 'max:20'],
            'student_id' => ['nullable', 'string', 'max:255'],
            'uni_course' => ['nullable', 'string', 'max:255'],
            'uni_faculty' => ['nullable', 'string', 'max:255'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'student', // Default role for registration is student
            'phone' => $request->phone,
            'ic_number' => $request->ic_number,
            'student_id' => $request->student_id,
            'uni_course' => $request->uni_course,
            'uni_faculty' => $request->uni_faculty,
        ]);

        Auth::login($user);

        // Check if there was a selected course to redirect directly to application form
        if ($request->filled('course_id')) {
            return redirect()->route('student.apply', ['course_id' => $request->course_id]);
        }

        return redirect('/student/dashboard');
    }
}
