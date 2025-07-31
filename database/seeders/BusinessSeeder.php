<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class BusinessSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $businesses = [
            [
                'business_name' => 'Tech Solutions Inc.',
                'contact_email' => 'contact@techsolutions.com',
                'username' => 'techsolutions',
                'password' => 'password123',
                'business_description' => 'Leading technology solutions provider specializing in web development and digital transformation.',
                'phone' => '+1-555-0123',
                'address' => '123 Tech Street',
                'city' => 'San Francisco',
                'state' => 'CA',
                'postal_code' => '94105',
                'country' => 'USA',
                'is_active' => true,
                'expires_at' => now()->addYear(),
            ],
            [
                'business_name' => 'Global Retail Corp.',
                'contact_email' => 'info@globalretail.com',
                'username' => 'globalretail',
                'password' => 'password123',
                'business_description' => 'International retail chain with stores across multiple countries.',
                'phone' => '+1-555-0456',
                'address' => '456 Retail Avenue',
                'city' => 'New York',
                'state' => 'NY',
                'postal_code' => '10001',
                'country' => 'USA',
                'is_active' => true,
                'expires_at' => now()->addMonths(6),
            ],
            [
                'business_name' => 'Creative Design Studio',
                'contact_email' => 'hello@creativedesign.com',
                'username' => 'creativedesign',
                'password' => 'password123',
                'business_description' => 'Creative design agency offering branding, web design, and marketing services.',
                'phone' => '+1-555-0789',
                'address' => '789 Design Lane',
                'city' => 'Los Angeles',
                'state' => 'CA',
                'postal_code' => '90210',
                'country' => 'USA',
                'is_active' => false,
                'expires_at' => now()->addDays(30),
            ],
            [
                'business_name' => 'Green Energy Solutions',
                'contact_email' => 'contact@greenenergy.com',
                'username' => 'greenenergy',
                'password' => 'password123',
                'business_description' => 'Sustainable energy solutions for businesses and homes.',
                'phone' => '+1-555-0321',
                'address' => '321 Green Street',
                'city' => 'Seattle',
                'state' => 'WA',
                'postal_code' => '98101',
                'country' => 'USA',
                'is_active' => true,
                'expires_at' => null, // No expiration
            ],
            [
                'business_name' => 'Food & Beverage Co.',
                'contact_email' => 'info@foodbeverage.com',
                'username' => 'foodbeverage',
                'password' => 'password123',
                'business_description' => 'Premium food and beverage distribution company.',
                'phone' => '+1-555-0654',
                'address' => '654 Food Court',
                'city' => 'Chicago',
                'state' => 'IL',
                'postal_code' => '60601',
                'country' => 'USA',
                'is_active' => true,
                'expires_at' => now()->subDays(5), // Expired
            ],
        ];

        foreach ($businesses as $businessData) {
            User::create([
                'name' => $businessData['business_name'],
                'business_name' => $businessData['business_name'],
                'email' => $businessData['contact_email'],
                'contact_email' => $businessData['contact_email'],
                'username' => $businessData['username'],
                'password' => Hash::make($businessData['password']),
                'role' => 'business',
                'business_description' => $businessData['business_description'],
                'phone' => $businessData['phone'],
                'address' => $businessData['address'],
                'city' => $businessData['city'],
                'state' => $businessData['state'],
                'postal_code' => $businessData['postal_code'],
                'country' => $businessData['country'],
                'is_active' => $businessData['is_active'],
                'expires_at' => $businessData['expires_at'],
            ]);
        }
    }
}
