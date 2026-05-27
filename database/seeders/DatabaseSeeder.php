<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create vendor user
        $vendor = User::create([
            'name' => 'Warung Pak Mat',
            'email' => 'vendor@campus.edu.my',
            'password' => Hash::make('password'),
            'role' => 'vendor',
        ]);

        // Create customer users
        $alif = User::create([
            'name' => 'Alif Ibrahim',
            'email' => 'alif@campus.edu.my',
            'password' => Hash::make('password'),
            'role' => 'customer',
        ]);

        $sarah = User::create([
            'name' => 'Sarah Tan',
            'email' => 'sarah@campus.edu.my',
            'password' => Hash::make('password'),
            'role' => 'customer',
        ]);

        $johan = User::create([
            'name' => 'Johan Lee',
            'email' => 'johan@campus.edu.my',
            'password' => Hash::make('password'),
            'role' => 'customer',
        ]);

        $nurul = User::create([
            'name' => 'Nurul Aisyah',
            'email' => 'nurul@campus.edu.my',
            'password' => Hash::make('password'),
            'role' => 'customer',
        ]);

        $irfan = User::create([
            'name' => 'Irfan Hakim',
            'email' => 'irfan@campus.edu.my',
            'password' => Hash::make('password'),
            'role' => 'customer',
        ]);

        $iman = User::create([
            'name' => 'Iman Razak',
            'email' => 'iman@campus.edu.my',
            'password' => Hash::make('password'),
            'role' => 'customer',
        ]);

        $adam = User::create([
            'name' => 'Adam Firdaus',
            'email' => 'adam@campus.edu.my',
            'password' => Hash::make('password'),
            'role' => 'customer',
        ]);

        $lina = User::create([
            'name' => 'Lina Mohd',
            'email' => 'lina@campus.edu.my',
            'password' => Hash::make('password'),
            'role' => 'customer',
        ]);

        // Create sample orders (matching the original hardcoded data)
        $orders = [
            [
                'order_code' => 'BOM-2401',
                'customer_id' => $alif->id,
                'vendor_name' => 'Warung Pak Mat',
                'delivery_location' => 'Block A - Main Lobby',
                'delivery_date' => now()->toDateString(),
                'time_slot' => 'Lunch (12:00 - 14:00)',
                'items' => [['name' => 'Nasi Lemak Special', 'qty' => 2, 'price' => 6.00]],
                'subtotal' => 12.00,
                'delivery_fee' => 2.00,
                'total' => 14.00,
                'notes' => null,
                'status' => 'pending',
            ],
            [
                'order_code' => 'BOM-2402',
                'customer_id' => $sarah->id,
                'vendor_name' => 'Warung Pak Mat',
                'delivery_location' => 'Block B - Cafeteria',
                'delivery_date' => now()->toDateString(),
                'time_slot' => 'Lunch (12:00 - 14:00)',
                'items' => [['name' => 'Mee Goreng Mamak', 'qty' => 1, 'price' => 6.50]],
                'subtotal' => 6.50,
                'delivery_fee' => 2.00,
                'total' => 8.50,
                'notes' => null,
                'status' => 'processing',
            ],
            [
                'order_code' => 'BOM-2403',
                'customer_id' => $johan->id,
                'vendor_name' => 'Warung Pak Mat',
                'delivery_location' => 'Library Entrance',
                'delivery_date' => now()->toDateString(),
                'time_slot' => 'Morning (8:00 - 10:00)',
                'items' => [
                    ['name' => 'Roti Canai', 'qty' => 5, 'price' => 2.00],
                    ['name' => 'Teh Tarik', 'qty' => 5, 'price' => 2.60],
                ],
                'subtotal' => 23.00,
                'delivery_fee' => 2.00,
                'total' => 25.00,
                'notes' => null,
                'status' => 'processing',
            ],
            [
                'order_code' => 'BOM-2404',
                'customer_id' => $nurul->id,
                'vendor_name' => 'Warung Pak Mat',
                'delivery_location' => 'Dewan Kuliah Utama',
                'delivery_date' => now()->toDateString(),
                'time_slot' => 'Morning (8:00 - 10:00)',
                'items' => [['name' => 'Nasi Ayam Penyet', 'qty' => 1, 'price' => 8.00]],
                'subtotal' => 8.00,
                'delivery_fee' => 2.00,
                'total' => 10.00,
                'notes' => null,
                'status' => 'completed',
            ],
            [
                'order_code' => 'BOM-2405',
                'customer_id' => $irfan->id,
                'vendor_name' => 'Burger Station KL',
                'delivery_location' => 'Block B - Cafeteria',
                'delivery_date' => now()->toDateString(),
                'time_slot' => 'Evening (17:00 - 19:00)',
                'items' => [['name' => 'Burger Bakar', 'qty' => 3, 'price' => 7.33]],
                'subtotal' => 22.00,
                'delivery_fee' => 2.00,
                'total' => 24.00,
                'notes' => null,
                'status' => 'pending',
            ],
            [
                'order_code' => 'BOM-2406',
                'customer_id' => $iman->id,
                'vendor_name' => 'Warung Pak Mat',
                'delivery_location' => 'Block A - Main Lobby',
                'delivery_date' => now()->toDateString(),
                'time_slot' => 'Morning (8:00 - 10:00)',
                'items' => [
                    ['name' => 'Nasi Lemak', 'qty' => 1, 'price' => 5.50],
                    ['name' => 'Kopi O', 'qty' => 1, 'price' => 2.00],
                ],
                'subtotal' => 7.50,
                'delivery_fee' => 2.00,
                'total' => 9.50,
                'notes' => null,
                'status' => 'completed',
            ],
            [
                'order_code' => 'BOM-2407',
                'customer_id' => $adam->id,
                'vendor_name' => 'Warung Pak Mat',
                'delivery_location' => 'Dewan Kuliah Utama',
                'delivery_date' => now()->toDateString(),
                'time_slot' => 'Lunch (12:00 - 14:00)',
                'items' => [['name' => 'Ayam Goreng Set', 'qty' => 2, 'price' => 8.00]],
                'subtotal' => 16.00,
                'delivery_fee' => 2.00,
                'total' => 18.00,
                'notes' => null,
                'status' => 'pending',
            ],
            [
                'order_code' => 'BOM-2408',
                'customer_id' => $lina->id,
                'vendor_name' => 'Warung Pak Mat',
                'delivery_location' => 'Library Entrance',
                'delivery_date' => now()->toDateString(),
                'time_slot' => 'Morning (8:00 - 10:00)',
                'items' => [['name' => 'Laksa Penang', 'qty' => 1, 'price' => 7.00]],
                'subtotal' => 7.00,
                'delivery_fee' => 2.00,
                'total' => 9.00,
                'notes' => null,
                'status' => 'completed',
            ],
        ];

        foreach ($orders as $orderData) {
            Order::create($orderData);
        }
    }
}
