<?php

namespace App\Services\Location\DTOs;

class SearchLocationDTO
{
    public function __construct(
        public float $latitude,
        public float $longitude,
        public int $radius = 1000,
        public ?string $category = null,
        public string $rawQuery = '',
        public string $searchType = 'address'
    ) {}

    public function cacheHash(): string
    {
        $normalizedCategory = strtolower(trim($this->category ?? 'all'));
        $latRounded = round($this->latitude, 4);
        $lngRounded = round($this->longitude, 4);

        return md5("v3|{$latRounded}|{$lngRounded}|{$this->radius}|{$normalizedCategory}");
    }
}
