<?php

use App\Models\MessageTemplate;
use App\Models\User;

it('allows user to create and parse message template with variables', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post('/templates', [
        'title' => 'Template de Vendas',
        'category' => 'Landing Page',
        'content' => 'Olá {{empresa}} de {{cidade}}, quer aumentar seus clientes de {{categoria}}?',
    ]);

    $response->assertRedirect('/templates');

    $template = MessageTemplate::where('user_id', $user->id)->first();
    expect($template)->not->toBeNull();

    $parsed = $template->parseForLead([
        'name' => 'Farmácia Silva',
        'city' => 'Santos',
        'category' => 'Saúde',
    ]);

    expect($parsed)->toBe('Olá Farmácia Silva de Santos, quer aumentar seus clientes de Saúde?');
});
