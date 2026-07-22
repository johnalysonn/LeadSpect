<?php

use App\Enums\LeadStatus;
use App\Models\Lead;
use App\Models\MessageTemplate;
use App\Models\User;

it('allows user to save search result as lead', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->postJson('/leads', [
        'osm_id' => 'node_999',
        'name' => 'Academia Fitness',
        'category' => 'Academia',
        'address' => 'Av. Paulista, 1000',
        'city' => 'São Paulo',
        'phone' => '11988887777',
    ]);

    $response->assertSuccessful();
    $this->assertDatabaseHas('leads', [
        'user_id' => $user->id,
        'name' => 'Academia Fitness',
        'status' => 'a_prospectar',
    ]);
});

it('initiates whatsapp contact and updates lead status to contato_iniciado', function () {
    $user = User::factory()->create();
    $template = MessageTemplate::create([
        'user_id' => $user->id,
        'title' => 'Apresentação',
        'content' => 'Olá {{empresa}}, vi vocês em {{cidade}}!',
    ]);

    $response = $this->actingAs($user)->postJson('/leads/whatsapp', [
        'name' => 'Padaria Central',
        'city' => 'Campinas',
        'phone' => '19977776666',
        'template_id' => $template->id,
    ]);

    $response->assertSuccessful();
    $response->assertJsonPath('message', 'Olá Padaria Central, vi vocês em Campinas!');
    $this->assertStringContainsString('https://wa.me/5519977776666', $response->json('whatsapp_url'));

    $this->assertDatabaseHas('leads', [
        'user_id' => $user->id,
        'name' => 'Padaria Central',
        'status' => 'contato_iniciado',
    ]);
});

it('allows changing lead status in kanban pipeline and logs status history', function () {
    $user = User::factory()->create();
    $lead = Lead::factory()->create([
        'user_id' => $user->id,
        'status' => LeadStatus::A_PROSPECTAR,
    ]);

    $response = $this->actingAs($user)->patchJson("/leads/{$lead->id}/status", [
        'status' => 'em_negociacao',
        'notes' => 'Apresentou proposta',
    ]);

    $response->assertSuccessful();

    $this->assertDatabaseHas('leads', [
        'id' => $lead->id,
        'status' => 'em_negociacao',
    ]);

    $this->assertDatabaseHas('lead_status_histories', [
        'lead_id' => $lead->id,
        'new_status' => 'em_negociacao',
    ]);
});

it('enforces data isolation preventing user from accessing another user leads', function () {
    $userA = User::factory()->create();
    $userB = User::factory()->create();

    $leadA = Lead::factory()->create(['user_id' => $userA->id]);

    $response = $this->actingAs($userB)->patchJson("/leads/{$leadA->id}/status", [
        'status' => 'cliente_fechado',
    ]);

    $response->assertForbidden();
});
