<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'John Smith',
                'email' => 'john.smith@example.com',
                'company' => 'Tech Solutions Inc.',
                'role' => 'user',
                'password' => 'password123',
                'is_active' => true,
                'custom_hours' => 48,
            ],
            [
                'name' => 'Sarah Johnson',
                'email' => 'sarah.johnson@example.com',
                'company' => 'Global Retail Corp.',
                'role' => 'user',
                'password' => 'password123',
                'is_active' => true,
                'custom_hours' => 24,
            ],
            [
                'name' => 'Michael Brown',
                'email' => 'michael.brown@example.com',
                'company' => 'Creative Design Studio',
                'role' => 'user',
                'password' => 'password123',
                'is_active' => false,
                'custom_hours' => 72,
            ],
            [
                'name' => 'Emily Davis',
                'email' => 'emily.davis@example.com',
                'company' => 'Green Energy Solutions',
                'role' => 'user',
                'password' => 'password123',
                'is_active' => true,
                'custom_hours' => 168, // 1 week
            ],
            [
                'name' => 'David Wilson',
                'email' => 'david.wilson@example.com',
                'company' => 'Food & Beverage Co.',
                'role' => 'user',
                'password' => 'password123',
                'is_active' => true,
                'custom_hours' => 12,
            ],
            [
                'name' => 'Lisa Anderson',
                'email' => 'lisa.anderson@example.com',
                'company' => 'Tech Solutions Inc.',
                'role' => 'user',
                'password' => 'password123',
                'is_active' => true,
                'custom_hours' => 24,
            ],
            [
                'name' => 'Robert Taylor',
                'email' => 'robert.taylor@example.com',
                'company' => 'Global Retail Corp.',
                'role' => 'user',
                'password' => 'password123',
                'is_active' => true,
                'custom_hours' => 36,
            ],
            [
                'name' => 'Jennifer Martinez',
                'email' => 'jennifer.martinez@example.com',
                'company' => 'Creative Design Studio',
                'role' => 'user',
                'password' => 'password123',
                'is_active' => true,
                'custom_hours' => 24,
            ],
        ];

        foreach ($users as $userData) {
            $user = User::create([
                'name' => $userData['name'],
                'email' => $userData['email'],
                'company' => $userData['company'],
                'role' => $userData['role'],
                'password' => Hash::make($userData['password']),
                'is_active' => $userData['is_active'],
            ]);

            // Generate secure link if custom hours provided
            if ($userData['custom_hours']) {
                $user->generateSecureLink($userData['custom_hours']);
            }
        }
    }
}
