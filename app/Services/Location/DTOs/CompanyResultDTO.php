<?php

namespace App\Services\Location\DTOs;

class CompanyResultDTO
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
    ) {}

    public function toArray(): array
    {
        return [
            'osm_id' => $this->osmId,
            'name' => $this->name,
            'category' => $this->category,
            'address' => $this->address,
            'city' => $this->city,
            'neighborhood' => $this->neighborhood,
            'postal_code' => $this->postalCode,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'phone' => $this->phone,
            'whatsapp' => $this->whatsapp,
            'website' => $this->website,
            'instagram' => $this->instagram,
            'facebook' => $this->facebook,
            'rating' => $this->rating,
            'review_count' => $this->reviewCount,
            'is_open_now' => $this->isOpenNow,
            'opening_hours' => $this->openingHours,
            'has_website' => !empty($this->website),
            'has_whatsapp' => !empty($this->whatsapp),
            'has_phone' => !empty($this->phone),
            'has_instagram' => !empty($this->instagram),
            'has_facebook' => !empty($this->facebook),
            'raw_data' => $this->rawData,
        ];
    }
}
