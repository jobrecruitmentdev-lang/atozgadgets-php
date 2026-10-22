<?php

namespace App\Services\Seo;

class IndiaGeoDataService
{
    /**
     * All 28 States & Key Union Territories of India with regional hubs and delivery timelines.
     */
    protected static array $states = [
        'andhra-pradesh' => ['name' => 'Andhra Pradesh', 'code' => 'AP', 'capital' => 'Amaravati', 'region' => 'South', 'transit_days' => '5-8'],
        'assam' => ['name' => 'Assam', 'code' => 'AS', 'capital' => 'Dispur', 'region' => 'Northeast', 'transit_days' => '6-9'],
        'bihar' => ['name' => 'Bihar', 'code' => 'BR', 'capital' => 'Patna', 'region' => 'East', 'transit_days' => '6-8'],
        'chandigarh' => ['name' => 'Chandigarh', 'code' => 'CH', 'capital' => 'Chandigarh', 'region' => 'North', 'transit_days' => '5-7'],
        'chhattisgarh' => ['name' => 'Chhattisgarh', 'code' => 'CG', 'capital' => 'Raipur', 'region' => 'Central', 'transit_days' => '6-8'],
        'delhi' => ['name' => 'Delhi NCR', 'code' => 'DL', 'capital' => 'New Delhi', 'region' => 'North', 'transit_days' => '4-7'],
        'goa' => ['name' => 'Goa', 'code' => 'GA', 'capital' => 'Panaji', 'region' => 'West', 'transit_days' => '5-8'],
        'gujarat' => ['name' => 'Gujarat', 'code' => 'GJ', 'capital' => 'Gandhinagar', 'region' => 'West', 'transit_days' => '5-7'],
        'haryana' => ['name' => 'Haryana', 'code' => 'HR', 'capital' => 'Chandigarh', 'region' => 'North', 'transit_days' => '4-7'],
        'himachal-pradesh' => ['name' => 'Himachal Pradesh', 'code' => 'HP', 'capital' => 'Shimla', 'region' => 'North', 'transit_days' => '6-9'],
        'jammu-and-kashmir' => ['name' => 'Jammu and Kashmir', 'code' => 'JK', 'capital' => 'Srinagar', 'region' => 'North', 'transit_days' => '7-10'],
        'jharkhand' => ['name' => 'Jharkhand', 'code' => 'JH', 'capital' => 'Ranchi', 'region' => 'East', 'transit_days' => '6-8'],
        'karnataka' => ['name' => 'Karnataka', 'code' => 'KA', 'capital' => 'Bengaluru', 'region' => 'South', 'transit_days' => '4-7'],
        'kerala' => ['name' => 'Kerala', 'code' => 'KL', 'capital' => 'Thiruvananthapuram', 'region' => 'South', 'transit_days' => '5-8'],
        'madhya-pradesh' => ['name' => 'Madhya Pradesh', 'code' => 'MP', 'capital' => 'Bhopal', 'region' => 'Central', 'transit_days' => '5-8'],
        'maharashtra' => ['name' => 'Maharashtra', 'code' => 'MH', 'capital' => 'Mumbai', 'region' => 'West', 'transit_days' => '4-7'],
        'odisha' => ['name' => 'Odisha', 'code' => 'OD', 'capital' => 'Bhubaneswar', 'region' => 'East', 'transit_days' => '6-8'],
        'punjab' => ['name' => 'Punjab', 'code' => 'PB', 'capital' => 'Chandigarh', 'region' => 'North', 'transit_days' => '5-7'],
        'rajasthan' => ['name' => 'Rajasthan', 'code' => 'RJ', 'capital' => 'Jaipur', 'region' => 'North', 'transit_days' => '5-8'],
        'tamil-nadu' => ['name' => 'Tamil Nadu', 'code' => 'TN', 'capital' => 'Chennai', 'region' => 'South', 'transit_days' => '4-7'],
        'telangana' => ['name' => 'Telangana', 'code' => 'TS', 'capital' => 'Hyderabad', 'region' => 'South', 'transit_days' => '4-7'],
        'uttar-pradesh' => ['name' => 'Uttar Pradesh', 'code' => 'UP', 'capital' => 'Lucknow', 'region' => 'North', 'transit_days' => '5-8'],
        'uttarakhand' => ['name' => 'Uttarakhand', 'code' => 'UK', 'capital' => 'Dehradun', 'region' => 'North', 'transit_days' => '6-8'],
        'west-bengal' => ['name' => 'West Bengal', 'code' => 'WB', 'capital' => 'Kolkata', 'region' => 'East', 'transit_days' => '5-8'],
    ];

