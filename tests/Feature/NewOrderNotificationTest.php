<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Order;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Notification;
use App\Notifications\NewOrderNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Notifications\Messages\MailMessage;

class NewOrderNotificationTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected Order $order;
    protected NewOrderNotification $notification;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(); // Run seeders for roles

        // Create test users
        $this->admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('Password123!'),
            'phone' => '1234567890'
        ]);
        $this->admin->assignRole('admin');

        // Create test data
        $this->order = Order::create([
            'user_id' => $this->admin->id,
            'pickup_location' => '123 Start St',
            'delivery_location' => '456 End Ave',
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
        $this->notification = new NewOrderNotification($this->order);
    }

    /** @test */
    public function notification_contains_correct_order_information()
    {
        // Test database notification content
        $data = $this->notification->toDatabase($this->admin);

        $this->assertEquals($this->order->id, $data['order_id']);
        $this->assertEquals('new_order', $data['type']);
        $this->assertEquals(
            "New order #{$this->order->id} created",
            $data['message']
        );
        $this->assertEquals($this->order->pickup_location, $data['data']['pickup_location']);
        $this->assertEquals($this->order->delivery_location, $data['data']['delivery_location']);
        $this->assertEquals($this->order->status, $data['data']['status']);
    }


    /** @test */
    public function notification_is_queued()
    {
        Queue::fake();

        $this->admin->notify($this->notification);

        Queue::assertPushed(\Illuminate\Notifications\SendQueuedNotifications::class);
    }

    /** @test */
    public function notification_uses_correct_channels()
    {
        $channels = $this->notification->via($this->admin);

        $this->assertContains('database', $channels);
        $this->assertContains('mail', $channels);
    }

    /** @test */
    public function email_has_correct_content()
    {
        $mailMessage = $this->notification->toMail($this->admin);

        $this->assertInstanceOf(MailMessage::class, $mailMessage);
        $this->assertEquals("New Order #{$this->order->id}", $mailMessage->subject);
        $this->assertContains('A new order has been created.', $mailMessage->introLines);
        $this->assertEquals(
            url("/orders/{$this->order->id}"),
            $mailMessage->actionUrl
        );
    }

    /** @test */
    public function notification_is_sent_to_all_admins()
    {
        Notification::fake();

        // Create multiple admins
        $admins = [];
        for ($i = 0; $i < 3; $i++) {
            $user = User::create([
                'name' => "Admin User $i", // Changed backticks to double quotes
                'email' => "admin$i@example.com", // Changed backticks to double quotes
                'password' => bcrypt('Password123!'),
                'phone' => '1234567890'
            ]);
            $user->assignRole('admin');
            array_push($admins, $user);
        }
        // Trigger notification sending (this would typically be in your OrderController)
        User::role('admin')->each(function ($admin) {
            $admin->notify($this->notification);
        });

        // Verify each admin received the notification
        Notification::assertSentTo(
            $admins,
            NewOrderNotification::class,
            function ($notification, $channels) {
                return $notification->order->id === $this->order->id;
            }
        );
    }

    // /** @test */
    public function notification_handles_deleted_orders()
    {
        $order = Order::create([
            'user_id' => $this->admin->id,
            'pickup_location' => '789 Other St',
            'delivery_location' => '012 Other Ave',
            'cargo_details' => ['weight' => 200],
            'pickup_time' => now()->addDay(),
            'delivery_time' => now()->addDays(2),
            'status' => 'pending'
        ]);
        $notification = new NewOrderNotification($order);

        // Store notification before deleting order
        $data = $notification->toDatabase($this->admin);

        // Delete the order
        $order->delete();

        // Verify notification still contains essential information
        $this->assertEquals($order->id, $data['order_id']);
        $this->assertEquals('new_order', $data['type']);
        $this->assertNotNull($data['message']);
    }
}
