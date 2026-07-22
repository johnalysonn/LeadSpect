<?php

namespace App\Services\Location\DTOs;

/**
 * @deprecated Use PlaceDTO instead.
 */
class CompanyResultDTO extends PlaceDTO
{
    public function __construct(
        public ?string $osmId,
        public string $name,
        public ?string $category,
        public ?string $address,
        public ?string $city,
        public ?string $neighborhood,
        public ?string $postalCode,
        public float $latitude,
        public float $longitude,
        public ?string $phone = null,
        public ?string $whatsapp = null,
        public ?string $website = null,
        public ?string $instagram = null,
        public ?string $facebook = null,
        public ?float $rating = null,
        public int $reviewCount = 0,
        public ?bool $isOpenNow = null,
        public ?string $openingHours = null,
        public array $rawData = []
    ) {
        parent::__construct(
            id: $osmId,
            name: $name,
            category: $category,
            latitude: $latitude,
            longitude: $longitude,
            address: $address,
            city: $city,
            state: null,
            country: 'Brasil',
            phone: $phone,
            website: $website,
            openingHours: $openingHours,
            rating: $rating,
            reviewsCount: $reviewCount,
            provider: $rawData['provider'] ?? null,
            whatsapp: $whatsapp,
            instagram: $instagram,
            facebook: $facebook,
            isOpenNow: $isOpenNow,
            neighborhood: $neighborhood,
            postalCode: $postalCode,
            rawData: $rawData
        );
    }
}
