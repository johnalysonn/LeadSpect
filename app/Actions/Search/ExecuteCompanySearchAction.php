<?php

namespace App\Actions\Search;

use App\Models\SearchHistory;
use App\Models\User;
use App\Services\Location\Contracts\LocationProviderInterface;
use App\Services\Location\DTOs\SearchLocationDTO;

class ExecuteCompanySearchAction
{
    public function __construct(
        protected LocationProviderInterface $locationProvider
    ) {}

    public function execute(User $user, array $params): array
    {
        $query = trim($params['query'] ?? '');
        $lat = isset($params['latitude']) ? (float) $params['latitude'] : null;
        $lng = isset($params['longitude']) ? (float) $params['longitude'] : null;
        $radius = isset($params['radius']) ? (int) $params['radius'] : 1000;
        $category = !empty($params['category']) ? trim($params['category']) : null;
        $searchType = $params['search_type'] ?? 'address';

        $city = null;

        // Se latitude e longitude não foram informadas, geocodificar termo de busca
        if (($lat === null || $lng === null) && !empty($query)) {
            $geocoded = $this->locationProvider->geocode($query);
            if ($geocoded) {
                $lat = $geocoded['latitude'];
                $lng = $geocoded['longitude'];
                $city = $geocoded['city'] ?? null;
            }
        }

        // Fallback se geocodificação falhar: centro de São Paulo / padrão
        if ($lat === null || $lng === null) {
            $lat = -23.550520;
            $lng = -46.633309;
            $query = $query ?: 'São Paulo, SP';
            $city = 'São Paulo';
        }

        // Se a cidade ainda é desconhecida e temos lat/lng, reverse-geocodificar
        if ($city === null && $lat !== null && $lng !== null) {
            if (method_exists($this->locationProvider, 'reverseGeocodeCity')) {
                $city = $this->locationProvider->reverseGeocodeCity($lat, $lng);
            }
        }

        // Se temos a cidade identificada, buscar limite territorial dela
        $cityBoundary = null;
        if ($city && method_exists($this->locationProvider, 'getCityBoundary')) {
            $cityBoundary = $this->locationProvider->getCityBoundary($city);
        }

        $searchDTO = new SearchLocationDTO(
            latitude: $lat,
            longitude: $lng,
            radius: $radius,
            category: $category,
            rawQuery: $query,
            searchType: $searchType
        );

        $companiesDTOs = $this->locationProvider->searchCompanies($searchDTO);

        // Se a cidade ainda for desconhecida, tentar extrair dos resultados retornados
        if ($city === null && !empty($companiesDTOs)) {
            foreach ($companiesDTOs as $dto) {
                if (!empty($dto->city)) {
                    $city = $dto->city;
                    break;
                }
            }
        }

        // Gravar Histórico de Busca do Usuário
        SearchHistory::create([
            'user_id' => $user->id,
            'search_term' => $query ?: "{$lat}, {$lng}",
            'search_type' => $searchType,
            'latitude' => $lat,
            'longitude' => $lng,
            'radius' => $radius,
            'category' => $category,
            'results_count' => count($companiesDTOs),
        ]);

        return [
            'center' => [
                'latitude' => $lat,
                'longitude' => $lng,
                'query' => $query,
                'city' => $city,
            ],
            'city_geojson' => $cityBoundary,
            'radius' => $radius,
            'category' => $category,
            'total' => count($companiesDTOs),
            'companies' => array_map(fn($dto) => $dto->toArray(), $companiesDTOs),
        ];
    }
}
