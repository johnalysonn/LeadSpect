<?php

use App\Models\CompanySearchCache;
use App\Models\User;
use App\Services\Location\Contracts\LocationProviderInterface;
use App\Services\Location\DTOs\PlaceDTO;
use App\Services\Location\DTOs\SearchLocationDTO;
use App\Services\Location\Providers\FallbackLocationProvider;
use App\Services\Location\Providers\OverpassLocationProvider;
use App\Services\Location\Providers\TomTomLocationProvider;
use Illuminate\Support\Collection;
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

        public function searchCompanies(SearchLocationDTO $searchDTO): Collection {
            return collect([
                new PlaceDTO(
                    id: 'node_123',
                    name: 'Restaurante Teste',
                    category: 'restaurant',
                    latitude: -23.5505,
                    longitude: -46.6333,
                    address: 'Rua Augusta, 100',
                    city: 'São Paulo',
                    state: 'SP',
                    country: 'Brasil',
                    phone: '11999998888',
                    website: 'https://restauranteteste.com.br',
                    provider: 'Overpass',
                    whatsapp: '11999998888',
                    neighborhood: 'Consolação',
                    postalCode: '01305-000'
                )
            ]);
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
    $response->assertJsonPath('companies.0.category', 'restaurant');
    $response->assertJsonPath('companies.0.provider', 'Overpass');

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
    expect($results)->toBeInstanceOf(Collection::class)->and($results->count())->toBeGreaterThan(0);
    expect($results->first())->toBeInstanceOf(PlaceDTO::class);
    expect($results->first()->provider)->toBe('Overpass');
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
    expect($results->first()->name)->toBe('Mercado TomTom');
    expect($results->first()->provider)->toBe('TomTom');

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

    expect($results)->toBeInstanceOf(Collection::class)->and($results->count())->toBeGreaterThan(0);
    expect($results->first()->provider)->toBe('Overpass');

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
                'id' => 'cached_1',
                'osm_id' => 'cached_1',
                'name' => 'Hotel Cache',
                'category' => 'hotel',
                'latitude' => -23.550520,
                'longitude' => -46.633309,
                'provider' => 'Cache',
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
    expect($results->first()->name)->toBe('Hotel Cache');

    Log::shouldHaveReceived('info')
        ->with('Busca de empresas retornada do cache', \Mockery::on(function ($data) {
            return $data['provider'] === 'Cache'
                && $data['results_count'] === 1;
        }));
});
