<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Str;

class LoginController extends Controller
{

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();
            
            if ($user->isAdmin()) {
                return redirect()->intended('/admin/dashboard');
            } elseif ($user->isTeacher()) {
                return redirect()->intended('/teacher/dashboard');
            } else {
                return redirect()->intended('/student/dashboard');
            }
        }

        return back()->withErrors([
            'email' => 'Maklumat kelayakan yang diberikan tidak sepadan dengan rekod kami.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    public function redirectToGoogle()
    {
        if (empty(config('services.google.client_id'))) {
            // Fallback for development/testing when client_id is not set
            return redirect()->route('auth.google.callback.mock');
        }

        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Exception $e) {
            return redirect()->route('login')->withErrors([
                'email' => 'Gagal menyambung ke Google. Sila cuba lagi.',
            ]);
        }

        $email = $googleUser->getEmail();

        // Constrain emails to Universiti Malaya domains
        if (!str_ends_with($email, 'um.edu.my') && !str_ends_with($email, 'siswa.um.edu.my')) {
            return redirect()->route('login')->withErrors([
                'email' => 'Hanya alamat emel Universiti Malaya (UMMail) sahaja yang dibenarkan.',
            ]);
        }

        $user = User::where('email', $email)->first();

        if (!$user) {
            $role = str_ends_with($email, 'siswa.um.edu.my') ? 'student' : 'student';
            
            $user = User::create([
                'name' => $googleUser->getName() ?? explode('@', $email)[0],
                'email' => $email,
                'password' => bcrypt(Str::random(16)),
                'role' => $role,
            ]);
        }

        Auth::login($user, true);

        if ($user->isAdmin()) {
            return redirect()->intended('/admin/dashboard');
        } elseif ($user->isTeacher()) {
            return redirect()->intended('/teacher/dashboard');
        } else {
            return redirect()->intended('/student/dashboard');
        }
    }

    public function handleGoogleCallbackMock()
    {
        $mockEmail = 'pelajar.ummail@siswa.um.edu.my';
        $user = User::where('email', $mockEmail)->first();
        
        if (!$user) {
            $user = User::create([
                'name' => 'Pelajar UMMail Ujian',
                'email' => $mockEmail,
                'password' => bcrypt('password'),
                'role' => 'student',
            ]);
        }

        Auth::login($user, true);
        session()->flash('success', '[SIMULASI UMMAIL] Log masuk berjaya menggunakan akaun: ' . $mockEmail);
        return redirect()->intended('/student/dashboard');
    }
}
