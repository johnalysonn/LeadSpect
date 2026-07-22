<?php

namespace App\Services\Location\Providers;

use App\Models\CompanySearchCache;
use App\Services\Location\Contracts\LocationProviderInterface;
use App\Services\Location\DTOs\CompanyResultDTO;
use App\Services\Location\DTOs\SearchLocationDTO;
use Illuminate\Support\Facades\Log;

class FallbackLocationProvider implements LocationProviderInterface
{
    public function __construct(
        protected LocationProviderInterface $primaryProvider,
        protected LocationProviderInterface $fallbackProvider
    ) {}

    /**
     * Convert address, city, CEP or query into latitude and longitude coordinates.
     */
    public function geocode(string $query): ?array
    {
        try {
            $geocoded = $this->primaryProvider->geocode($query);
            if ($geocoded !== null) {
                return $geocoded;
            }
        } catch (\Throwable $e) {
            Log::warning("Geocodificação via provider principal falhou, acionando fallback: " . $e->getMessage());
        }

        return $this->fallbackProvider->geocode($query);
    }

    /**
     * Reverse geocode city coordinates.
     */
    public function reverseGeocodeCity(float $lat, float $lng): ?string
    {
        if (method_exists($this->primaryProvider, 'reverseGeocodeCity')) {
            try {
                $city = $this->primaryProvider->reverseGeocodeCity($lat, $lng);
                if ($city !== null) {
                    return $city;
                }
            } catch (\Throwable $e) {
                // Segue para o fallback
            }
        }

        if (method_exists($this->fallbackProvider, 'reverseGeocodeCity')) {
            return $this->fallbackProvider->reverseGeocodeCity($lat, $lng);
        }

        return null;
    }

    /**
     * Get GeoJSON city boundary.
     */
    public function getCityBoundary(string $cityName): ?array
    {
        if (method_exists($this->primaryProvider, 'getCityBoundary')) {
            try {
                $boundary = $this->primaryProvider->getCityBoundary($cityName);
                if ($boundary !== null) {
                    return $boundary;
                }
            } catch (\Throwable $e) {
                // Segue para o fallback
            }
        }

        if (method_exists($this->fallbackProvider, 'getCityBoundary')) {
            return $this->fallbackProvider->getCityBoundary($cityName);
        }

        return null;
    }

    /**
     * Search companies around a geographic coordinate with Cache -> TomTom -> Overpass strategy.
     *
     * @return array<CompanyResultDTO>
     */
    public function searchCompanies(SearchLocationDTO $searchDTO): array
    {
        $hash = $searchDTO->cacheHash();

        // 1. Verificar Cache em Banco de Dados antes de chamar qualquer API
        $cached = CompanySearchCache::where('search_hash', $hash)->first();

        if ($cached && $cached->consulted_at->gt(now()->subDays(7))) {
            $companyDTOs = array_map(
                fn($item) => new CompanyResultDTO(
                    osmId: $item['osm_id'] ?? null,
                    name: $item['name'] ?? 'Empresa',
                    category: $item['category'] ?? 'Estabelecimento',
                    address: $item['address'] ?? null,
                    city: $item['city'] ?? null,
                    neighborhood: $item['neighborhood'] ?? null,
                    postalCode: $item['postal_code'] ?? null,
                    latitude: (float) $item['latitude'],
                    longitude: (float) $item['longitude'],
                    phone: $item['phone'] ?? null,
                    whatsapp: $item['whatsapp'] ?? null,
                    website: $item['website'] ?? null,
                    instagram: $item['instagram'] ?? null,
                    facebook: $item['facebook'] ?? null,
                    rating: isset($item['rating']) ? (float) $item['rating'] : null,
                    reviewCount: (int) ($item['review_count'] ?? 0),
                    isOpenNow: $item['is_open_now'] ?? null,
                    openingHours: $item['opening_hours'] ?? null,
                    rawData: $item['raw_data'] ?? []
                ),
                $cached->response_data
            );

            Log::info('Busca de empresas retornada do cache', [
                'provider' => 'Cache',
                'execution_time_ms' => 0.0,
                'results_count' => count($companyDTOs),
            ]);

            return $companyDTOs;
        }

        // 2. Tentar Provider Principal (TomTom)
        $startTime = microtime(true);
        $fallbackReason = null;

        try {
            $results = $this->primaryProvider->searchCompanies($searchDTO);
            $durationMs = round((microtime(true) - $startTime) * 1000, 2);

            Log::info('Busca de empresas realizada com sucesso', [
                'provider' => 'TomTom',
                'execution_time_ms' => $durationMs,
                'results_count' => count($results),
            ]);

            $this->saveToCache($hash, $searchDTO, $results);

            return $results;
        } catch (\Throwable $e) {
            $fallbackReason = $e->getMessage();

            Log::warning('Fallback de busca ativado de TomTom para Overpass', [
                'fallback_reason' => $fallbackReason,
            ]);
        }

        // 3. Fallback para Provider Secundário (Overpass)
        $overpassStartTime = microtime(true);
        $results = $this->fallbackProvider->searchCompanies($searchDTO);
        $durationMs = round((microtime(true) - $overpassStartTime) * 1000, 2);

        $endpointUsed = method_exists($this->fallbackProvider, 'getLastUsedEndpoint')
            ? $this->fallbackProvider->getLastUsedEndpoint()
            : null;

        Log::info('Busca de empresas realizada com sucesso', [
            'provider' => 'Overpass',
            'execution_time_ms' => $durationMs,
            'results_count' => count($results),
            'fallback_reason' => $fallbackReason,
            'overpass_endpoint' => $endpointUsed,
        ]);

        $this->saveToCache($hash, $searchDTO, $results);

        return $results;
    }

    /**
     * Salva ou atualiza os resultados da busca no cache de banco de dados.
     *
     * @param array<CompanyResultDTO> $results
     */
    protected function saveToCache(string $hash, SearchLocationDTO $searchDTO, array $results): void
    {
        $serializedResults = array_map(fn(CompanyResultDTO $dto) => $dto->toArray(), $results);

        CompanySearchCache::updateOrCreate(
            ['search_hash' => $hash],
            [
                'latitude' => $searchDTO->latitude,
                'longitude' => $searchDTO->longitude,
                'radius' => $searchDTO->radius,
                'category' => $searchDTO->category,
                'response_data' => $serializedResults,
                'consulted_at' => now(),
            ]
        );
    }
}
