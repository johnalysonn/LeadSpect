<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

it('renders the login page successfully for guests', function () {
    $response = $this->get('/login');

    $response->assertSuccessful();
    $response->assertSee('LeadSpect');
    $response->assertDontSee('Continuar com GitHub');
});

it('redirects authenticated users away from the login page', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/login');

    $response->assertRedirect('/dashboard');
});

it('authenticates user with valid email and password credentials', function () {
    $user = User::factory()->create([
        'email' => 'john@example.com',
        'password' => Hash::make('password123'),
    ]);

    $response = $this->post('/login', [
        'email' => 'john@example.com',
        'password' => 'password123',
    ]);

    $response->assertRedirect('/dashboard');
    $this->assertAuthenticatedAs($user);
});

it('rejects authentication with invalid password credentials', function () {
    User::factory()->create([
        'email' => 'john@example.com',
        'password' => Hash::make('password123'),
    ]);

    $response = $this->post('/login', [
        'email' => 'john@example.com',
        'password' => 'wrong-password',
    ]);

    $response->assertSessionHasErrors('email');
    $this->assertGuest();
});

it('locks out user after multiple failed login attempts', function () {
    User::factory()->create([
        'email' => 'throttled@example.com',
        'password' => Hash::make('password123'),
    ]);

    for ($i = 0; $i < 10; $i++) {
        $this->post('/login', [
            'email' => 'throttled@example.com',
            'password' => 'wrong-password',
        ]);
    }

    $response = $this->post('/login', [
        'email' => 'throttled@example.com',
        'password' => 'password123',
    ]);

    $response->assertStatus(429);
    $this->assertGuest();
});

it('allows an authenticated user to logout securely', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post('/logout');

    $response->assertRedirect('/login');
    $this->assertGuest();
});
