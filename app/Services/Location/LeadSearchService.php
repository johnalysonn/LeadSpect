<?php

namespace App\Services\Location;

use App\Services\Location\Contracts\LocationProviderInterface;
use App\Services\Location\DTOs\PlaceDTO;
use App\Services\Location\DTOs\SearchLocationDTO;
use Illuminate\Support\Collection;

class LeadSearchService
{
    public function __construct(
        protected LocationProviderInterface $locationProvider
    ) {}

    /**
     * Search places around a location and return a Collection of PlaceDTOs.
     *
     * @return Collection<int, PlaceDTO>
     */
    public function search(SearchLocationDTO $searchDTO): Collection
    {
        $places = $this->locationProvider->searchCompanies($searchDTO);

        if ($places instanceof Collection) {
            return $places;
        }

        return collect($places);
    }
}
