<?php

namespace App\Services\Location\Mappers;

use App\Services\Location\DTOs\PlaceDTO;

class TomTomResponseMapper
{
    /**
     * Map a raw item from TomTom Search API response to a PlaceDTO instance.
     */
    public static function map(array $item, ?string $requestedCategory = null): ?PlaceDTO
    {
        $poi = $item['poi'] ?? [];
        $address = $item['address'] ?? [];
        $position = $item['position'] ?? [];

        $name = $poi['name'] ?? null;
        if (empty($name)) {
            return null;
        }

        $lat = $position['lat'] ?? null;
        $lng = $position['lon'] ?? null;
        if ($lat === null || $lng === null) {
            return null;
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

        // Extrair string bruta da categoria
        $rawCategory = $requestedCategory;
        if (!empty($poi['classifications'][0]['names'][0]['name'])) {
            $rawCategory = $poi['classifications'][0]['names'][0]['name'];
        } elseif (!empty($poi['categories'][0])) {
            $rawCategory = $poi['categories'][0];
        }

        $normalizedCategory = CategoryMapper::normalize($rawCategory, $name);

        $street = $address['streetName'] ?? null;
        $number = $address['streetNumber'] ?? null;
        $formattedAddress = $street ? ($number ? "{$street}, {$number}" : $street) : ($address['freeformAddress'] ?? null);

        $city = $address['municipality'] ?? null;
        $state = $address['countrySubdivision'] ?? null;
        $country = $address['country'] ?? 'Brasil';
        $neighborhood = $address['municipalitySubdivision'] ?? null;
        $postalCode = $address['postalCode'] ?? null;

        $id = 'tomtom_' . ($item['id'] ?? md5($name . $lat . $lng));

        return new PlaceDTO(
            id: $id,
            name: $name,
            category: $normalizedCategory,
            latitude: (float) $lat,
            longitude: (float) $lng,
            address: $formattedAddress,
            city: $city,
            state: $state,
            country: $country,
            phone: $phone,
            website: $website,
            openingHours: null,
            rating: null,
            reviewsCount: 0,
            provider: 'TomTom',
            whatsapp: $whatsapp,
            instagram: null,
            facebook: null,
            isOpenNow: null,
            neighborhood: $neighborhood,
            postalCode: $postalCode,
            rawData: $item
        );
    }
}
