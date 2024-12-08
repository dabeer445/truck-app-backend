<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Order;
use App\Models\Message;
use Illuminate\Support\Facades\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Notifications\NewMessageNotification;

class MessageTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $customer;
    protected $order;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(); // Run seeders for roles

        // Create admin user
        $this->admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('Password123!'),
            'phone' => '1234567890'
        ]);
        $this->admin->assignRole('admin');

        // Create customer user
        $this->customer = User::create([
            'name' => 'Customer User',
            'email' => 'customer@example.com',
            'password' => bcrypt('Password123!'),
            'phone' => '1234567890'
        ]);
        $this->customer->assignRole('customer');

        // Create order
        $this->order = Order::create([
            'user_id' => $this->customer->id,
            'pickup_location' => '123 Start St',
            'delivery_location' => '456 End Ave',
            'cargo_details' => ['weight' => 100],
            'pickup_time' => now()->addDay(),
            'delivery_time' => now()->addDays(2),
            'status' => 'pending'
        ]);
    }

    /** @test */
    public function user_can_send_message()
    {
        $messageData = [
            'order_id' => $this->order->id,
            'sender_id' => $this->customer->id,
            'receiver_id' => $this->admin->id,
            'content' => 'Test message content'
        ];
        
        $message = Message::create($messageData);

        $this->assertDatabaseHas('messages', [
            'id' => $message->id,
            'content' => 'Test message content'
        ]);
    }

    /** @test */
    public function recipient_receives_notification_when_message_sent()
    {
        Notification::fake();

        $message = Message::create([
            'order_id' => $this->order->id,
            'sender_id' => $this->customer->id,
            'receiver_id' => $this->admin->id,
            'content' => 'Test message content'
        ]);

        $message->receiver->notify(new NewMessageNotification($message));

        Notification::assertSentTo(
            $this->admin,
            NewMessageNotification::class,
            function ($notification, $channels) use ($message) {
                return $notification->message->id === $message->id;
            }
        );
    }

    /** @test */
    public function can_mark_message_as_read()
    {
        $message = Message::create([
            'order_id' => $this->order->id,
            'sender_id' => $this->customer->id,
            'receiver_id' => $this->admin->id,
            'content' => 'Test message content'
        ]);

        $message->markAsRead();

        $this->assertTrue($message->fresh()->is_read);
        $this->assertNotNull($message->fresh()->read_at);
    }

    /** @test */
    public function can_get_unread_messages_count()
    {
        // Create multiple unread messages
        Message::create([
            'order_id' => $this->order->id,
            'sender_id' => $this->customer->id,
            'receiver_id' => $this->admin->id,
            'content' => 'Message 1'
        ]);

        Message::create([
            'order_id' => $this->order->id,
            'sender_id' => $this->customer->id,
            'receiver_id' => $this->admin->id,
            'content' => 'Message 2'
        ]);
        
        $unreadCount = Message::where('receiver_id', $this->admin->id)
            ->where('is_read', false)
            ->count();

        $this->assertEquals(2, $unreadCount);
    }

    /** @test */
    public function can_get_conversation_messages()
    {
        // Create messages in both directions
        Message::create([
            'order_id' => $this->order->id,
            'sender_id' => $this->customer->id,
            'receiver_id' => $this->admin->id,
            'content' => 'Customer message'
        ]);

        Message::create([
            'order_id' => $this->order->id,
            'sender_id' => $this->admin->id,
            'receiver_id' => $this->customer->id,
            'content' => 'Admin reply'
        ]);

        $messages = Message::where('order_id', $this->order->id)
            ->latest()
            ->get();

        $this->assertCount(2, $messages);
    }

    /** @test */
    public function cannot_send_empty_message()
    {
        $this->expectException(\Illuminate\Database\QueryException::class);

        Message::create([
            'order_id' => $this->order->id,
            'sender_id' => $this->customer->id,
            'receiver_id' => $this->admin->id,
            'content' => ''
        ]);
    }

    /** @test */
    public function notification_contains_correct_data()
    {
        $message = Message::create([
            'order_id' => $this->order->id,
            'sender_id' => $this->customer->id,
            'receiver_id' => $this->admin->id,
            'content' => 'Test notification content'
        ]);

        $notification = new NewMessageNotification($message);
        
        $notificationData = $notification->toDatabase($this->admin);

        $this->assertEquals($message->id, $notificationData['message_id']);
        $this->assertEquals($this->order->id, $notificationData['order_id']);
        $this->assertEquals($this->customer->id, $notificationData['sender_id']);
    }

    /** @test */
    public function messages_are_ordered_by_created_at()
    {
        // Create older message
        $oldMessage = Message::create([
            'order_id' => $this->order->id,
            'sender_id' => $this->customer->id,
            'receiver_id' => $this->admin->id,
            'content' => 'Old message',
            'created_at' => now()->subDays(5)
        ]);

        // Create newer message
        $newMessage = Message::create([
            'order_id' => $this->order->id,
            'sender_id' => $this->admin->id,
            'receiver_id' => $this->customer->id,
            'content' => 'New message',
            'created_at' => now()
        ]);

        $messages = Message::where('order_id', $this->order->id)
            ->orderBy('created_at', 'desc')
            ->get();
        print_r($messages);
        $this->assertEquals($newMessage->id, $messages->first()->id);
        $this->assertEquals($oldMessage->id, $messages->last()->id);
    }

    /** @test */
    public function can_get_messages_for_specific_order()
    {
        // Create another order
        $anotherOrder = Order::create([
            'user_id' => $this->customer->id,
            'pickup_location' => '789 Other St',
            'delivery_location' => '321 Another Ave',
            'cargo_details' => ['weight' => 150],
            'pickup_time' => now()->addDay(),
            'delivery_time' => now()->addDays(2),
            'status' => 'pending'
        ]);

        // Create message for first order
        Message::create([
            'order_id' => $this->order->id,
            'sender_id' => $this->customer->id,
            'receiver_id' => $this->admin->id,
            'content' => 'First order message'
        ]);

        // Create message for second order
        Message::create([
            'order_id' => $anotherOrder->id,
            'sender_id' => $this->customer->id,
            'receiver_id' => $this->admin->id,
            'content' => 'Second order message'
        ]);

        $firstOrderMessages = Message::where('order_id', $this->order->id)->get();
        $secondOrderMessages = Message::where('order_id', $anotherOrder->id)->get();

        $this->assertCount(1, $firstOrderMessages);
        $this->assertCount(1, $secondOrderMessages);
        $this->assertNotEquals(
            $firstOrderMessages->first()->content,
            $secondOrderMessages->first()->content
        );
    }
}