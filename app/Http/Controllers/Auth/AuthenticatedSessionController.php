<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('errors.404');
    }
    public function loginForm()
    {
        return view('admin.auth.login');
    }
    /**
     * Handle an incoming authentication request.
     */
//    public function store(LoginRequest $request): RedirectResponse
//    {
//        $request->authenticate();
//        $request->session()->regenerate();
//        return redirect()->intended(route('dashboard', absolute: false));
//    }
    public function store(LoginRequest $request): RedirectResponse
    {
        try {
            $request->authenticate(); // Authenticate the user
            $user = Auth::user(); // Get the authenticated user
            if (!$user || $user->status !== 1) {
                Auth::logout(); // Logout the user if not active
                return back()->withErrors(['username' => 'Your account is inactive. Please contact support.']);
            }
            $request->session()->regenerate(); // Regenerate session
            return redirect()->intended(route('dashboard', absolute: false));
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors(['username' => 'Invalid username or password.']);
        }
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/mfa-admin/signin');
    }
}
