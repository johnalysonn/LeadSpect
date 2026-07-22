<?php

use App\Models\User;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;

it('initiates github oauth redirect when credentials exist', function () {
    config([
        'services.github.client_id' => 'fake-github-client-id',
        'services.github.client_secret' => 'fake-github-client-secret',
    ]);

    $response = $this->get('/auth/github');

    $response->assertRedirect();
    $this->assertStringContainsString('github.com', $response->headers->get('Location'));
})->skip('Login com GitHub desativado temporariamente.');

it('authenticates and creates new user via github oauth callback', function () {
    $abstractUser = Mockery::mock(SocialiteUser::class);
    $abstractUser->shouldReceive('getId')->andReturn('987654321');
    $abstractUser->shouldReceive('getName')->andReturn('Dev GitHub');
    $abstractUser->shouldReceive('getEmail')->andReturn('githubdev@example.com');
    $abstractUser->shouldReceive('getAvatar')->andReturn('https://avatars.githubusercontent.com/u/987654321');

    Socialite::shouldReceive('driver')->with('github')->andReturnSelf();
    Socialite::shouldReceive('user')->andReturn($abstractUser);

    $response = $this->get('/auth/github/callback');

    $response->assertRedirect('/dashboard');
    $this->assertAuthenticated();
    $this->assertDatabaseHas('users', [
        'email' => 'githubdev@example.com',
        'github_id' => '987654321',
        'auth_provider' => 'github',
    ]);
})->skip('Login com GitHub desativado temporariamente.');

it('links existing user with matching email when logging in via github oauth', function () {
    $existingUser = User::factory()->create([
        'email' => 'existing@example.com',
        'github_id' => null,
        'auth_provider' => 'email',
    ]);

    $abstractUser = Mockery::mock(SocialiteUser::class);
    $abstractUser->shouldReceive('getId')->andReturn('11223344');
    $abstractUser->shouldReceive('getName')->andReturn('Existing User');
    $abstractUser->shouldReceive('getEmail')->andReturn('existing@example.com');
    $abstractUser->shouldReceive('getAvatar')->andReturn('https://avatars.githubusercontent.com/u/11223344');

    Socialite::shouldReceive('driver')->with('github')->andReturnSelf();
    Socialite::shouldReceive('user')->andReturn($abstractUser);

    $response = $this->get('/auth/github/callback');

    $response->assertRedirect('/dashboard');
    $this->assertAuthenticatedAs($existingUser);

    $this->assertDatabaseHas('users', [
        'id' => $existingUser->id,
        'email' => 'existing@example.com',
        'github_id' => '11223344',
    ]);

    expect(User::where('email', 'existing@example.com')->count())->toBe(1);
})->skip('Login com GitHub desativado temporariamente.');

it('allows mock login in local environment', function () {
    $response = $this->post('/auth/mock');

    $response->assertRedirect('/dashboard');
    $this->assertAuthenticated();
    $this->assertDatabaseHas('users', [
        'email' => 'dev@leadspect.com',
        'github_id' => 'mock_github_id_12345',
    ]);
})->skip('Usuário mock desativado temporariamente.');
