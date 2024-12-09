<?php
// Create a database seeder (database/seeders/AdminSeeder.php)
namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\Message;
use App\Notifications\NewMessageNotification;
use App\Notifications\NewOrderNotification;
use Illuminate\Support\Facades\Notification;
 
class NotificationSeeder extends Seeder
{
    public function run()
    {
        $admins = User::whereHas('roles', function ($query) {
            $query->where('name', 'admin');
        })->take(5)->get();

        $order = Order::create([
            'user_id' => $admins->random()->id,
            'pickup_location' => '999 End St',
            'delivery_location' => '456 Start Ave',
            'cargo_details' => [
                'weight' => 100,
                'dimensions' => [
                    'length' => 10,
                    'width' => 10,
                    'height' => 10
                ]
            ],
            'pickup_time' => now()->addDay()->format('Y-m-d H:i:s'),
            'delivery_time' => now()->addDays(2)->format('Y-m-d H:i:s')
        ]);
        $message = Message::create([
            'order_id' => $order->id,
            'sender_id' => $admins->random()->id,
            'receiver_id' => $order->user_id,
            'content' => "DADAS ASd DSA",
            'is_read' => false,
            'read_at' => null
        ]);
        $orderNotification = new NewOrderNotification($order);
        $msgNotification = new NewMessageNotification($message);
        for ($i = 0; $i < 5; $i++) {
            $admin = $admins->random(); // Randomly select a user
            $admin->notify($orderNotification);
            Notification::sendNow($admins, $orderNotification);
            Notification::sendNow($admins, $msgNotification);
        }
    }
}
