<?php

use App\Models\User;

it('prevents guests from accessing user management pages', function () {
    $this->get('/users')->assertRedirect('/login');
    $this->get('/users/create')->assertRedirect('/login');
});

it('allows authenticated users to view the user list', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/users');

    $response->assertSuccessful();
    $response->assertSee('Gerenciamento de Usuários');
    $response->assertSee($user->email);
});

it('allows creating a new user through the management panel', function () {
    $admin = User::factory()->create();

    $response = $this->actingAs($admin)->post('/users', [
        'name' => 'Novo Membro',
        'email' => 'membro@example.com',
        'password' => 'password123',
    ]);

    $response->assertRedirect('/users');
    $this->assertDatabaseHas('users', [
        'name' => 'Novo Membro',
        'email' => 'membro@example.com',
    ]);
});

it('allows updating an existing user details', function () {
    $admin = User::factory()->create();
    $targetUser = User::factory()->create([
        'name' => 'Nome Antigo',
        'email' => 'antigo@example.com',
    ]);

    $response = $this->actingAs($admin)->put("/users/{$targetUser->id}", [
        'name' => 'Nome Atualizado',
        'email' => 'novoemail@example.com',
    ]);

    $response->assertRedirect('/users');
    $this->assertDatabaseHas('users', [
        'id' => $targetUser->id,
        'name' => 'Nome Atualizado',
        'email' => 'novoemail@example.com',
    ]);
});

it('allows deleting a user account but prevents self-deletion', function () {
    $admin = User::factory()->create();
    $otherUser = User::factory()->create();

    $deleteResponse = $this->actingAs($admin)->delete("/users/{$otherUser->id}");
    $deleteResponse->assertRedirect('/users');
    $this->assertDatabaseMissing('users', ['id' => $otherUser->id]);

    $selfDeleteResponse = $this->actingAs($admin)->delete("/users/{$admin->id}");
    $selfDeleteResponse->assertForbidden();
    $this->assertDatabaseHas('users', ['id' => $admin->id]);
});
