<?php
// Create a database seeder (database/seeders/AdminSeeder.php)
namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use App\Models\Order;

class OrderSeeder extends Seeder
{
    public function run()
    {
        // Get customer user
        $customers = User::whereHas('roles', function ($query) {
            $query->where('name', 'customer');
        })->take(5)->get();

        $STATUSES = [
            'pending',
            'in_progress',
            'completed',
            'cancelled'
        ];

        // Create at least 20 orders
        for ($i = 0; $i < 20; $i++) {
            $customer = $customers->random(); // Randomly select a user

            $customerOrder = Order::create([
                'user_id' => $customer->id,
                'pickup_location' => '123 Start St',
                'delivery_location' => '456 End Ave',
                'cargo_details' => [
                    'weight' => rand(50, 150), // Random weight between 50 and 150
                    'description' => 'Cargo ' . $i // Example cargo description
                ],
                'pickup_time' => now()->addDays(rand(1, 5)), // Random pickup time within 1 to 5 days
                'delivery_time' => now()->addDays(rand(6, 10)), // Random delivery time within 6 to 10 days
                'status' => $STATUSES[array_rand($STATUSES)] // Random status
            ]);
        }
    }
}
