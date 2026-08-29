<?php

namespace App\Services\Seo;

class UsGeoDataService
{
    /**
     * All 50 United States with postal codes, capitals, regions, and delivery timelines.
     */
    protected static array $states = [
        'alabama' => ['name' => 'Alabama', 'code' => 'AL', 'capital' => 'Montgomery', 'region' => 'South', 'transit_days' => '3-5'],
        'alaska' => ['name' => 'Alaska', 'code' => 'AK', 'capital' => 'Juneau', 'region' => 'West', 'transit_days' => '5-7'],
        'arizona' => ['name' => 'Arizona', 'code' => 'AZ', 'capital' => 'Phoenix', 'region' => 'West', 'transit_days' => '3-4'],
        'arkansas' => ['name' => 'Arkansas', 'code' => 'AR', 'capital' => 'Little Rock', 'region' => 'South', 'transit_days' => '3-5'],
        'california' => ['name' => 'California', 'code' => 'CA', 'capital' => 'Sacramento', 'region' => 'West', 'transit_days' => '3-4'],
        'colorado' => ['name' => 'Colorado', 'code' => 'CO', 'capital' => 'Denver', 'region' => 'West', 'transit_days' => '3-4'],
        'connecticut' => ['name' => 'Connecticut', 'code' => 'CT', 'capital' => 'Hartford', 'region' => 'Northeast', 'transit_days' => '3-5'],
        'delaware' => ['name' => 'Delaware', 'code' => 'DE', 'capital' => 'Dover', 'region' => 'Northeast', 'transit_days' => '3-5'],
        'florida' => ['name' => 'Florida', 'code' => 'FL', 'capital' => 'Tallahassee', 'region' => 'South', 'transit_days' => '3-4'],
        'georgia' => ['name' => 'Georgia', 'code' => 'GA', 'capital' => 'Atlanta', 'region' => 'South', 'transit_days' => '3-4'],
        'hawaii' => ['name' => 'Hawaii', 'code' => 'HI', 'capital' => 'Honolulu', 'region' => 'West', 'transit_days' => '5-7'],
        'idaho' => ['name' => 'Idaho', 'code' => 'ID', 'capital' => 'Boise', 'region' => 'West', 'transit_days' => '3-5'],
        'illinois' => ['name' => 'Illinois', 'code' => 'IL', 'capital' => 'Springfield', 'region' => 'Midwest', 'transit_days' => '3-4'],
        'indiana' => ['name' => 'Indiana', 'code' => 'IN', 'capital' => 'Indianapolis', 'region' => 'Midwest', 'transit_days' => '3-4'],
        'iowa' => ['name' => 'Iowa', 'code' => 'IA', 'capital' => 'Des Moines', 'region' => 'Midwest', 'transit_days' => '3-5'],
        'kansas' => ['name' => 'Kansas', 'code' => 'KS', 'capital' => 'Topeka', 'region' => 'Midwest', 'transit_days' => '3-5'],
        'kentucky' => ['name' => 'Kentucky', 'code' => 'KY', 'capital' => 'Frankfort', 'region' => 'South', 'transit_days' => '3-4'],
        'louisiana' => ['name' => 'Louisiana', 'code' => 'LA', 'capital' => 'Baton Rouge', 'region' => 'South', 'transit_days' => '3-5'],
        'maine' => ['name' => 'Maine', 'code' => 'ME', 'capital' => 'Augusta', 'region' => 'Northeast', 'transit_days' => '4-6'],
        'maryland' => ['name' => 'Maryland', 'code' => 'MD', 'capital' => 'Annapolis', 'region' => 'Northeast', 'transit_days' => '3-4'],
        'massachusetts' => ['name' => 'Massachusetts', 'code' => 'MA', 'capital' => 'Boston', 'region' => 'Northeast', 'transit_days' => '3-4'],
        'michigan' => ['name' => 'Michigan', 'code' => 'MI', 'capital' => 'Lansing', 'region' => 'Midwest', 'transit_days' => '3-4'],
        'minnesota' => ['name' => 'Minnesota', 'code' => 'MN', 'capital' => 'St. Paul', 'region' => 'Midwest', 'transit_days' => '3-5'],
        'mississippi' => ['name' => 'Mississippi', 'code' => 'MS', 'capital' => 'Jackson', 'region' => 'South', 'transit_days' => '3-5'],
        'missouri' => ['name' => 'Missouri', 'code' => 'MO', 'capital' => 'Jefferson City', 'region' => 'Midwest', 'transit_days' => '3-4'],
        'montana' => ['name' => 'Montana', 'code' => 'MT', 'capital' => 'Helena', 'region' => 'West', 'transit_days' => '4-6'],
        'nebraska' => ['name' => 'Nebraska', 'code' => 'NE', 'capital' => 'Lincoln', 'region' => 'Midwest', 'transit_days' => '3-5'],
        'nevada' => ['name' => 'Nevada', 'code' => 'NV', 'capital' => 'Carson City', 'region' => 'West', 'transit_days' => '3-4'],
        'new-hampshire' => ['name' => 'New Hampshire', 'code' => 'NH', 'capital' => 'Concord', 'region' => 'Northeast', 'transit_days' => '3-5'],
        'new-jersey' => ['name' => 'New Jersey', 'code' => 'NJ', 'capital' => 'Trenton', 'region' => 'Northeast', 'transit_days' => '3-4'],
        'new-mexico' => ['name' => 'New Mexico', 'code' => 'NM', 'capital' => 'Santa Fe', 'region' => 'West', 'transit_days' => '3-5'],
        'new-york' => ['name' => 'New York', 'code' => 'NY', 'capital' => 'Albany', 'region' => 'Northeast', 'transit_days' => '3-4'],
        'north-carolina' => ['name' => 'North Carolina', 'code' => 'NC', 'capital' => 'Raleigh', 'region' => 'South', 'transit_days' => '3-4'],
        'north-dakota' => ['name' => 'North Dakota', 'code' => 'ND', 'capital' => 'Bismarck', 'region' => 'Midwest', 'transit_days' => '4-6'],
        'ohio' => ['name' => 'Ohio', 'code' => 'OH', 'capital' => 'Columbus', 'region' => 'Midwest', 'transit_days' => '3-4'],
        'oklahoma' => ['name' => 'Oklahoma', 'code' => 'OK', 'capital' => 'Oklahoma City', 'region' => 'South', 'transit_days' => '3-5'],
        'oregon' => ['name' => 'Oregon', 'code' => 'OR', 'capital' => 'Salem', 'region' => 'West', 'transit_days' => '3-5'],
        'pennsylvania' => ['name' => 'Pennsylvania', 'code' => 'PA', 'capital' => 'Harrisburg', 'region' => 'Northeast', 'transit_days' => '3-4'],
        'rhode-island' => ['name' => 'Rhode Island', 'code' => 'RI', 'capital' => 'Providence', 'region' => 'Northeast', 'transit_days' => '3-5'],
        'south-carolina' => ['name' => 'South Carolina', 'code' => 'SC', 'capital' => 'Columbia', 'region' => 'South', 'transit_days' => '3-4'],
        'south-dakota' => ['name' => 'South Dakota', 'code' => 'SD', 'capital' => 'Pierre', 'region' => 'Midwest', 'transit_days' => '4-6'],
        'tennessee' => ['name' => 'Tennessee', 'code' => 'TN', 'capital' => 'Nashville', 'region' => 'South', 'transit_days' => '3-4'],
        'texas' => ['name' => 'Texas', 'code' => 'TX', 'capital' => 'Austin', 'region' => 'South', 'transit_days' => '3-4'],
        'utah' => ['name' => 'Utah', 'code' => 'UT', 'capital' => 'Salt Lake City', 'region' => 'West', 'transit_days' => '3-5'],
        'vermont' => ['name' => 'Vermont', 'code' => 'VT', 'capital' => 'Montpelier', 'region' => 'Northeast', 'transit_days' => '3-5'],
        'virginia' => ['name' => 'Virginia', 'code' => 'VA', 'capital' => 'Richmond', 'region' => 'South', 'transit_days' => '3-4'],
        'washington' => ['name' => 'Washington', 'code' => 'WA', 'capital' => 'Olympia', 'region' => 'West', 'transit_days' => '3-5'],
        'west-virginia' => ['name' => 'West Virginia', 'code' => 'WV', 'capital' => 'Charleston', 'region' => 'South', 'transit_days' => '3-5'],
        'wisconsin' => ['name' => 'Wisconsin', 'code' => 'WI', 'capital' => 'Madison', 'region' => 'Midwest', 'transit_days' => '3-4'],
        'wyoming' => ['name' => 'Wyoming', 'code' => 'WY', 'capital' => 'Cheyenne', 'region' => 'West', 'transit_days' => '4-6'],
    ];

