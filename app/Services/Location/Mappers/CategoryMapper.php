<?php

namespace App\Services\Location\Mappers;

class CategoryMapper
{
    /**
     * Map of keywords to standardized category slugs.
     */
    protected static array $categoryRules = [
        'pharmacy' => ['pharmacy', 'farmacia', 'farmácia', 'drogaria', 'drugstore', 'chemist', 'remedios', 'medicamento', 'drogas'],
        'restaurant' => ['restaurant', 'restaurante', 'pizzeria', 'pizzaria', 'churrascaria', 'lanchonete', 'burger', 'hamburgueria', 'fast_food', 'food', 'comida', 'gastronomia', 'bistro', 'bistrô', 'grill', 'snack', 'pastelaria', 'espetinho', 'sushi', 'temakeria'],
        'bar' => ['bar', 'pub', 'choperia', 'boteco', 'cervejaria', 'lounge', 'nightclub', 'boate', 'adega'],
        'cafe' => ['cafe', 'café', 'cafeteria', 'coffee', 'bakery', 'padaria', 'panificadora', 'confeitaria', 'doceira', 'sorveteria', 'gelateria'],
        'hospital' => ['hospital', 'pronto_socorro', 'pronto socorro', 'upa', 'emergencia', 'urgencia'],
        'clinic' => ['clinic', 'clinica', 'clínica', 'doctors', 'consultorio', 'consultório', 'medico', 'médico', 'healthcare', 'laboratorio', 'laboratório', 'diagnostico', 'diagnóstico', 'fisioterapia', 'psicologia', 'oftalmologia', 'dermatologia'],
        'dentist' => ['dentist', 'dentista', 'odontologia', 'odonto', 'ortodontia', 'sorriso'],
        'gym' => ['gym', 'fitness', 'academia', 'crossfit', 'treino', 'musculacao', 'musculação', 'pilates', 'sports_centre', 'sports_center', 'fitness_centre', 'fitness_center', 'gymnasium', 'health_club', 'jiu', 'judo', 'boxe', 'natacao', 'natação'],
        'hotel' => ['hotel', 'pousada', 'hostel', 'guest_house', 'lodging', 'resort', 'hospedagem', 'motel', 'flat'],
        'supermarket' => ['supermarket', 'supermercado', 'mercado', 'hypermarket', 'hipermercado', 'convenience', 'conveniencia', 'conveniência', 'mercearia', 'hortifruti', 'sacolao', 'sacolão', 'atacadao', 'atacadão', 'atacado'],
        'shop' => ['shop', 'store', 'loja', 'retail', 'varejo', 'boutique', 'clothes', 'vestuario', 'vestuário', 'calcados', 'calçados', 'eletronicos', 'eletrônicos', 'celular', 'shopping', 'hardware', 'ferragens', 'optician', 'otica', 'ótica'],
        'auto_repair' => ['auto_repair', 'car_repair', 'oficina', 'mecanica', 'mecânica', 'mecanico', 'mecânico', 'auto pecas', 'auto peças', 'lataria', 'funilaria', 'pneus', 'borracharia', 'car_wash', 'lava rapido', 'lava rápido'],
        'gas_station' => ['gas_station', 'posto', 'combustivel', 'combustível', 'gasolina', 'fuel', 'br', 'ipiranga', 'shell'],
        'school' => ['school', 'escola', 'colegio', 'colégio', 'university', 'faculdade', 'universidade', 'educacao', 'educação', 'ensino', 'curso', 'creche', 'kindergarten'],
        'bank' => ['bank', 'banco', 'financeira', 'cooperativa', 'caixa', 'bradesco', 'itau', 'santander', 'credito', 'crédito', 'atm'],
        'pet_shop' => ['pet', 'pet_shop', 'petshop', 'veterinary', 'veterinaria', 'veterinária', 'vet', 'animais', 'banho e tosa'],
        'beauty_salon' => ['hairdresser', 'salao', 'salão', 'barber', 'barbearia', 'beauty', 'estetica', 'estética', 'manicure', 'sobrancelhas', 'spa', 'cabeleireiro'],
        'office' => ['office', 'escritorio', 'escritório', 'advocacia', 'advogado', 'lawyer', 'contabilidade', 'consultoria', 'cartorio', 'cartório'],
        'company' => ['company', 'empresa', 'corporate', 'negocio', 'negócio', 'industria', 'indústria', 'tecnologia', 'software', 'agencia', 'agência'],
    ];

    /**
     * Icon mapping for categories.
     */
    protected static array $iconMap = [
        'pharmacy' => 'pharmacy-icon',
        'restaurant' => 'restaurant-icon',
        'bar' => 'bar-icon',
        'hospital' => 'hospital-icon',
        'clinic' => 'clinic-icon',
        'dentist' => 'dentist-icon',
        'cafe' => 'cafe-icon',
        'gym' => 'gym-icon',
        'hotel' => 'hotel-icon',
        'supermarket' => 'supermarket-icon',
        'shop' => 'shop-icon',
        'auto_repair' => 'auto-repair-icon',
        'gas_station' => 'gas-station-icon',
        'school' => 'school-icon',
        'bank' => 'bank-icon',
        'pet_shop' => 'pet-icon',
        'beauty_salon' => 'beauty-icon',
        'office' => 'office-icon',
        'company' => 'company-icon',
        'default' => 'default-icon',
    ];

    /**
     * Normalize a raw category string or company name into a standardized category slug.
     */
    public static function normalize(?string $rawCategory, ?string $name = null): string
    {
        $catSlug = self::matchSlug($rawCategory);
        if ($catSlug !== 'default') {
            return $catSlug;
        }

        if (!empty($name)) {
            $nameSlug = self::matchSlug($name);
            if ($nameSlug !== 'default') {
                return $nameSlug;
            }
        }

        return 'default';
    }

    protected static function matchSlug(?string $string): string
    {
        if (empty($string)) {
            return 'default';
        }

        $clean = strtolower(trim($string));
        $clean = str_replace(['_', '-'], ' ', $clean);

        // Remove accents
        $unaccented = self::removeAccents($clean);

        foreach (self::$categoryRules as $slug => $keywords) {
            foreach ($keywords as $kw) {
                if (str_contains($unaccented, $kw) || str_contains($clean, $kw)) {
                    return $slug;
                }
            }
        }

        return 'default';
    }

    /**
     * Map a normalized category slug or raw category to an icon identifier.
     */
    public static function toIcon(?string $category, ?string $name = null): string
    {
        $normalized = self::normalize($category, $name);
        return self::$iconMap[$normalized] ?? self::$iconMap['default'];
    }

    protected static function removeAccents(string $string): string
    {
        $map = [
            'á'=>'a','à'=>'a','â'=>'a','ã'=>'a','ä'=>'a',
            'é'=>'e','è'=>'e','ê'=>'e','ë'=>'e',
            'í'=>'i','ì'=>'i','î'=>'i','ï'=>'i',
            'ó'=>'o','ò'=>'o','ô'=>'o','õ'=>'o','ö'=>'o',
            'ú'=>'u','ù'=>'u','û'=>'u','ü'=>'u',
            'ç'=>'c'
        ];
        return strtr($string, $map);
    }
}
