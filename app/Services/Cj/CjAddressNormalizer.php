<?php

namespace App\Services\Cj;

class CjAddressNormalizer
{
    private static array $countryMap = [
        'united states' => 'US',
        'united states of america' => 'US',
        'usa' => 'US',
        'us' => 'US',
        'united kingdom' => 'GB',
        'uk' => 'GB',
        'great britain' => 'GB',
        'gb' => 'GB',
        'england' => 'GB',
        'canada' => 'CA',
        'ca' => 'CA',
        'australia' => 'AU',
        'au' => 'AU',
        'germany' => 'DE',
        'deutschland' => 'DE',
        'de' => 'DE',
        'france' => 'FR',
        'fr' => 'FR',
        'india' => 'IN',
        'in' => 'IN',
        'united arab emirates' => 'AE',
        'uae' => 'AE',
        'ae' => 'AE',
        'singapore' => 'SG',
        'sg' => 'SG',
        'netherlands' => 'NL',
        'nl' => 'NL',
        'italy' => 'IT',
        'it' => 'IT',
        'spain' => 'ES',
        'es' => 'ES',
        'new zealand' => 'NZ',
        'nz' => 'NZ',
        'ireland' => 'IE',
        'ie' => 'IE',
    ];

    public static function normalizeCountryCode(?string $country): string
    {
        if (empty($country)) {
            return 'US';
        }

        $clean = strtolower(trim($country));
        if (isset(self::$countryMap[$clean])) {
            return self::$countryMap[$clean];
        }

        if (strlen($clean) === 2) {
            return strtoupper($clean);
        }

        return 'US';
    }

    public static function cleanPhone(?string $phone): string
    {
        if (empty($phone)) {
            return '';
        }
        $cleaned = preg_replace('/[^\d+]/', '', trim($phone));
        return $cleaned ?: '';
    }
}
