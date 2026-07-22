<?php

use App\Models\CompanySearchCache;
use App\Models\User;
use App\Services\Location\Contracts\LocationProviderInterface;
use App\Services\Location\DTOs\CompanyResultDTO;
use App\Services\Location\DTOs\SearchLocationDTO;
use App\Services\Location\Providers\FallbackLocationProvider;
use App\Services\Location\Providers\OverpassLocationProvider;
use App\Services\Location\Providers\TomTomLocationProvider;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

it('executes company search using location provider strategy and saves history', function () {
    $user = User::factory()->create();

    $fakeProvider = new class implements LocationProviderInterface {
        public function geocode(string $query): ?array {
            return [
                'latitude' => -23.550520,
                'longitude' => -46.633309,
                'display_name' => 'São Paulo, SP',
                'city' => 'São Paulo',
            ];
        }

        public function searchCompanies(SearchLocationDTO $searchDTO): array {
            return [
                new CompanyResultDTO(
                    osmId: 'node_123',
                    name: 'Restaurante Teste',
                    category: 'Restaurante',
                    address: 'Rua Augusta, 100',
                    city: 'São Paulo',
                    neighborhood: 'Consolação',
                    postalCode: '01305-000',
                    latitude: -23.5505,
                    longitude: -46.6333,
                    phone: '11999998888',
                    whatsapp: '11999998888',
                    website: 'https://restauranteteste.com.br'
                )
            ];
        }
    };

    app()->instance(LocationProviderInterface::class, $fakeProvider);

    $response = $this->actingAs($user)->postJson('/search', [
        'query' => 'São Paulo, SP',
        'radius' => 1000,
        'category' => 'Restaurante',
    ]);

    $response->assertSuccessful();
    $response->assertJsonPath('total', 1);
    $response->assertJsonPath('companies.0.name', 'Restaurante Teste');

    $this->assertDatabaseHas('search_histories', [
        'user_id' => $user->id,
        'results_count' => 1,
    ]);
});

it('overpass location provider constructs valid search dto and handles fallback gracefully', function () {
    $provider = new OverpassLocationProvider();
    $dto = new SearchLocationDTO(
        latitude: -23.550520,
        longitude: -46.633309,
        radius: 1000,
        category: 'farmácia',
        rawQuery: 'farmácia'
    );

    $results = $provider->searchCompanies($dto);
    expect($results)->toBeArray()->and(count($results))->toBeGreaterThan(0);
    expect($results[0])->toBeInstanceOf(CompanyResultDTO::class);
});

it('uses tomtom as primary provider and logs success details', function () {
    Log::spy();

    Http::fake([
        'https://api.tomtom.com/search/2/search/*' => Http::response([
            'results' => [
                [
                    'id' => 'tt_999',
                    'poi' => ['name' => 'Mercado TomTom'],
                    'address' => ['municipality' => 'São Paulo'],
                    'position' => ['lat' => -23.5500, 'lon' => -46.6300],
                ]
            ]
        ], 200),
    ]);

    $primary = new TomTomLocationProvider(apiKey: 'key_123');
    $fallback = new OverpassLocationProvider();
    $provider = new FallbackLocationProvider($primary, $fallback);

    $dto = new SearchLocationDTO(latitude: -23.5500, longitude: -46.6300, radius: 1000, category: 'mercado');
    $results = $provider->searchCompanies($dto);

    expect($results)->toHaveCount(1);
    expect($results[0]->name)->toBe('Mercado TomTom');

    Log::shouldHaveReceived('info')
        ->with('Busca de empresas realizada com sucesso', \Mockery::on(function ($data) {
            return $data['provider'] === 'TomTom'
                && $data['results_count'] === 1
                && isset($data['execution_time_ms']);
        }));
});

it('automatically falls back to overpass when tomtom fails and logs fallback reason', function () {
    Log::spy();

    // TomTom retorna 429
    Http::fake([
        'https://api.tomtom.com/search/2/search/*' => Http::response(['error' => 'Rate limit'], 429),
    ]);

    $primary = new TomTomLocationProvider(apiKey: 'key_123');
    $fallback = new OverpassLocationProvider();
    $provider = new FallbackLocationProvider($primary, $fallback);

    $dto = new SearchLocationDTO(latitude: -23.550520, longitude: -46.633309, radius: 1000, category: 'farmácia');
    $results = $provider->searchCompanies($dto);

    expect($results)->toBeArray()->and(count($results))->toBeGreaterThan(0);

    Log::shouldHaveReceived('warning')
        ->with('Fallback de busca ativado de TomTom para Overpass', \Mockery::on(function ($data) {
            return str_contains($data['fallback_reason'], '429');
        }));

    Log::shouldHaveReceived('info')
        ->with('Busca de empresas realizada com sucesso', \Mockery::on(function ($data) {
            return $data['provider'] === 'Overpass'
                && $data['results_count'] > 0
                && isset($data['fallback_reason'])
                && isset($data['overpass_endpoint']);
        }));
});

it('returns cached results without calling apis on cache hit', function () {
    Log::spy();

    $dto = new SearchLocationDTO(latitude: -23.550520, longitude: -46.633309, radius: 500, category: 'hotel');
    $hash = $dto->cacheHash();

    CompanySearchCache::create([
        'search_hash' => $hash,
        'latitude' => $dto->latitude,
        'longitude' => $dto->longitude,
        'radius' => $dto->radius,
        'category' => $dto->category,
        'response_data' => [
            [
                'osm_id' => 'cached_1',
                'name' => 'Hotel Cache',
                'category' => 'Hotel',
                'latitude' => -23.550520,
                'longitude' => -46.633309,
            ]
        ],
        'consulted_at' => now(),
    ]);

    // Bloquear todas as chamadas HTTP
    Http::preventStrayRequests();

    $primary = new TomTomLocationProvider(apiKey: 'key_123');
    $fallback = new OverpassLocationProvider();
    $provider = new FallbackLocationProvider($primary, $fallback);

    $results = $provider->searchCompanies($dto);

    expect($results)->toHaveCount(1);
    expect($results[0]->name)->toBe('Hotel Cache');

    Log::shouldHaveReceived('info')
        ->with('Busca de empresas retornada do cache', \Mockery::on(function ($data) {
            return $data['provider'] === 'Cache'
                && $data['results_count'] === 1;
        }));
});
