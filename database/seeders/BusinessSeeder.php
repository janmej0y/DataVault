<?php

namespace Database\Seeders;

use App\Models\Business;
use App\Services\BusinessNormalizer;
use App\Services\DuplicateDetectionService;
use Illuminate\Database\Seeder;

class BusinessSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $normalizer = app(BusinessNormalizer::class);

        $rows = [
            [
                'business_name' => 'Honeybee Digital',
                'area' => 'Sector 18',
                'city' => 'Noida',
                'mobile_no' => '9876543210',
                'category' => 'Marketing',
                'sub_category' => 'Digital Agency',
                'address' => 'Tower A, Sector 18',
            ],
            [
                'business_name' => 'Honeybee Digital ',
                'area' => 'Sector-18',
                'city' => 'NOIDA',
                'mobile_no' => '9876543210, 9911223344',
                'category' => 'Marketing',
                'sub_category' => 'SEO',
                'address' => 'Tower A Sector 18',
            ],
            [
                'business_name' => 'Cedar Foods',
                'area' => 'Indiranagar',
                'city' => 'Bangalore',
                'mobile_no' => '9988776655',
                'category' => 'Restaurant',
                'sub_category' => 'Cafe',
                'address' => '100 Feet Road',
            ],
            [
                'business_name' => 'Cedar Foods',
                'area' => 'Indiranagar',
                'city' => 'Bangalore',
                'mobile_no' => '9988776655',
                'category' => 'Restaurant',
                'sub_category' => 'Bakery',
                'address' => '100 Feet Road',
            ],
            [
                'business_name' => 'Metro Auto Care',
                'area' => 'Andheri West',
                'city' => 'Mumbai',
                'mobile_no' => '9820012345',
                'category' => 'Automobile',
                'sub_category' => 'Garage',
                'address' => 'Link Road',
            ],
            [
                'business_name' => 'Lotus Spa',
                'area' => 'Banjara Hills',
                'city' => 'Hyderabad',
                'mobile_no' => '',
                'category' => 'Wellness',
                'sub_category' => 'Spa',
                'address' => 'Road No 12',
            ],
            [
                'business_name' => '',
                'area' => 'Salt Lake',
                'city' => 'Kolkata',
                'mobile_no' => '9000090000',
                'category' => 'Education',
                'sub_category' => 'Coaching',
                'address' => 'Block BD',
            ],
            [
                'business_name' => 'Skyline Interiors',
                'area' => 'MG Road',
                'city' => 'Pune',
                'mobile_no' => '9777766666',
                'category' => '',
                'sub_category' => 'Interior Design',
                'address' => 'Camp',
            ],
            [
                'business_name' => 'Fresh Kart',
                'area' => 'Koramangala',
                'city' => 'Bangalore',
                'mobile_no' => '9012345678',
                'category' => 'Retail',
                'sub_category' => 'Grocery',
                'address' => '5th Block',
            ],
            [
                'business_name' => 'Fresh-Kart',
                'area' => 'Koramangala ',
                'city' => 'Bangalore',
                'mobile_no' => '9012345678',
                'category' => 'Retail',
                'sub_category' => 'Supermarket',
                'address' => '5th Block',
            ],
        ];

        foreach ($rows as $row) {
            Business::query()->create($normalizer->prepareBusinessPayload($row));
        }

        app(DuplicateDetectionService::class)->refreshFlags();
    }
}
