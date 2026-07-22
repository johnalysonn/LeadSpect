<?php

namespace App\Services\Location\Providers;

use App\Models\CompanySearchCache;
use App\Services\Location\Contracts\LocationProviderInterface;
use App\Services\Location\DTOs\PlaceDTO;
use App\Services\Location\DTOs\SearchLocationDTO;
use Illuminate\Support\Collection;
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
     * @return Collection<int, PlaceDTO>
     */
    public function searchCompanies(SearchLocationDTO $searchDTO): Collection
    {
        $hash = $searchDTO->cacheHash();

        // 1. Verificar Cache em Banco de Dados antes de chamar qualquer API
        $cached = CompanySearchCache::where('search_hash', $hash)->first();

        if ($cached && $cached->consulted_at->gt(now()->subDays(7))) {
            $places = array_map(
                fn($item) => PlaceDTO::fromArray($item),
                $cached->response_data
            );

            $collection = collect($places);

            Log::info('Busca de empresas retornada do cache', [
                'provider' => 'Cache',
                'execution_time_ms' => 0.0,
                'results_count' => $collection->count(),
            ]);

            return $collection;
        }

        // 2. Tentar Provider Principal (TomTom)
        $startTime = microtime(true);
        $fallbackReason = null;

        try {
            $results = $this->primaryProvider->searchCompanies($searchDTO);
            $collection = $results instanceof Collection ? $results : collect($results);
            $durationMs = round((microtime(true) - $startTime) * 1000, 2);

            Log::info('Busca de empresas realizada com sucesso', [
                'provider' => 'TomTom',
                'execution_time_ms' => $durationMs,
                'results_count' => $collection->count(),
            ]);

            $this->saveToCache($hash, $searchDTO, $collection);

            return $collection;
        } catch (\Throwable $e) {
            $fallbackReason = $e->getMessage();

            Log::warning('Fallback de busca ativado de TomTom para Overpass', [
                'fallback_reason' => $fallbackReason,
            ]);
        }

        // 3. Fallback para Provider Secundário (Overpass)
        $overpassStartTime = microtime(true);
        $results = $this->fallbackProvider->searchCompanies($searchDTO);
        $collection = $results instanceof Collection ? $results : collect($results);
        $durationMs = round((microtime(true) - $overpassStartTime) * 1000, 2);

        $endpointUsed = method_exists($this->fallbackProvider, 'getLastUsedEndpoint')
            ? $this->fallbackProvider->getLastUsedEndpoint()
            : null;

        Log::info('Busca de empresas realizada com sucesso', [
            'provider' => 'Overpass',
            'execution_time_ms' => $durationMs,
            'results_count' => $collection->count(),
            'fallback_reason' => $fallbackReason,
            'overpass_endpoint' => $endpointUsed,
        ]);

        $this->saveToCache($hash, $searchDTO, $collection);

        return $collection;
    }

    /**
     * Salva ou atualiza os resultados da busca no cache de banco de dados.
     *
     * @param Collection<int, PlaceDTO> $results
     */
    protected function saveToCache(string $hash, SearchLocationDTO $searchDTO, Collection $results): void
    {
        $serializedResults = $results->map(fn(PlaceDTO $dto) => $dto->toArray())->values()->all();

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
