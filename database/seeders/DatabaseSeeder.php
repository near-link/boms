<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
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

        // ========== Products ==========
        // Warung Pak Mat menu
        $pakMatProducts = [
            ['name' => 'Nasi Lemak Special', 'description' => 'Fragrant coconut rice with sambal, fried egg, anchovies, peanuts, and rendang chicken.', 'price' => 6.00, 'category' => 'Rice', 'stock' => 50, 'image' => 'nasi_lemak_special.png'],
            ['name' => 'Nasi Ayam Penyet', 'description' => 'Smashed fried chicken served with steamed rice, sambal, and lalapan.', 'price' => 8.00, 'category' => 'Rice', 'stock' => 30, 'image' => 'nasi_ayam_penyet.png'],
            ['name' => 'Nasi Goreng Kampung', 'description' => 'Village-style fried rice with anchovies, vegetables, and a fried egg.', 'price' => 7.00, 'category' => 'Rice', 'stock' => 40, 'image' => 'nasi_goreng_kampung.png'],
            ['name' => 'Nasi Campur', 'description' => 'Steamed rice with your choice of 2 side dishes from the daily selection.', 'price' => 7.50, 'category' => 'Rice', 'stock' => 35, 'image' => 'nasi_campur.png'],
            ['name' => 'Mee Goreng Mamak', 'description' => 'Stir-fried yellow noodles with soy sauce, egg, tofu, and vegetables.', 'price' => 6.50, 'category' => 'Noodles', 'stock' => 40, 'image' => 'mee_goreng_mamak.png'],
            ['name' => 'Laksa Penang', 'description' => 'Penang-style rice noodles in tangy fish-based broth with shredded mackerel.', 'price' => 7.00, 'category' => 'Noodles', 'stock' => 25, 'image' => 'laksa_penang.png'],
            ['name' => 'Roti Canai', 'description' => 'Crispy and fluffy flatbread served with dhal and sambal.', 'price' => 2.00, 'category' => 'Snacks', 'stock' => 100, 'image' => 'roti_canai.png'],
            ['name' => 'Ayam Goreng Set', 'description' => 'Deep fried chicken set with rice, coleslaw, and chili sauce.', 'price' => 8.00, 'category' => 'Rice', 'stock' => 25, 'image' => 'ayam_goreng_set.png'],
            ['name' => 'Teh Tarik', 'description' => 'Pulled milk tea — creamy and frothy Malaysian classic.', 'price' => 2.60, 'category' => 'Drinks', 'stock' => 200, 'image' => 'teh_tarik.png'],
            ['name' => 'Kopi O', 'description' => 'Traditional black coffee with sugar.', 'price' => 2.00, 'category' => 'Drinks', 'stock' => 200, 'image' => 'kopi_o.png'],
            ['name' => 'Milo Ais', 'description' => 'Iced chocolate malt drink — a Malaysian favourite.', 'price' => 3.50, 'category' => 'Drinks', 'stock' => 150, 'image' => 'milo_ais.png'],
            ['name' => 'Air Sirap Limau', 'description' => 'Rose syrup with fresh lime juice over ice.', 'price' => 2.50, 'category' => 'Drinks', 'stock' => 150, 'image' => 'air_sirap_limau.png'],
        ];

        foreach ($pakMatProducts as $p) {
            Product::create(array_merge($p, ['vendor_id' => $pakMat->id]));
        }

        // Burger Station KL menu
        $burgerProducts = [
            ['name' => 'Burger Bakar Double', 'description' => 'Double grilled beef patty with cheddar, caramelized onions, and smoky BBQ sauce.', 'price' => 7.33, 'category' => 'Burgers', 'stock' => 40, 'image' => 'burger_bakar_double.png'],
            ['name' => 'Chicken Burger Set', 'description' => 'Crispy chicken burger with lettuce, mayo, fries, and a drink.', 'price' => 9.00, 'category' => 'Burgers', 'stock' => 35, 'image' => 'chicken_burger_set.png'],
            ['name' => 'Fish Burger', 'description' => 'Breaded fish fillet with tartar sauce, lettuce, and sesame bun.', 'price' => 8.50, 'category' => 'Burgers', 'stock' => 30, 'image' => 'fish_burger.png'],
            ['name' => 'BBQ Beef Burger', 'description' => 'Premium beef patty with BBQ glaze, jalapeños, and crispy bacon.', 'price' => 12.00, 'category' => 'Burgers', 'stock' => 20, 'image' => 'bbq_beef_burger.png'],
            ['name' => 'Veggie Burger', 'description' => 'Plant-based patty with avocado, tomato, and herb mayo.', 'price' => 8.00, 'category' => 'Burgers', 'stock' => 25, 'image' => 'veggie_burger.jpg'],
            ['name' => 'Fries Large', 'description' => 'Golden crispy french fries with seasoning salt.', 'price' => 4.00, 'category' => 'Sides', 'stock' => 100, 'image' => 'fries_large.jpg'],
            ['name' => 'Onion Rings', 'description' => 'Battered and fried sweet onion rings with dipping sauce.', 'price' => 4.50, 'category' => 'Sides', 'stock' => 80, 'image' => 'onion_rings.jpg'],
            ['name' => 'Nuggets (6pc)', 'description' => 'Crispy chicken nuggets with BBQ and chili sauce.', 'price' => 5.00, 'category' => 'Sides', 'stock' => 60, 'image' => 'nuggets.jpg'],
            ['name' => 'Coleslaw', 'description' => 'Fresh cabbage and carrot slaw with creamy dressing.', 'price' => 2.50, 'category' => 'Sides', 'stock' => 80, 'image' => 'coleslaw.jpg'],
            ['name' => 'Iced Lemon Tea', 'description' => 'Refreshing lemon tea served over crushed ice.', 'price' => 3.00, 'category' => 'Drinks', 'stock' => 150, 'image' => 'iced_lemon_tea.jpg'],
            ['name' => 'Milkshake Chocolate', 'description' => 'Thick and creamy chocolate milkshake topped with whipped cream.', 'price' => 6.00, 'category' => 'Drinks', 'stock' => 50, 'image' => 'milkshake_chocolate.jpg'],
            ['name' => 'Sparkling Water', 'description' => 'Chilled carbonated water with a slice of lime.', 'price' => 2.00, 'category' => 'Drinks', 'stock' => 100, 'image' => 'sparkling_water.jpg'],
        ];

        foreach ($burgerProducts as $p) {
            Product::create(array_merge($p, ['vendor_id' => $burgerKL->id]));
        }

        // ========== Reviews ==========
        $reviews = [
            ['product' => 'Nasi Lemak Special', 'user' => $alif, 'rating' => 5, 'comment' => 'Best nasi lemak on campus! The sambal is 🔥'],
            ['product' => 'Nasi Lemak Special', 'user' => $sarah, 'rating' => 4, 'comment' => 'Really good, just wish the portion was bigger.'],
            ['product' => 'Teh Tarik', 'user' => $johan, 'rating' => 5, 'comment' => 'Perfect pull every time.'],
            ['product' => 'Roti Canai', 'user' => $nurul, 'rating' => 4, 'comment' => 'Crispy and flaky, great with dhal.'],
            ['product' => 'Burger Bakar Double', 'user' => $irfan, 'rating' => 5, 'comment' => 'Juicy and smoky, worth every ringgit.'],
            ['product' => 'Burger Bakar Double', 'user' => $adam, 'rating' => 4, 'comment' => 'Solid burger. Would order again.'],
            ['product' => 'Chicken Burger Set', 'user' => $lina, 'rating' => 4, 'comment' => 'Good value set meal.'],
            ['product' => 'Fries Large', 'user' => $iman, 'rating' => 3, 'comment' => 'Decent fries, nothing special.'],
            ['product' => 'Mee Goreng Mamak', 'user' => $alif, 'rating' => 4, 'comment' => 'Tasty and filling!'],
            ['product' => 'Milkshake Chocolate', 'user' => $sarah, 'rating' => 5, 'comment' => 'So thick and creamy 😍'],
        ];

        foreach ($reviews as $r) {
            $product = Product::where('name', $r['product'])->first();
            if ($product) {
                Review::create([
                    'product_id' => $product->id,
                    'user_id' => $r['user']->id,
                    'rating' => $r['rating'],
                    'comment' => $r['comment'],
                ]);
            }
        }

        // ========== Orders ==========
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

        // ========== Historical Orders (6 months) for Reports + Prep List ==========
        $customers = [$alif, $sarah, $johan, $nurul, $irfan, $iman, $adam, $lina];
        $locations = ['Block A - Main Lobby', 'Block B - Cafeteria', 'Library Entrance', 'Dewan Kuliah Utama', 'Counter'];
        $slots = ['Morning (8:00 - 10:00)', 'Lunch (12:00 - 14:00)', 'Evening (17:00 - 19:00)'];

        $pakMatMenu = [
            ['name' => 'Nasi Lemak Special', 'price' => 6.00],
            ['name' => 'Nasi Ayam Penyet', 'price' => 8.00],
            ['name' => 'Nasi Goreng Kampung', 'price' => 7.00],
            ['name' => 'Nasi Campur', 'price' => 7.50],
            ['name' => 'Mee Goreng Mamak', 'price' => 6.50],
            ['name' => 'Laksa Penang', 'price' => 7.00],
            ['name' => 'Roti Canai', 'price' => 2.00],
            ['name' => 'Ayam Goreng Set', 'price' => 8.00],
            ['name' => 'Teh Tarik', 'price' => 2.60],
            ['name' => 'Kopi O', 'price' => 2.00],
            ['name' => 'Milo Ais', 'price' => 3.50],
        ];

        $burgerMenu = [
            ['name' => 'Burger Bakar Double', 'price' => 7.33],
            ['name' => 'Chicken Burger Set', 'price' => 9.00],
            ['name' => 'Fish Burger', 'price' => 8.50],
            ['name' => 'BBQ Beef Burger', 'price' => 12.00],
            ['name' => 'Fries Large', 'price' => 4.00],
            ['name' => 'Onion Rings', 'price' => 4.50],
            ['name' => 'Nuggets (6pc)', 'price' => 5.00],
            ['name' => 'Iced Lemon Tea', 'price' => 3.00],
            ['name' => 'Milkshake Chocolate', 'price' => 6.00],
        ];

        $orderCode = 2500;

        // Generate 6 months of history (~26 weeks)
        for ($week = 1; $week <= 26; $week++) {
            for ($day = 0; $day < 7; $day++) {
                $date = now()->subWeeks($week)->startOfWeek()->addDays($day);
                // Skip weekends for realism (lower volume)
                $isWeekend = $day >= 5;
                // Growth factor: older weeks get fewer orders (simulates business growth)
                $growthFactor = max(0.4, $week <= 4 ? 1.0 : 1.0 - (($week - 4) * 0.025));

                // Pak Mat: 2-6 orders per day (scaled)
                $baseOrders = $isWeekend ? rand(1, 3) : rand(3, 6);
                $numOrders = max(1, (int) round($baseOrders * $growthFactor));
                for ($i = 0; $i < $numOrders; $i++) {
                    $cust = $customers[array_rand($customers)];
                    $numItems = rand(1, 3);
                    $items = [];
                    $subtotal = 0;
                    for ($j = 0; $j < $numItems; $j++) {
                        $product = $pakMatMenu[array_rand($pakMatMenu)];
                        $qty = rand(1, 3);
                        $items[] = ['name' => $product['name'], 'qty' => $qty, 'price' => $product['price']];
                        $subtotal += $product['price'] * $qty;
                    }

                    Order::create([
                        'order_code' => 'BOM-' . $orderCode++,
                        'customer_id' => $cust->id,
                        'vendor_name' => 'Warung Pak Mat',
                        'delivery_location' => $locations[array_rand($locations)],
                        'delivery_date' => $date->toDateString(),
                        'time_slot' => $slots[array_rand($slots)],
                        'items' => $items,
                        'subtotal' => round($subtotal, 2),
                        'delivery_fee' => 2.00,
                        'total' => round($subtotal + 2, 2),
                        'status' => 'completed',
                        'created_at' => $date->copy()->setTime(rand(7, 18), rand(0, 59)),
                        'updated_at' => $date->copy()->setTime(rand(7, 18), rand(0, 59)),
                    ]);
                }

                // Burger Station: 1-4 orders per day (scaled)
                $baseOrders = $isWeekend ? rand(1, 2) : rand(2, 4);
                $numOrders = max(1, (int) round($baseOrders * $growthFactor));
                for ($i = 0; $i < $numOrders; $i++) {
                    $cust = $customers[array_rand($customers)];
                    $numItems = rand(1, 3);
                    $items = [];
                    $subtotal = 0;
                    for ($j = 0; $j < $numItems; $j++) {
                        $product = $burgerMenu[array_rand($burgerMenu)];
                        $qty = rand(1, 2);
                        $items[] = ['name' => $product['name'], 'qty' => $qty, 'price' => $product['price']];
                        $subtotal += $product['price'] * $qty;
                    }

                    Order::create([
                        'order_code' => 'BOM-' . $orderCode++,
                        'customer_id' => $cust->id,
                        'vendor_name' => 'Burger Station KL',
                        'delivery_location' => $locations[array_rand($locations)],
                        'delivery_date' => $date->toDateString(),
                        'time_slot' => $slots[array_rand($slots)],
                        'items' => $items,
                        'subtotal' => round($subtotal, 2),
                        'delivery_fee' => 2.00,
                        'total' => round($subtotal + 2, 2),
                        'status' => 'completed',
                        'created_at' => $date->copy()->setTime(rand(7, 18), rand(0, 59)),
                        'updated_at' => $date->copy()->setTime(rand(7, 18), rand(0, 59)),
                    ]);
                }
            }
        }

        // ========== Extra processing orders for Delivery Manifest demo ==========
        $manifestOrders = [
            ['code' => 'BOM-9001', 'cust' => $nurul, 'vendor' => 'Warung Pak Mat', 'loc' => 'Block A - Main Lobby', 'slot' => 'Morning (8:00 - 10:00)',
             'items' => [['name' => 'Nasi Lemak Special', 'qty' => 3, 'price' => 6.00], ['name' => 'Teh Tarik', 'qty' => 3, 'price' => 2.60]],
             'sub' => 25.80, 'total' => 27.80, 'notes' => 'Extra sambal please'],
            ['code' => 'BOM-9002', 'cust' => $irfan, 'vendor' => 'Warung Pak Mat', 'loc' => 'Block A - Main Lobby', 'slot' => 'Morning (8:00 - 10:00)',
             'items' => [['name' => 'Roti Canai', 'qty' => 4, 'price' => 2.00]], 'sub' => 8.00, 'total' => 10.00, 'notes' => null],
            ['code' => 'BOM-9003', 'cust' => $sarah, 'vendor' => 'Warung Pak Mat', 'loc' => 'Block B - Cafeteria', 'slot' => 'Morning (8:00 - 10:00)',
             'items' => [['name' => 'Nasi Goreng Kampung', 'qty' => 1, 'price' => 7.00], ['name' => 'Kopi O', 'qty' => 1, 'price' => 2.00]],
             'sub' => 9.00, 'total' => 11.00, 'notes' => null],
            ['code' => 'BOM-9004', 'cust' => $adam, 'vendor' => 'Warung Pak Mat', 'loc' => 'Library Entrance', 'slot' => 'Lunch (12:00 - 14:00)',
             'items' => [['name' => 'Nasi Ayam Penyet', 'qty' => 2, 'price' => 8.00]], 'sub' => 16.00, 'total' => 18.00, 'notes' => 'No spicy'],
            ['code' => 'BOM-9005', 'cust' => $lina, 'vendor' => 'Warung Pak Mat', 'loc' => 'Library Entrance', 'slot' => 'Lunch (12:00 - 14:00)',
             'items' => [['name' => 'Laksa Penang', 'qty' => 1, 'price' => 7.00], ['name' => 'Milo Ais', 'qty' => 1, 'price' => 3.50]],
             'sub' => 10.50, 'total' => 12.50, 'notes' => null],
            ['code' => 'BOM-9006', 'cust' => $alif, 'vendor' => 'Warung Pak Mat', 'loc' => 'Dewan Kuliah Utama', 'slot' => 'Lunch (12:00 - 14:00)',
             'items' => [['name' => 'Ayam Goreng Set', 'qty' => 1, 'price' => 8.00]], 'sub' => 8.00, 'total' => 10.00, 'notes' => 'Meeting lunch'],
            ['code' => 'BOM-9007', 'cust' => $johan, 'vendor' => 'Warung Pak Mat', 'loc' => 'Block A - Main Lobby', 'slot' => 'Evening (17:00 - 19:00)',
             'items' => [['name' => 'Nasi Campur', 'qty' => 2, 'price' => 7.50], ['name' => 'Air Sirap Limau', 'qty' => 2, 'price' => 2.50]],
             'sub' => 20.00, 'total' => 22.00, 'notes' => null],

            // Burger Station processing orders
            ['code' => 'BOM-9008', 'cust' => $iman, 'vendor' => 'Burger Station KL', 'loc' => 'Block B - Cafeteria', 'slot' => 'Evening (17:00 - 19:00)',
             'items' => [['name' => 'Burger Bakar Double', 'qty' => 2, 'price' => 7.33], ['name' => 'Fries Large', 'qty' => 2, 'price' => 4.00]],
             'sub' => 22.66, 'total' => 24.66, 'notes' => null],
            ['code' => 'BOM-9009', 'cust' => $nurul, 'vendor' => 'Burger Station KL', 'loc' => 'Block A - Main Lobby', 'slot' => 'Evening (17:00 - 19:00)',
             'items' => [['name' => 'Chicken Burger Set', 'qty' => 1, 'price' => 9.00], ['name' => 'Milkshake Chocolate', 'qty' => 1, 'price' => 6.00]],
             'sub' => 15.00, 'total' => 17.00, 'notes' => 'Extra mayo'],
            ['code' => 'BOM-9010', 'cust' => $adam, 'vendor' => 'Burger Station KL', 'loc' => 'Dewan Kuliah Utama', 'slot' => 'Lunch (12:00 - 14:00)',
             'items' => [['name' => 'BBQ Beef Burger', 'qty' => 1, 'price' => 12.00], ['name' => 'Onion Rings', 'qty' => 1, 'price' => 4.50]],
             'sub' => 16.50, 'total' => 18.50, 'notes' => null],
        ];

        foreach ($manifestOrders as $o) {
            Order::create([
                'order_code' => $o['code'],
                'customer_id' => $o['cust']->id,
                'vendor_name' => $o['vendor'],
                'delivery_location' => $o['loc'],
                'delivery_date' => now()->toDateString(),
                'time_slot' => $o['slot'],
                'items' => $o['items'],
                'subtotal' => $o['sub'],
                'delivery_fee' => 2.00,
                'total' => $o['total'],
                'notes' => $o['notes'],
                'status' => 'processing',
                'created_at' => now()->setTime(rand(7, 12), rand(0, 59)),
                'updated_at' => now()->setTime(rand(7, 12), rand(0, 59)),
            ]);
        }
    }
}
