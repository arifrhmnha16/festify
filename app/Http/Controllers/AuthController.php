<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin() { return view('auth.login'); }
    public function showAdminLogin() { return view('auth.admin-login'); }
    public function showOfficerLogin(string $role) { return view('auth.officer-login', compact('role')); }
    public function showRegister() { return view('auth.register'); }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:30'],
            'password' => ['required', 'confirmed', 'min:6'],
        ]);

        $user = User::create($data);
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
