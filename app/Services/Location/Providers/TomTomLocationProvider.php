<?php

namespace App\Services\Location\Providers;

use App\Services\Location\Contracts\LocationProviderInterface;
use App\Services\Location\DTOs\PlaceDTO;
use App\Services\Location\DTOs\SearchLocationDTO;
use App\Services\Location\Mappers\TomTomResponseMapper;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class TomTomLocationProvider implements LocationProviderInterface
{
    protected string $apiKey;

    public function __construct(?string $apiKey = null)
    {
        $this->apiKey = $apiKey ?? (string) config('services.tomtom.key', '');
    }

    /**
     * Convert address, city, CEP or query into latitude and longitude coordinates via TomTom.
     *
     * @return array{latitude: float, longitude: float, display_name: string, city: ?string}|null
     */
    public function geocode(string $query): ?array
    {
        if (empty($this->apiKey)) {
            throw new RuntimeException('TomTom API Key não configurada (TOMTOM_API_KEY)');
        }

        $cleanQuery = trim($query);
        if (empty($cleanQuery)) {
            return null;
        }

        // Se for par de coordenadas GPS (lat, lng), retornar direto
        if (preg_match('/^\s*(-?\d+(?:\.\d+)?)\s*,\s*(-?\d+(?:\.\d+)?)\s*$/', $cleanQuery, $matches)) {
            return [
                'latitude' => (float) $matches[1],
                'longitude' => (float) $matches[2],
                'display_name' => "Coordenadas ({$matches[1]}, {$matches[2]})",
                'city' => null,
            ];
        }

        try {
            $url = 'https://api.tomtom.com/search/2/geocode/' . rawurlencode($cleanQuery) . '.json';

            $response = Http::timeout(6)->get($url, [
                'key' => $this->apiKey,
                'limit' => 1,
                'language' => 'pt-BR',
            ]);

            if ($response->status() === 429) {
                throw new RuntimeException('TomTom Geocode HTTP 429 (Limite de requisições excedido)');
            }

            if (!$response->successful()) {
                throw new RuntimeException("TomTom Geocode HTTP Erro {$response->status()}");
            }

            $data = $response->json();
            $results = $data['results'] ?? [];

            if (empty($results)) {
                return null;
            }

            $item = $results[0];
            $position = $item['position'] ?? [];
            $address = $item['address'] ?? [];

            $city = $address['municipality'] ?? $address['countrySubdivision'] ?? null;

            return [
                'latitude' => (float) ($position['lat'] ?? 0),
                'longitude' => (float) ($position['lon'] ?? 0),
                'display_name' => $address['freeformAddress'] ?? $cleanQuery,
                'city' => $city,
            ];
        } catch (\Throwable $e) {
            if ($e instanceof RuntimeException) {
                throw $e;
            }
            throw new RuntimeException("Erro ao geocodificar via TomTom: {$e->getMessage()}", 0, $e);
        }
    }

    /**
     * Search companies around a geographic coordinate using TomTom Search API.
     *
     * @return Collection<int, PlaceDTO>
     */
    public function searchCompanies(SearchLocationDTO $searchDTO): Collection
    {
        if (empty($this->apiKey)) {
            throw new RuntimeException('TomTom API Key não configurada (TOMTOM_API_KEY)');
        }

        $lat = $searchDTO->latitude;
        $lng = $searchDTO->longitude;
        $radius = $searchDTO->radius;
        $searchTerm = trim($searchDTO->category ?? $searchDTO->rawQuery);

        if (!empty($searchTerm)) {
            $url = 'https://api.tomtom.com/search/2/search/' . rawurlencode($searchTerm) . '.json';
            $queryParams = [
                'key' => $this->apiKey,
                'lat' => $lat,
                'lon' => $lng,
                'radius' => $radius,
                'limit' => 100,
                'idxSet' => 'POI',
                'language' => 'pt-BR',
            ];
        } else {
            $url = 'https://api.tomtom.com/search/2/nearbySearch.json';
            $queryParams = [
                'key' => $this->apiKey,
                'lat' => $lat,
                'lon' => $lng,
                'radius' => $radius,
                'limit' => 100,
                'idxSet' => 'POI',
                'language' => 'pt-BR',
            ];
        }

        try {
            $response = Http::timeout(8)->get($url, $queryParams);

            if ($response->status() === 429) {
                throw new RuntimeException('TomTom API HTTP 429 (Limite de requisições excedido)');
            }

            if (!$response->successful()) {
                throw new RuntimeException("TomTom API HTTP Erro {$response->status()}: " . $response->body());
            }

            $data = $response->json();
            $results = $data['results'] ?? [];

            $places = [];
            foreach ($results as $item) {
                $placeDTO = TomTomResponseMapper::map($item, $searchDTO->category);
                if ($placeDTO !== null) {
                    $places[] = $placeDTO;
                }
            }

            return collect($places);
        } catch (\Throwable $e) {
            if ($e instanceof RuntimeException) {
                throw $e;
            }
            throw new RuntimeException("Erro ao consultar TomTom Search API: {$e->getMessage()}", 0, $e);
        }
    }
}