    /**
     * Curated Top 60+ Major US Metros & Commercial Cities across the 50 States.
     */
    protected static array $cities = [
        // New York
        ['name' => 'New York City', 'slug' => 'new-york-city', 'state_slug' => 'new-york', 'state_code' => 'NY', 'focus' => 'Smart Apartment Gadgets & Urban Mobility Tech', 'transit_days' => '2-4'],
        ['name' => 'Buffalo', 'slug' => 'buffalo', 'state_slug' => 'new-york', 'state_code' => 'NY', 'focus' => 'Home Office Accessories & Winter Tech Gear', 'transit_days' => '3-4'],
        ['name' => 'Rochester', 'slug' => 'rochester', 'state_slug' => 'new-york', 'state_code' => 'NY', 'focus' => 'Desk Tech & Smart Living Devices', 'transit_days' => '3-4'],
        
        // California
        ['name' => 'Los Angeles', 'slug' => 'los-angeles', 'state_slug' => 'california', 'state_code' => 'CA', 'focus' => 'Commuter Car Gadgets & Content Creator Tech', 'transit_days' => '2-4'],
        ['name' => 'San Francisco', 'slug' => 'san-francisco', 'state_slug' => 'california', 'state_code' => 'CA', 'focus' => 'Smart Home Automation & Productivity Tech', 'transit_days' => '2-4'],
        ['name' => 'San Diego', 'slug' => 'san-diego', 'state_slug' => 'california', 'state_code' => 'CA', 'focus' => 'Outdoor Tech Accessories & Audio Gadgets', 'transit_days' => '2-4'],
        ['name' => 'San Jose', 'slug' => 'san-jose', 'state_slug' => 'california', 'state_code' => 'CA', 'focus' => 'Silicon Valley Tech Accessories & Gaming Gear', 'transit_days' => '2-4'],
        ['name' => 'Sacramento', 'slug' => 'sacramento', 'state_slug' => 'california', 'state_code' => 'CA', 'focus' => 'Smart Living & Vehicle Tech Accessories', 'transit_days' => '3-4'],
        ['name' => 'Fresno', 'slug' => 'fresno', 'state_slug' => 'california', 'state_code' => 'CA', 'focus' => 'Automotive & Practical Smart Home Tech', 'transit_days' => '3-4'],

        // Texas
        ['name' => 'Houston', 'slug' => 'houston', 'state_slug' => 'texas', 'state_code' => 'TX', 'focus' => 'Vehicle Gadgets & Smart Home Cooling Tech', 'transit_days' => '2-4'],
        ['name' => 'Dallas', 'slug' => 'dallas', 'state_slug' => 'texas', 'state_code' => 'TX', 'focus' => 'Highway Commuter Tech & Workstation Accessories', 'transit_days' => '2-4'],
        ['name' => 'Austin', 'slug' => 'austin', 'state_slug' => 'texas', 'state_code' => 'TX', 'focus' => 'Creative Tech Gadgets & Smart Office Gear', 'transit_days' => '2-4'],
        ['name' => 'San Antonio', 'slug' => 'san-antonio', 'state_slug' => 'texas', 'state_code' => 'TX', 'focus' => 'Family Tech Devices & Home Security Gadgets', 'transit_days' => '3-4'],
        ['name' => 'Fort Worth', 'slug' => 'fort-worth', 'state_slug' => 'texas', 'state_code' => 'TX', 'focus' => 'Truck & Vehicle Electronics Accessories', 'transit_days' => '3-4'],
        ['name' => 'El Paso', 'slug' => 'el-paso', 'state_slug' => 'texas', 'state_code' => 'TX', 'focus' => 'Mobile Charging & Solar Tech Accessories', 'transit_days' => '3-5'],

        // Illinois
        ['name' => 'Chicago', 'slug' => 'chicago', 'state_slug' => 'illinois', 'state_code' => 'IL', 'focus' => 'Urban Apartment Gadgets & Wireless Audio', 'transit_days' => '2-4'],
        ['name' => 'Naperville', 'slug' => 'naperville', 'state_slug' => 'illinois', 'state_code' => 'IL', 'focus' => 'Smart Home Comfort & Family Tech', 'transit_days' => '3-4'],

        // Florida
        ['name' => 'Miami', 'slug' => 'miami', 'state_slug' => 'florida', 'state_code' => 'FL', 'focus' => 'Waterproof Audio & Travel Lifestyle Tech', 'transit_days' => '2-4'],
        ['name' => 'Orlando', 'slug' => 'orlando', 'state_slug' => 'florida', 'state_code' => 'FL', 'focus' => 'Travel Power Gadgets & Smart Gaming Gear', 'transit_days' => '3-4'],
        ['name' => 'Tampa', 'slug' => 'tampa', 'state_slug' => 'florida', 'state_code' => 'FL', 'focus' => 'Automotive Tech & Outdoor Electronics', 'transit_days' => '3-4'],
        ['name' => 'Jacksonville', 'slug' => 'jacksonville', 'state_slug' => 'florida', 'state_code' => 'FL', 'focus' => 'Smart Home Gadgets & Car Electronics', 'transit_days' => '3-4'],

        // Washington
        ['name' => 'Seattle', 'slug' => 'seattle', 'state_slug' => 'washington', 'state_code' => 'WA', 'focus' => 'Developer Workstation & Smart Living Tech', 'transit_days' => '3-4'],
        ['name' => 'Spokane', 'slug' => 'spokane', 'state_slug' => 'washington', 'state_code' => 'WA', 'focus' => 'Outdoor Gadgets & Practical Tech Tools', 'transit_days' => '3-5'],

        // Massachusetts
        ['name' => 'Boston', 'slug' => 'boston', 'state_slug' => 'massachusetts', 'state_code' => 'MA', 'focus' => 'Student Study Tech & Ergonomic Desk Gadgets', 'transit_days' => '2-4'],
        ['name' => 'Cambridge', 'slug' => 'cambridge', 'state_slug' => 'massachusetts', 'state_code' => 'MA', 'focus' => 'Innovation Tech & Compact Electronic Accessories', 'transit_days' => '2-4'],

        // Georgia
        ['name' => 'Atlanta', 'slug' => 'atlanta', 'state_slug' => 'georgia', 'state_code' => 'GA', 'focus' => 'Commuter Accessories & Audio Entertainment Tech', 'transit_days' => '2-4'],
        ['name' => 'Savannah', 'slug' => 'savannah', 'state_slug' => 'georgia', 'state_code' => 'GA', 'focus' => 'Home Comfort Devices & Mobile Accessories', 'transit_days' => '3-4'],

        // Arizona
        ['name' => 'Phoenix', 'slug' => 'phoenix', 'state_slug' => 'arizona', 'state_code' => 'AZ', 'focus' => 'Vehicle Sun Tech & Smart Home Climate Gadgets', 'transit_days' => '2-4'],
        ['name' => 'Tucson', 'slug' => 'tucson', 'state_slug' => 'arizona', 'state_code' => 'AZ', 'focus' => 'Outdoor Electronics & Smart Living Devices', 'transit_days' => '3-4'],

        // Colorado
        ['name' => 'Denver', 'slug' => 'denver', 'state_slug' => 'colorado', 'state_code' => 'CO', 'focus' => 'Outdoor Action Tech & Adventure Accessories', 'transit_days' => '2-4'],
        ['name' => 'Colorado Springs', 'slug' => 'colorado-springs', 'state_slug' => 'colorado', 'state_code' => 'CO', 'focus' => 'Travel Tech & Road Trip Electronics', 'transit_days' => '3-4'],

        // Pennsylvania
        ['name' => 'Philadelphia', 'slug' => 'philadelphia', 'state_slug' => 'pennsylvania', 'state_code' => 'PA', 'focus' => 'Smart Urban Living & Home Entertainment Tech', 'transit_days' => '2-4'],
        ['name' => 'Pittsburgh', 'slug' => 'pittsburgh', 'state_slug' => 'pennsylvania', 'state_code' => 'PA', 'focus' => 'Robotics & Desk Productivity Accessories', 'transit_days' => '3-4'],

        // Nevada
        ['name' => 'Las Vegas', 'slug' => 'las-vegas', 'state_slug' => 'nevada', 'state_code' => 'NV', 'focus' => 'Portable Entertainment & Travel Electronics', 'transit_days' => '2-4'],
        ['name' => 'Reno', 'slug' => 'reno', 'state_slug' => 'nevada', 'state_code' => 'NV', 'focus' => 'Smart Home & Vehicle Accessories', 'transit_days' => '3-4'],

        // North Carolina
        ['name' => 'Charlotte', 'slug' => 'charlotte', 'state_slug' => 'north-carolina', 'state_code' => 'NC', 'focus' => 'Executive Tech & Smart Office Gear', 'transit_days' => '2-4'],
        ['name' => 'Raleigh', 'slug' => 'raleigh', 'state_slug' => 'north-carolina', 'state_code' => 'NC', 'focus' => 'Research Triangle Tech & Innovation Devices', 'transit_days' => '2-4'],

        // Ohio
        ['name' => 'Columbus', 'slug' => 'columbus', 'state_slug' => 'ohio', 'state_code' => 'OH', 'focus' => 'Smart Living & Gaming Accessories', 'transit_days' => '2-4'],
        ['name' => 'Cleveland', 'slug' => 'cleveland', 'state_slug' => 'ohio', 'state_code' => 'OH', 'focus' => 'Automotive & Practical Home Electronics', 'transit_days' => '3-4'],
        ['name' => 'Cincinnati', 'slug' => 'cincinnati', 'state_slug' => 'ohio', 'state_code' => 'OH', 'focus' => 'Home Office Setup & Mobile Gadgets', 'transit_days' => '3-4'],

        // Michigan
        ['name' => 'Detroit', 'slug' => 'detroit', 'state_slug' => 'michigan', 'state_code' => 'MI', 'focus' => 'Automotive Tech & Smart Mobility Accessories', 'transit_days' => '3-4'],
        ['name' => 'Grand Rapids', 'slug' => 'grand-rapids', 'state_slug' => 'michigan', 'state_code' => 'MI', 'focus' => 'Ergonomic Workspace & Home Gadgets', 'transit_days' => '3-4'],

        // Tennessee
        ['name' => 'Nashville', 'slug' => 'nashville', 'state_slug' => 'tennessee', 'state_code' => 'TN', 'focus' => 'Audio Production Tech & Wireless Accessories', 'transit_days' => '2-4'],
        ['name' => 'Memphis', 'slug' => 'memphis', 'state_slug' => 'tennessee', 'state_code' => 'TN', 'focus' => 'Logistics Tech & Personal Audio Gear', 'transit_days' => '3-4'],

        // Indiana
        ['name' => 'Indianapolis', 'slug' => 'indianapolis', 'state_slug' => 'indiana', 'state_code' => 'IN', 'focus' => 'Racing Automotive Tech & Smart Devices', 'transit_days' => '2-4'],

        // Minnesota
        ['name' => 'Minneapolis', 'slug' => 'minneapolis', 'state_slug' => 'minnesota', 'state_code' => 'MN', 'focus' => 'Winter Smart Home Comfort & Productivity Tech', 'transit_days' => '3-4'],

        // Oregon
        ['name' => 'Portland', 'slug' => 'portland', 'state_slug' => 'oregon', 'state_code' => 'OR', 'focus' => 'Eco-Friendly Tech & Outdoor Accessories', 'transit_days' => '3-4'],

        // Missouri
        ['name' => 'Kansas City', 'slug' => 'kansas-city', 'state_slug' => 'missouri', 'state_code' => 'MO', 'focus' => 'Smart Living & Mobile Accessories', 'transit_days' => '3-4'],
        ['name' => 'St. Louis', 'slug' => 'st-louis', 'state_slug' => 'missouri', 'state_code' => 'MO', 'focus' => 'Desk Setup Gear & Car Tech Gadgets', 'transit_days' => '3-4'],

        // Maryland & DC
        ['name' => 'Washington DC', 'slug' => 'washington-dc', 'state_slug' => 'maryland', 'state_code' => 'MD', 'focus' => 'Secure Tech Accessories & Mobile Gadgets', 'transit_days' => '2-4'],
        ['name' => 'Baltimore', 'slug' => 'baltimore', 'state_slug' => 'maryland', 'state_code' => 'MD', 'focus' => 'Smart Home Essentials & Wireless Audio', 'transit_days' => '2-4'],

        // Louisiana
        ['name' => 'New Orleans', 'slug' => 'new-orleans', 'state_slug' => 'louisiana', 'state_code' => 'LA', 'focus' => 'Portable Audio & Entertainment Electronics', 'transit_days' => '3-4'],

        // Utah
        ['name' => 'Salt Lake City', 'slug' => 'salt-lake-city', 'state_slug' => 'utah', 'state_code' => 'UT', 'focus' => 'Silicon Slopes Tech & Outdoor Gear', 'transit_days' => '3-4'],

        // Oklahoma
        ['name' => 'Oklahoma City', 'slug' => 'oklahoma-city', 'state_slug' => 'oklahoma', 'state_code' => 'OK', 'focus' => 'Vehicle Tech & Smart Home Devices', 'transit_days' => '3-4'],

        // Wisconsin
        ['name' => 'Milwaukee', 'slug' => 'milwaukee', 'state_slug' => 'wisconsin', 'state_code' => 'WI', 'focus' => 'Home Workshop Gadgets & Wireless Tech', 'transit_days' => '3-4'],

        // Virginia
        ['name' => 'Virginia Beach', 'slug' => 'virginia-beach', 'state_slug' => 'virginia', 'state_code' => 'VA', 'focus' => 'Coastal Tech Gear & Waterproof Audio', 'transit_days' => '3-4'],
        ['name' => 'Richmond', 'slug' => 'richmond', 'state_slug' => 'virginia', 'state_code' => 'VA', 'focus' => 'Smart Living & Workplace Tech', 'transit_days' => '3-4'],

        // Kentucky
        ['name' => 'Louisville', 'slug' => 'louisville', 'state_slug' => 'kentucky', 'state_code' => 'KY', 'focus' => 'Home Entertainment & Smart Devices', 'transit_days' => '3-4'],

        // Hawaii & Alaska
        ['name' => 'Honolulu', 'slug' => 'honolulu', 'state_slug' => 'hawaii', 'state_code' => 'HI', 'focus' => 'Island Travel Tech & Waterproof Electronics', 'transit_days' => '5-7'],
        ['name' => 'Anchorage', 'slug' => 'anchorage', 'state_slug' => 'alaska', 'state_code' => 'AK', 'focus' => 'Heavy Duty Cold Weather Electronics', 'transit_days' => '5-7'],
    ];

    public static function getAllStates(): array
    {
        return self::$states;
    }

    public static function getStateBySlug(string $slug): ?array
    {
        $slug = strtolower($slug);
        if (isset(self::$states[$slug])) {
            return array_merge(self::$states[$slug], ['slug' => $slug]);
        }
        return null;
    }

    public static function getAllCities(): array
    {
        return self::$cities;
    }

    public static function getCitiesForState(string $stateSlug): array
    {
        $stateSlug = strtolower($stateSlug);
        return array_values(array_filter(self::$cities, fn($c) => $c['state_slug'] === $stateSlug));
    }

    public static function getCityBySlug(string $stateSlug, string $citySlug): ?array
    {
        $stateSlug = strtolower($stateSlug);
        $citySlug = strtolower($citySlug);
        
        foreach (self::$cities as $city) {
            if ($city['state_slug'] === $stateSlug && $city['slug'] === $citySlug) {
                return $city;
            }
        }
        return null;
    }

    public static function getTopCommercialCities(int $limit = 25): array
    {
        return array_slice(self::$cities, 0, $limit);
    }
}
