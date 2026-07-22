<?php

use App\Http\Controllers\Auth\GithubAuthController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\MessageTemplateController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
});

// Rotas de Autenticação para Visitantes (Guest)
Route::middleware(['guest'])->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])
        ->middleware('throttle:10,1')
        ->name('login.store');

    // Desativados temporariamente:
    // Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    // Route::post('/register', [RegisterController::class, 'register'])
    //     ->middleware('throttle:6,1')
    //     ->name('register.store');

    // Route::get('/auth/github', [GithubAuthController::class, 'redirect'])
    //     ->middleware('throttle:10,1')
    //     ->name('auth.github');

    // Route::get('/auth/github/callback', [GithubAuthController::class, 'callback'])
    //     ->middleware('throttle:10,1')
    //     ->name('auth.github.callback');

    // Route::post('/auth/mock', [GithubAuthController::class, 'mockLogin'])
    //     ->middleware('throttle:10,1')
    //     ->name('auth.mock');
});

// Área Autenticada
Route::middleware(['auth'])->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Pesquisa Geográfica de Leads (OpenStreetMap / Overpass / Nominatim)
    Route::get('/search', [SearchController::class, 'index'])->name('search.index');
    Route::post('/search', [SearchController::class, 'search'])
        ->middleware('throttle:30,1')
        ->name('search.perform');
    Route::get('/search/history', [SearchController::class, 'history'])->name('search.history');

    // Gestão de Leads & Pipeline Kanban
    Route::get('/leads', [LeadController::class, 'index'])->name('leads.index');
    Route::post('/leads', [LeadController::class, 'store'])->name('leads.store');
    Route::patch('/leads/{lead}/status', [LeadController::class, 'updateStatus'])->name('leads.update-status');
    Route::post('/leads/whatsapp', [LeadController::class, 'whatsapp'])->name('leads.whatsapp');
    Route::post('/leads/{lead}/enrich', [LeadController::class, 'enrich'])->name('leads.enrich');
    Route::post('/leads/{lead}/favorite', [LeadController::class, 'toggleFavorite'])->name('leads.favorite');
    Route::post('/leads/{lead}/notes', [LeadController::class, 'addNote'])->name('leads.add-note');
    Route::get('/leads/export', [LeadController::class, 'export'])->name('leads.export');
    Route::delete('/leads/{lead}', [LeadController::class, 'destroy'])->name('leads.destroy');

    // Templates de Mensagem para WhatsApp
    Route::resource('templates', MessageTemplateController::class)->except(['create', 'show', 'edit']);

    // Gerenciamento de Usuários (CRUD)
    Route::resource('users', UserController::class);
});
