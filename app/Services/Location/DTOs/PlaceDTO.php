<?php

namespace App\Services\Location\DTOs;

class PlaceDTO
{
    public function __construct(
        public ?string $id,
        public string $name,
        public ?string $category,
        public float $latitude,
        public float $longitude,
        public ?string $address = null,
        public ?string $city = null,
        public ?string $state = null,
        public ?string $country = null,
        public ?string $phone = null,
        public ?string $website = null,
        public ?string $openingHours = null,
        public ?float $rating = null,
        public int $reviewsCount = 0,
        public ?string $provider = null,
        public ?string $whatsapp = null,
        public ?string $instagram = null,
        public ?string $facebook = null,
        public ?bool $isOpenNow = null,
        public ?string $neighborhood = null,
        public ?string $postalCode = null,
        public array $rawData = []
    ) {}

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'osm_id' => $this->id,
            'name' => $this->name,
            'category' => $this->category,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'address' => $this->address,
            'city' => $this->city,
            'state' => $this->state,
            'country' => $this->country,
            'phone' => $this->phone,
            'website' => $this->website,
            'opening_hours' => $this->openingHours,
            'rating' => $this->rating,
            'reviews_count' => $this->reviewsCount,
            'review_count' => $this->reviewsCount,
            'provider' => $this->provider,
            'whatsapp' => $this->whatsapp,
            'instagram' => $this->instagram,
            'facebook' => $this->facebook,
            'is_open_now' => $this->isOpenNow,
            'neighborhood' => $this->neighborhood,
            'postal_code' => $this->postalCode,
            'has_website' => !empty($this->website),
            'has_whatsapp' => !empty($this->whatsapp),
            'has_phone' => !empty($this->phone),
            'has_instagram' => !empty($this->instagram),
            'has_facebook' => !empty($this->facebook),
            'raw_data' => $this->rawData,
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? ($data['osm_id'] ?? null),
            name: $data['name'] ?? 'Empresa',
            category: $data['category'] ?? 'default',
            latitude: (float) ($data['latitude'] ?? 0),
            longitude: (float) ($data['longitude'] ?? 0),
            address: $data['address'] ?? null,
            city: $data['city'] ?? null,
            state: $data['state'] ?? null,
            country: $data['country'] ?? null,
            phone: $data['phone'] ?? null,
            website: $data['website'] ?? null,
            openingHours: $data['opening_hours'] ?? null,
            rating: isset($data['rating']) ? (float) $data['rating'] : null,
            reviewsCount: (int) ($data['reviews_count'] ?? ($data['review_count'] ?? 0)),
            provider: $data['provider'] ?? null,
            whatsapp: $data['whatsapp'] ?? null,
            instagram: $data['instagram'] ?? null,
            facebook: $data['facebook'] ?? null,
            isOpenNow: $data['is_open_now'] ?? null,
            neighborhood: $data['neighborhood'] ?? null,
            postalCode: $data['postal_code'] ?? null,
            rawData: $data['raw_data'] ?? []
        );
    }
}