    /**
     * Top 50+ Key Commercial & Tech Metros in India.
     */
    protected static array $cities = [
        // Karnataka
        ['name' => 'Bengaluru', 'slug' => 'bengaluru', 'state_slug' => 'karnataka', 'state_code' => 'KA', 'focus' => 'Tech Enthusiast Gadgets & Smart Home Electronics', 'transit_days' => '4-7'],
        ['name' => 'Mysuru', 'slug' => 'mysuru', 'state_slug' => 'karnataka', 'state_code' => 'KA', 'focus' => 'Smart Living & Lifestyle Tech Accessories', 'transit_days' => '5-8'],
        ['name' => 'Mangaluru', 'slug' => 'mangaluru', 'state_slug' => 'karnataka', 'state_code' => 'KA', 'focus' => 'Consumer Electronics & Audio Accessories', 'transit_days' => '5-8'],

        // Maharashtra
        ['name' => 'Mumbai', 'slug' => 'mumbai', 'state_slug' => 'maharashtra', 'state_code' => 'MH', 'focus' => 'Luxury Timepieces, Lifestyle Tech & Viral Gadgets', 'transit_days' => '4-7'],
        ['name' => 'Pune', 'slug' => 'pune', 'state_slug' => 'maharashtra', 'state_code' => 'MH', 'focus' => 'Developer Desk Gear, Smart Wearables & Audio', 'transit_days' => '4-7'],
        ['name' => 'Nagpur', 'slug' => 'nagpur', 'state_slug' => 'maharashtra', 'state_code' => 'MH', 'focus' => 'Smart Home Gadgets & Automotive Electronics', 'transit_days' => '5-8'],
        ['name' => 'Nashik', 'slug' => 'nashik', 'state_slug' => 'maharashtra', 'state_code' => 'MH', 'focus' => 'Home Kitchen Tools & Holiday Decor', 'transit_days' => '5-8'],
        ['name' => 'Thane', 'slug' => 'thane', 'state_slug' => 'maharashtra', 'state_code' => 'MH', 'focus' => 'Modern Apartment Tech & Kitchen Utilities', 'transit_days' => '4-7'],

        // Delhi NCR
        ['name' => 'New Delhi', 'slug' => 'new-delhi', 'state_slug' => 'delhi', 'state_code' => 'DL', 'focus' => 'Executive Smart Wearables & High-End Tech Gifts', 'transit_days' => '4-7'],
        ['name' => 'Delhi', 'slug' => 'delhi', 'state_slug' => 'delhi', 'state_code' => 'DL', 'focus' => 'Trending Viral Gadgets & Electronic Accessories', 'transit_days' => '4-7'],

        // Haryana (NCR Hubs)
        ['name' => 'Gurugram', 'slug' => 'gurugram', 'state_slug' => 'haryana', 'state_code' => 'HR', 'focus' => 'Smart Desk Setup, Premium Watches & Audio Tech', 'transit_days' => '4-7'],
        ['name' => 'Faridabad', 'slug' => 'faridabad', 'state_slug' => 'haryana', 'state_code' => 'HR', 'focus' => 'Home Gadgets & Everyday Tech Utilities', 'transit_days' => '5-8'],

        // Uttar Pradesh (NCR & Major Hubs)
        ['name' => 'Noida', 'slug' => 'noida', 'state_slug' => 'uttar-pradesh', 'state_code' => 'UP', 'focus' => 'Smart Home Automation & Creative Gadgets', 'transit_days' => '4-7'],
        ['name' => 'Lucknow', 'slug' => 'lucknow', 'state_slug' => 'uttar-pradesh', 'state_code' => 'UP', 'focus' => 'Fashion Timepieces & Lifestyle Electronics', 'transit_days' => '5-8'],
        ['name' => 'Kanpur', 'slug' => 'kanpur', 'state_slug' => 'uttar-pradesh', 'state_code' => 'UP', 'focus' => 'Practical Home Tools & Kitchen Gadgetz', 'transit_days' => '5-8'],
        ['name' => 'Agra', 'slug' => 'agra', 'state_slug' => 'uttar-pradesh', 'state_code' => 'UP', 'focus' => 'Travel Accessories & Holiday Home Decor', 'transit_days' => '5-8'],
        ['name' => 'Varanasi', 'slug' => 'varanasi', 'state_slug' => 'uttar-pradesh', 'state_code' => 'UP', 'focus' => 'Ambient Decorative Lighting & Unique Gifts', 'transit_days' => '6-8'],

        // Telangana
        ['name' => 'Hyderabad', 'slug' => 'hyderabad', 'state_slug' => 'telangana', 'state_code' => 'TS', 'focus' => 'Cyber City Tech Gadgets, Wearables & Audio', 'transit_days' => '4-7'],
        ['name' => 'Warangal', 'slug' => 'warangal', 'state_slug' => 'telangana', 'state_code' => 'TS', 'focus' => 'Smart Home Utilities & Electronics', 'transit_days' => '5-8'],

        // Tamil Nadu
        ['name' => 'Chennai', 'slug' => 'chennai', 'state_slug' => 'tamil-nadu', 'state_code' => 'TN', 'focus' => 'Engineering Tech, Smart Watches & Audio Gear', 'transit_days' => '4-7'],
        ['name' => 'Coimbatore', 'slug' => 'coimbatore', 'state_slug' => 'tamil-nadu', 'state_code' => 'TN', 'focus' => 'Precision Tools, Home Utilities & Gadgets', 'transit_days' => '5-8'],
        ['name' => 'Madurai', 'slug' => 'madurai', 'state_slug' => 'tamil-nadu', 'state_code' => 'TN', 'focus' => 'Everyday Electronics & Family Tech Devices', 'transit_days' => '5-8'],

        // Gujarat
        ['name' => 'Ahmedabad', 'slug' => 'ahmedabad', 'state_slug' => 'gujarat', 'state_code' => 'GJ', 'focus' => 'Commercial Tech Gadgets & Smart Living Utilities', 'transit_days' => '5-7'],
        ['name' => 'Surat', 'slug' => 'surat', 'state_slug' => 'gujarat', 'state_code' => 'GJ', 'focus' => 'Luxury Timepieces & Novelty Tech Accessories', 'transit_days' => '5-7'],
        ['name' => 'Vadodara', 'slug' => 'vadodara', 'state_slug' => 'gujarat', 'state_code' => 'GJ', 'focus' => 'Home Kitchen Tech & Ambient Decor Lighting', 'transit_days' => '5-8'],
        ['name' => 'Rajkot', 'slug' => 'rajkot', 'state_slug' => 'gujarat', 'state_code' => 'GJ', 'focus' => 'Practical Gadgetz & Household Electronics', 'transit_days' => '5-8'],

        // West Bengal
        ['name' => 'Kolkata', 'slug' => 'kolkata', 'state_slug' => 'west-bengal', 'state_code' => 'WB', 'focus' => 'Cultural Decor, Elegant Timepieces & Audio Tech', 'transit_days' => '5-8'],
        ['name' => 'Howrah', 'slug' => 'howrah', 'state_slug' => 'west-bengal', 'state_code' => 'WB', 'focus' => 'Smart Apartment Utilities & Everyday Tech', 'transit_days' => '5-8'],

        // Rajasthan
        ['name' => 'Jaipur', 'slug' => 'jaipur', 'state_slug' => 'rajasthan', 'state_code' => 'RJ', 'focus' => 'Aesthetic Ambient Decor, Watches & Smart Tech', 'transit_days' => '5-8'],
        ['name' => 'Jodhpur', 'slug' => 'jodhpur', 'state_slug' => 'rajasthan', 'state_code' => 'RJ', 'focus' => 'Home Decor Accents & Novelty Gifts', 'transit_days' => '5-8'],
        ['name' => 'Udaipur', 'slug' => 'udaipur', 'state_slug' => 'rajasthan', 'state_code' => 'RJ', 'focus' => 'Luxury Lifestyle Accessories & Holiday Decor', 'transit_days' => '5-8'],

        // Punjab & Chandigarh
        ['name' => 'Chandigarh', 'slug' => 'chandigarh', 'state_slug' => 'chandigarh', 'state_code' => 'CH', 'focus' => 'Modern Urban Gadgets & Smart Home Utilities', 'transit_days' => '4-7'],
        ['name' => 'Ludhiana', 'slug' => 'ludhiana', 'state_slug' => 'punjab', 'state_code' => 'PB', 'focus' => 'Premium Timepieces & Smart Automotive Gear', 'transit_days' => '5-8'],
        ['name' => 'Amritsar', 'slug' => 'amritsar', 'state_slug' => 'punjab', 'state_code' => 'PB', 'focus' => 'Travel Accessories & Household Gadgetz', 'transit_days' => '5-8'],

        // Kerala
        ['name' => 'Kochi', 'slug' => 'kochi', 'state_slug' => 'kerala', 'state_code' => 'KL', 'focus' => 'Coastal Travel Gear, Audio Tech & Home Gadgets', 'transit_days' => '5-8'],
        ['name' => 'Thiruvananthapuram', 'slug' => 'thiruvananthapuram', 'state_slug' => 'kerala', 'state_code' => 'KL', 'focus' => 'Tech Accessories, Smart Wearables & Gifts', 'transit_days' => '5-8'],
        ['name' => 'Kozhikode', 'slug' => 'kozhikode', 'state_slug' => 'kerala', 'state_code' => 'KL', 'focus' => 'Smart Living Gadgets & Culinary Tools', 'transit_days' => '5-8'],

        // Madhya Pradesh
        ['name' => 'Indore', 'slug' => 'indore', 'state_slug' => 'madhya-pradesh', 'state_code' => 'MP', 'focus' => 'Smart City Tech, Kitchen Gadgets & Watches', 'transit_days' => '5-8'],
        ['name' => 'Bhopal', 'slug' => 'bhopal', 'state_slug' => 'madhya-pradesh', 'state_code' => 'MP', 'focus' => 'Home Office Tech & Practical Electronics', 'transit_days' => '5-8'],

        // Andhra Pradesh
        ['name' => 'Visakhapatnam', 'slug' => 'visakhapatnam', 'state_slug' => 'andhra-pradesh', 'state_code' => 'AP', 'focus' => 'Coastal Tech, Audio Accessories & Smart Living', 'transit_days' => '5-8'],
        ['name' => 'Vijayawada', 'slug' => 'vijayawada', 'state_slug' => 'andhra-pradesh', 'state_code' => 'AP', 'focus' => 'Electronic Utilities & Home Kitchen Tools', 'transit_days' => '5-8'],

        // Odisha
        ['name' => 'Bhubaneswar', 'slug' => 'bhubaneswar', 'state_slug' => 'odisha', 'state_code' => 'OD', 'focus' => 'Smart City Gadgets & Tech Accessories', 'transit_days' => '6-8'],

        // Bihar
        ['name' => 'Patna', 'slug' => 'patna', 'state_slug' => 'bihar', 'state_code' => 'BR', 'focus' => 'Everyday Useful Electronics & Smart Kitchen Tech', 'transit_days' => '6-8'],

        // Assam
        ['name' => 'Guwahati', 'slug' => 'guwahati', 'state_slug' => 'assam', 'state_code' => 'AS', 'focus' => 'Northeast Hub Tech Accessories & Unique Gifts', 'transit_days' => '6-9'],

        // Goa
        ['name' => 'Panaji', 'slug' => 'panaji', 'state_slug' => 'goa', 'state_code' => 'GA', 'focus' => 'Beachside Lifestyle Tech, Audio & Watches', 'transit_days' => '5-8'],
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

    public static function getTopCommercialCities(int $limit = 24): array
    {
        return array_slice(self::$cities, 0, $limit);
    }
}
