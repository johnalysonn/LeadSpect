<?php

namespace App\Services\Location\Mappers;

use App\Services\Location\DTOs\PlaceDTO;

class OverpassResponseMapper
{
    /**
     * Map a raw element item from Overpass API response to a PlaceDTO instance.
     */
    public static function map(array $element, ?string $requestedCategory = null): ?PlaceDTO
    {
        $tags = $element['tags'] ?? [];
        $name = $tags['name'] ?? null;
        if (empty($name)) {
            return null;
        }

        $lat = $element['lat'] ?? ($element['center']['lat'] ?? null);
        $lng = $element['lon'] ?? ($element['center']['lon'] ?? null);

        if ($lat === null || $lng === null) {
            return null;
        }

        $phone = $tags['phone'] ?? $tags['contact:phone'] ?? null;
        $whatsapp = $tags['whatsapp'] ?? $tags['contact:whatsapp'] ?? null;
        $website = $tags['website'] ?? $tags['contact:website'] ?? $tags['url'] ?? null;

        if (!$whatsapp && $phone && preg_match('/(?:9\d{8}|\+55.*9\d{8})/', $phone)) {
            $whatsapp = $phone;
        }

        $rawCategory = $tags['amenity'] ?? $tags['shop'] ?? $tags['office'] ?? $tags['craft'] ?? $tags['leisure'] ?? $tags['tourism'] ?? $tags['healthcare'] ?? $requestedCategory;
        $normalizedCategory = CategoryMapper::normalize($rawCategory, $name);

        $street = $tags['addr:street'] ?? null;
        $housenumber = $tags['addr:housenumber'] ?? null;
        $address = $street ? ($housenumber ? "{$street}, {$housenumber}" : $street) : null;

        $city = $tags['addr:city'] ?? null;
        $state = $tags['addr:state'] ?? null;
        $country = $tags['addr:country'] ?? 'Brasil';
        $neighborhood = $tags['addr:suburb'] ?? $tags['addr:neighbourhood'] ?? null;
        $postalCode = $tags['addr:postcode'] ?? null;

        $id = (string) ($element['id'] ?? md5($name . $lat . $lng));

        return new PlaceDTO(
            id: $id,
            name: $name,
            category: $normalizedCategory,
            latitude: (float) $lat,
            longitude: (float) $lng,
            address: $address,
            city: $city,
            state: $state,
            country: $country,
            phone: $phone,
            website: $website,
            openingHours: $tags['opening_hours'] ?? null,
            rating: null,
            reviewsCount: 0,
            provider: 'Overpass',
            whatsapp: $whatsapp,
            instagram: $tags['contact:instagram'] ?? null,
            facebook: $tags['contact:facebook'] ?? null,
            isOpenNow: null,
            neighborhood: $neighborhood,
            postalCode: $postalCode,
            rawData: $tags
        );
    }
}
