<?php

namespace App\Services\Location\Providers;

use App\Services\Location\Contracts\LocationProviderInterface;
use App\Services\Location\DTOs\CompanyResultDTO;
use App\Services\Location\DTOs\SearchLocationDTO;
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
     * @return array<CompanyResultDTO>
     */
    public function searchCompanies(SearchLocationDTO $searchDTO): array
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

            $companyDTOs = [];
            foreach ($results as $item) {
                $poi = $item['poi'] ?? [];
                $address = $item['address'] ?? [];
                $position = $item['position'] ?? [];

                $name = $poi['name'] ?? null;
                if (empty($name)) {
                    continue;
                }

                $elemLat = $position['lat'] ?? null;
                $elemLng = $position['lon'] ?? null;
                if ($elemLat === null || $elemLng === null) {
                    continue;
                }

                $phone = $poi['phone'] ?? null;
                $whatsapp = null;
                if ($phone) {
                    $cleanPhone = preg_replace('/\D/', '', $phone);
                    if (preg_match('/^(?:55)?\d{2}9\d{8}$/', $cleanPhone)) {
                        $whatsapp = $phone;
                    }
                }

                $website = $poi['url'] ?? null;

                // Categoria formatada
                $categoryName = $searchDTO->category;
                if (!empty($poi['classifications'][0]['names'][0]['name'])) {
                    $categoryName = $poi['classifications'][0]['names'][0]['name'];
                } elseif (!empty($poi['categories'][0])) {
                    $categoryName = ucfirst(str_replace('_', ' ', $poi['categories'][0]));
                }

                $street = $address['streetName'] ?? null;
                $number = $address['streetNumber'] ?? null;
                $formattedAddress = $street ? ($number ? "{$street}, {$number}" : $street) : ($address['freeformAddress'] ?? null);

                $companyDTOs[] = new CompanyResultDTO(
                    osmId: 'tomtom_' . ($item['id'] ?? md5($name . $elemLat . $elemLng)),
                    name: $name,
                    category: $categoryName ?? 'Estabelecimento',
                    address: $formattedAddress,
                    city: $address['municipality'] ?? null,
                    neighborhood: $address['municipalitySubdivision'] ?? null,
                    postalCode: $address['postalCode'] ?? null,
                    latitude: (float) $elemLat,
                    longitude: (float) $elemLng,
                    phone: $phone,
                    whatsapp: $whatsapp,
                    website: $website,
                    instagram: null,
                    facebook: null,
                    rating: null,
                    reviewCount: 0,
                    isOpenNow: null,
                    openingHours: null,
                    rawData: $item
                );
            }

            return $companyDTOs;
        } catch (\Throwable $e) {
            if ($e instanceof RuntimeException) {
                throw $e;
            }
            throw new RuntimeException("Erro ao consultar TomTom Search API: {$e->getMessage()}", 0, $e);
        }
    }
}
