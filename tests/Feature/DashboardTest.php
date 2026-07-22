<?php

use App\Models\User;

it('renders the dashboard successfully for authenticated users', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/dashboard');

    $response->assertSuccessful();
    $response->assertSee('Visão Geral da Prospecção');
    $response->assertSee('Empresas Encontradas');
    $response->assertSee('Clientes Fechados');
});
