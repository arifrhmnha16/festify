<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Passwords\PasswordBroker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function showLogin() { return view('auth.login'); }
    public function showAdminLogin() { return view('auth.admin-login'); }
    public function showOfficerLogin(string $role) { return view('auth.officer-login', compact('role')); }
    public function showRegister() { return view('auth.register'); }
    public function showForgotPassword() { return view('auth.forgot-password'); }
    public function showResetPassword(Request $request, string $token) { return view('auth.reset-password', ['token' => $token, 'email' => $request->email]); }
    public function showVerificationNotice(Request $request)
    {
        $localVerificationUrl = null;

        if (app()->environment('local') && config('mail.default') === 'log' && $request->user()) {
            $localVerificationUrl = URL::temporarySignedRoute(
                'verification.verify',
                now()->addMinutes(60),
                [
                    'id' => $request->user()->getKey(),
                    'hash' => sha1($request->user()->getEmailForVerification()),
                ],
            );
        }

        return view('auth.verify-email', compact('localVerificationUrl'));
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:30'],
            'password' => ['required', 'confirmed', 'min:6'],
        ]);

        $user = User::create($data);
        event(new Registered($user));

        Auth::login($user);

        return redirect()->route('user.dashboard');
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (Auth::guard('web')->attempt(['email' => $data['email'], 'password' => $data['password']], $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->route('user.dashboard');
        }

        return back()->withErrors(['email' => 'Email atau password tidak sesuai.'])->onlyInput('email');
    }

    public function adminLogin(Request $request)
    {
        $data = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        if (Auth::guard('admin')->attempt($data, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->route('admin.dashboard');
        }

        return back()->withErrors(['username' => 'Username atau password admin tidak sesuai.'])->onlyInput('username');
    }

    public function officerLogin(Request $request, string $role)
    {
        abort_unless(in_array($role, ['loket', 'gate'], true), 404);

        $data = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        if (Auth::guard('officer')->attempt(['username' => $data['username'], 'password' => $data['password'], 'role' => $role], $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->route($role.'.dashboard');
        }

        return back()->withErrors(['username' => 'Username atau password petugas tidak sesuai.'])->onlyInput('username');
    }

    public function redirectToRoleLogin(string $role)
    {
        if ($role === 'admin') {
            return redirect()->route('admin.login');
        }

        if (in_array($role, ['loket', 'gate'], true)) {
            return redirect()->route($role.'.login');
        }

        return redirect()->route('login');
    }

    public function sendPasswordResetLink(Request $request)
    {
        $request->validate(['email' => ['required', 'email']]);

        $status = Password::sendResetLink($request->only('email'));

        return $status === Password::RESET_LINK_SENT
            ? back()->with('success', __($status))
            : back()->withErrors(['email' => __($status)])->onlyInput('email');
    }

    public function resetPassword(Request $request)
    {
        $data = $request->validate([
            'token' => ['required', 'string'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', 'min:6'],
        ]);

        $status = Password::reset($data, function (User $user, string $password) {
            $user->forceFill([
                'password' => Hash::make($password),
                'remember_token' => Str::random(60),
            ])->save();
        });

        return $status === PasswordBroker::PASSWORD_RESET
            ? redirect()->route('login')->with('success', __($status))
            : back()->withErrors(['email' => __($status)])->onlyInput('email');
    }

    public function verifyEmail(Request $request, int $id, string $hash)
    {
        $user = User::findOrFail($id);

        abort_unless(hash_equals($hash, sha1($user->getEmailForVerification())), 403);

        if ($user->hasVerifiedEmail()) {
            Auth::login($user);
            return redirect()->route('user.dashboard');
        }

        $user->markEmailAsVerified();
        Auth::login($user);

        return redirect()->route('user.dashboard')->with('success', 'Email berhasil diverifikasi.');
    }

    public function resendVerificationEmail(Request $request)
    {
        if ($request->user()?->hasVerifiedEmail()) {
            return redirect()->route('user.dashboard');
        }

        $request->user()->sendEmailVerificationNotification();

        return back()->with('success', 'Link verifikasi email sudah dikirim ulang.');
    }

    public function logout(Request $request)
    {
        foreach (['web', 'admin', 'officer'] as $guard) {
            Auth::guard($guard)->logout();
        }

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
