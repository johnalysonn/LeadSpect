<?php

use App\Services\Location\DTOs\CompanyResultDTO;
use App\Services\Location\DTOs\SearchLocationDTO;
use App\Services\Location\Providers\TomTomLocationProvider;
use Illuminate\Support\Facades\Http;

it('throws exception when tom tom api key is missing', function () {
    $provider = new TomTomLocationProvider(apiKey: '');
    $dto = new SearchLocationDTO(latitude: -23.550520, longitude: -46.633309);

    $provider->searchCompanies($dto);
})->throws(\RuntimeException::class, 'TomTom API Key não configurada');

it('searches companies using tom tom api and maps to company result dto', function () {
    Http::fake([
        'https://api.tomtom.com/*' => Http::response([
            'summary' => ['numResults' => 1],
            'results' => [
                [
                    'id' => 'tt_123',
                    'poi' => [
                        'name' => 'Farmácia TomTom',
                        'phone' => '+55 11 98888-7777',
                        'url' => 'https://farmaciatomtom.com.br',
                        'classifications' => [
                            ['names' => [['name' => 'Farmácia']]]
                        ]
                    ],
                    'address' => [
                        'streetName' => 'Avenida Paulista',
                        'streetNumber' => '1000',
                        'municipality' => 'São Paulo',
                        'municipalitySubdivision' => 'Bela Vista',
                        'postalCode' => '01310-100',
                    ],
                    'position' => [
                        'lat' => -23.5615,
                        'lon' => -46.6560,
                    ],
                ]
            ]
        ], 200),
    ]);

    $provider = new TomTomLocationProvider(apiKey: 'fake_key_123');
    $dto = new SearchLocationDTO(
        latitude: -23.550520,
        longitude: -46.633309,
        radius: 1000,
        category: 'farmácia',
        rawQuery: 'farmácia'
    );

    $results = $provider->searchCompanies($dto);

    expect($results)->toHaveCount(1);
    expect($results[0])->toBeInstanceOf(CompanyResultDTO::class);
    expect($results[0]->osmId)->toBe('tomtom_tt_123');
    expect($results[0]->name)->toBe('Farmácia TomTom');
    expect($results[0]->category)->toBe('Farmácia');
    expect($results[0]->address)->toBe('Avenida Paulista, 1000');
    expect($results[0]->city)->toBe('São Paulo');
    expect($results[0]->neighborhood)->toBe('Bela Vista');
    expect($results[0]->postalCode)->toBe('01310-100');
    expect($results[0]->phone)->toBe('+55 11 98888-7777');
    expect($results[0]->whatsapp)->toBe('+55 11 98888-7777');
    expect($results[0]->website)->toBe('https://farmaciatomtom.com.br');
});

it('throws runtime exception when tom tom returns http 429 rate limit', function () {
    Http::fake([
        'https://api.tomtom.com/*' => Http::response(['error' => 'Rate limit exceeded'], 429),
    ]);

    $provider = new TomTomLocationProvider(apiKey: 'fake_key_123');
    $dto = new SearchLocationDTO(latitude: -23.550520, longitude: -46.633309);

    $provider->searchCompanies($dto);
})->throws(\RuntimeException::class, 'TomTom API HTTP 429');

it('geocodes query using tom tom geocode api', function () {
    Http::fake([
        'https://api.tomtom.com/*' => Http::response([
            'results' => [
                [
                    'position' => ['lat' => -23.55052, 'lon' => -46.633309],
                    'address' => [
                        'freeformAddress' => 'São Paulo, SP, Brasil',
                        'municipality' => 'São Paulo',
                    ],
                ]
            ]
        ], 200),
    ]);

    $provider = new TomTomLocationProvider(apiKey: 'fake_key_123');
    $res = $provider->geocode('São Paulo, SP');

    expect($res)->not->toBeNull();
    expect($res['latitude'])->toBe(-23.55052);
    expect($res['longitude'])->toBe(-46.633309);
    expect($res['city'])->toBe('São Paulo');
});
