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

    public function run(): void
    {
        // Vendor users
        $pakMat = User::create([
            'name' => 'Warung Pak Mat',
            'email' => 'vendor@campus.edu.my',
            'password' => Hash::make('password'),
            'role' => 'vendor',
        ]);

        $burgerKL = User::create([
            'name' => 'Burger Station KL',
            'email' => 'burger@campus.edu.my',
            'password' => Hash::make('password'),
            'role' => 'vendor',
        ]);

        // Customer users
        $alif = User::create(['name' => 'Alif Ibrahim', 'email' => 'alif@campus.edu.my', 'password' => Hash::make('password'), 'role' => 'customer']);
        $sarah = User::create(['name' => 'Sarah Tan', 'email' => 'sarah@campus.edu.my', 'password' => Hash::make('password'), 'role' => 'customer']);
        $johan = User::create(['name' => 'Johan Lee', 'email' => 'johan@campus.edu.my', 'password' => Hash::make('password'), 'role' => 'customer']);
        $nurul = User::create(['name' => 'Nurul Aisyah', 'email' => 'nurul@campus.edu.my', 'password' => Hash::make('password'), 'role' => 'customer']);
        $irfan = User::create(['name' => 'Irfan Hakim', 'email' => 'irfan@campus.edu.my', 'password' => Hash::make('password'), 'role' => 'customer']);
        $iman = User::create(['name' => 'Iman Razak', 'email' => 'iman@campus.edu.my', 'password' => Hash::make('password'), 'role' => 'customer']);
        $adam = User::create(['name' => 'Adam Firdaus', 'email' => 'adam@campus.edu.my', 'password' => Hash::make('password'), 'role' => 'customer']);
        $lina = User::create(['name' => 'Lina Mohd', 'email' => 'lina@campus.edu.my', 'password' => Hash::make('password'), 'role' => 'customer']);

        // Warung Pak Mat orders (spread across dates for chart demo)
        $pakMatOrders = [
            ['code' => 'BOM-2401', 'cust' => $alif, 'vendor' => 'Warung Pak Mat', 'loc' => 'Block A - Main Lobby', 'slot' => 'Lunch (12:00 - 14:00)',
             'items' => [['name' => 'Nasi Lemak Special', 'qty' => 2, 'price' => 6.00]], 'sub' => 12.00, 'total' => 14.00, 'status' => 'pending', 'days_ago' => 0],

            ['code' => 'BOM-2402', 'cust' => $sarah, 'vendor' => 'Warung Pak Mat', 'loc' => 'Block B - Cafeteria', 'slot' => 'Lunch (12:00 - 14:00)',
             'items' => [['name' => 'Mee Goreng Mamak', 'qty' => 1, 'price' => 6.50]], 'sub' => 6.50, 'total' => 8.50, 'status' => 'processing', 'days_ago' => 0],

            ['code' => 'BOM-2403', 'cust' => $johan, 'vendor' => 'Warung Pak Mat', 'loc' => 'Library Entrance', 'slot' => 'Morning (8:00 - 10:00)',
             'items' => [['name' => 'Roti Canai', 'qty' => 5, 'price' => 2.00], ['name' => 'Teh Tarik', 'qty' => 5, 'price' => 2.60]],
             'sub' => 23.00, 'total' => 25.00, 'status' => 'processing', 'days_ago' => 0],

            ['code' => 'BOM-2404', 'cust' => $nurul, 'vendor' => 'Warung Pak Mat', 'loc' => 'Dewan Kuliah Utama', 'slot' => 'Morning (8:00 - 10:00)',
             'items' => [['name' => 'Nasi Ayam Penyet', 'qty' => 1, 'price' => 8.00]], 'sub' => 8.00, 'total' => 10.00, 'status' => 'completed', 'days_ago' => 1],

            ['code' => 'BOM-2406', 'cust' => $iman, 'vendor' => 'Warung Pak Mat', 'loc' => 'Block A - Main Lobby', 'slot' => 'Morning (8:00 - 10:00)',
             'items' => [['name' => 'Nasi Lemak', 'qty' => 1, 'price' => 5.50], ['name' => 'Kopi O', 'qty' => 1, 'price' => 2.00]],
             'sub' => 7.50, 'total' => 9.50, 'status' => 'completed', 'days_ago' => 1],

            ['code' => 'BOM-2407', 'cust' => $adam, 'vendor' => 'Warung Pak Mat', 'loc' => 'Dewan Kuliah Utama', 'slot' => 'Lunch (12:00 - 14:00)',
             'items' => [['name' => 'Ayam Goreng Set', 'qty' => 2, 'price' => 8.00]], 'sub' => 16.00, 'total' => 18.00, 'status' => 'completed', 'days_ago' => 2],

            ['code' => 'BOM-2408', 'cust' => $lina, 'vendor' => 'Warung Pak Mat', 'loc' => 'Library Entrance', 'slot' => 'Morning (8:00 - 10:00)',
             'items' => [['name' => 'Laksa Penang', 'qty' => 1, 'price' => 7.00]], 'sub' => 7.00, 'total' => 9.00, 'status' => 'completed', 'days_ago' => 3],

            ['code' => 'BOM-2409', 'cust' => $alif, 'vendor' => 'Warung Pak Mat', 'loc' => 'Block A - Main Lobby', 'slot' => 'Evening (17:00 - 19:00)',
             'items' => [['name' => 'Nasi Goreng Kampung', 'qty' => 1, 'price' => 7.00]], 'sub' => 7.00, 'total' => 9.00, 'status' => 'completed', 'days_ago' => 4],

            ['code' => 'BOM-2410', 'cust' => $sarah, 'vendor' => 'Warung Pak Mat', 'loc' => 'Block B - Cafeteria', 'slot' => 'Lunch (12:00 - 14:00)',
             'items' => [['name' => 'Nasi Campur', 'qty' => 2, 'price' => 7.50]], 'sub' => 15.00, 'total' => 17.00, 'status' => 'completed', 'days_ago' => 5],

            ['code' => 'BOM-2411', 'cust' => $johan, 'vendor' => 'Warung Pak Mat', 'loc' => 'Library Entrance', 'slot' => 'Morning (8:00 - 10:00)',
             'items' => [['name' => 'Roti Canai', 'qty' => 3, 'price' => 2.00], ['name' => 'Teh Tarik', 'qty' => 3, 'price' => 2.60]],
             'sub' => 13.80, 'total' => 15.80, 'status' => 'completed', 'days_ago' => 6],
        ];

        // Burger Station KL orders
        $burgerOrders = [
            ['code' => 'BOM-2405', 'cust' => $irfan, 'vendor' => 'Burger Station KL', 'loc' => 'Block B - Cafeteria', 'slot' => 'Evening (17:00 - 19:00)',
             'items' => [['name' => 'Burger Bakar Double', 'qty' => 3, 'price' => 7.33]], 'sub' => 22.00, 'total' => 24.00, 'status' => 'pending', 'days_ago' => 0],

            ['code' => 'BOM-2412', 'cust' => $adam, 'vendor' => 'Burger Station KL', 'loc' => 'Dewan Kuliah Utama', 'slot' => 'Evening (17:00 - 19:00)',
             'items' => [['name' => 'Chicken Burger Set', 'qty' => 1, 'price' => 9.00], ['name' => 'Fries Large', 'qty' => 1, 'price' => 4.00]],
             'sub' => 13.00, 'total' => 15.00, 'status' => 'processing', 'days_ago' => 0],

            ['code' => 'BOM-2413', 'cust' => $nurul, 'vendor' => 'Burger Station KL', 'loc' => 'Block A - Main Lobby', 'slot' => 'Evening (17:00 - 19:00)',
             'items' => [['name' => 'Fish Burger', 'qty' => 2, 'price' => 8.50]], 'sub' => 17.00, 'total' => 19.00, 'status' => 'completed', 'days_ago' => 1],

            ['code' => 'BOM-2414', 'cust' => $lina, 'vendor' => 'Burger Station KL', 'loc' => 'Block B - Cafeteria', 'slot' => 'Lunch (12:00 - 14:00)',
             'items' => [['name' => 'BBQ Beef Burger', 'qty' => 1, 'price' => 12.00]], 'sub' => 12.00, 'total' => 14.00, 'status' => 'completed', 'days_ago' => 2],

            // Walk-in order (no customer account)
            ['code' => 'BOM-2415', 'cust' => null, 'cust_name' => 'Hafiz (walk-in)', 'vendor' => 'Burger Station KL', 'loc' => 'Counter', 'slot' => 'Lunch (12:00 - 14:00)',
             'items' => [['name' => 'Burger Bakar Double', 'qty' => 2, 'price' => 7.33]], 'sub' => 14.66, 'total' => 16.66, 'status' => 'completed', 'days_ago' => 3],
        ];

        foreach (array_merge($pakMatOrders, $burgerOrders) as $o) {
            Order::create([
                'order_code' => $o['code'],
                'customer_id' => $o['cust'] ? $o['cust']->id : null,
                'customer_name' => $o['cust_name'] ?? null,
                'vendor_name' => $o['vendor'],
                'delivery_location' => $o['loc'],
                'delivery_date' => now()->subDays($o['days_ago'])->toDateString(),
                'time_slot' => $o['slot'],
                'items' => $o['items'],
                'subtotal' => $o['sub'],
                'delivery_fee' => 2.00,
                'total' => $o['total'],
                'notes' => null,
                'vendor_note' => null,
                'status' => $o['status'],
                'created_at' => now()->subDays($o['days_ago'])->setTime(rand(7, 18), rand(0, 59)),
                'updated_at' => now()->subDays($o['days_ago'])->setTime(rand(7, 18), rand(0, 59)),
            ]);
        }
    }
}
