<?php

use App\Models\User;

it('renders the registration page for guests', function () {
    $response = $this->get('/register');

    $response->assertSuccessful();
    $response->assertSee('Criar conta no LeadSpect');
})->skip('Cadastro de usuário desativado temporariamente.');

it('registers a new user and logs them in', function () {
    $response = $this->post('/register', [
        'name' => 'Ana Silva',
        'email' => 'ana@example.com',
        'password' => 'secret123',
        'password_confirmation' => 'secret123',
    ]);

    $response->assertRedirect('/dashboard');
    $this->assertAuthenticated();

    $this->assertDatabaseHas('users', [
        'name' => 'Ana Silva',
        'email' => 'ana@example.com',
        'auth_provider' => 'email',
    ]);
})->skip('Cadastro de usuário desativado temporariamente.');

it('prevents registration with duplicate email address', function () {
    User::factory()->create(['email' => 'existing@example.com']);

    $response = $this->post('/register', [
        'name' => 'Outro Usuario',
        'email' => 'existing@example.com',
        'password' => 'secret123',
        'password_confirmation' => 'secret123',
    ]);

    $response->assertSessionHasErrors('email');
    $this->assertGuest();
})->skip('Cadastro de usuário desativado temporariamente.');

it('validates password confirmation during registration', function () {
    $response = $this->post('/register', [
        'name' => 'Carlos',
        'email' => 'carlos@example.com',
        'password' => 'secret123',
        'password_confirmation' => 'mismatch',
    ]);

    $response->assertSessionHasErrors('password');
    $this->assertGuest();
})->skip('Cadastro de usuário desativado temporariamente.');
