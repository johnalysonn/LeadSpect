<?php

namespace App\Http\Controllers\Auth;

use App\Actions\Auth\LoginUserAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LoginController extends Controller
{
    /**
     * Display the login view.
     */
    public function showLoginForm(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function login(LoginRequest $request, LoginUserAction $action): RedirectResponse
    {
        $action->execute(
            email: $request->input('email'),
            password: $request->input('password'),
            remember: $request->boolean('remember')
        );

        return redirect()->intended(route('dashboard'))->with('status', 'Bem-vindo de volta!');
    }

    /**
     * Log the user out of the application.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('status', 'Sua sessão foi encerrada com segurança.');
    }
}
