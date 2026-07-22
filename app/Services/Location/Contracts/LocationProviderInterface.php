<?php

namespace App\Services\Location\Contracts;

use App\Services\Location\DTOs\PlaceDTO;
use App\Services\Location\DTOs\SearchLocationDTO;
use Illuminate\Support\Collection;

interface LocationProviderInterface
{
    /**
     * Convert an address, CEP, city, or query into latitude and longitude coordinates.
     *
     * @return array{latitude: float, longitude: float, display_name: string, city: ?string}|null
     */
    public function geocode(string $query): ?array;

    /**
     * Search companies around a geographic coordinate within a given radius.
     *
     * @return Collection<int, PlaceDTO>|array<int, PlaceDTO>
     */
    public function searchCompanies(SearchLocationDTO $searchDTO): Collection|array;
}
