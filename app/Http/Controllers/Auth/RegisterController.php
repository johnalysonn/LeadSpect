<?php

namespace App\Http\Controllers\Auth;

use App\Actions\Auth\RegisterUserAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class RegisterController extends Controller
{
    /**
     * Display the registration view.
     */
    public function showRegistrationForm(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.register');
    }

    /**
     * Handle an incoming user registration request.
     */
    public function register(RegisterRequest $request, RegisterUserAction $action): RedirectResponse
    {
        $action->execute($request->validated());

        return redirect()->route('dashboard')->with('status', 'Conta criada com sucesso! Seja bem-vindo ao LeadSpect.');
    }
}
