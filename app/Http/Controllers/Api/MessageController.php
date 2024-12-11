<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Message;
use App\Models\User;
use App\Http\Resources\MessageResource;
use App\Notifications\NewMessageNotification;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Notification;

/**
 * @OA\Tag(
 *     name="Messages",
 *     description="API Endpoints for managing Messages"
 * )
 */
class MessageController extends Controller
{
    use AuthorizesRequests;
    /**
     * @OA\Get(
     *     path="/messages",
     *     tags={"Messages"},
     *     summary="Get a list of messages",
     *     @OA\Parameter(
     *         name="order_id",
     *         in="query",
     *         required=false,
     *         description="Filter messages by order ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="A list of messages",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/MessageResource")
     *         )     
     *     )
     * )
     */
    public function index(Request $request)
    {
        $messages = Message::where(function ($query) {
            $query->where('sender_id', auth('sanctum')->id())
                ->orWhere('receiver_id', auth('sanctum')->id());
        });

        // Filter by order if provided
        if ($request->has('order_id')) {
            $messages->where('order_id', $request->order_id);
        }

        return MessageResource::collection(
            $messages->latest()->paginate($request->per_page ?? 10)
        );
    }

    /**
     * @OA\Post(
     *     path="/messages",
     *     tags={"Messages"},
     *     summary="Store a new message",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/MessageRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Message created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/MessageResource")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(ref="#/components/schemas/ValidationError")
     *     )
     * )
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'content' => 'required|string|max:1000',
        ]);

        if ($request->user()->hasRole('customer')) {
            $admin = User::whereHas('roles', function ($query) {
                $query->where('name', 'admin');
            })->first();
            $receiverId = $admin->id;
        } else {
            $rece_validated = $request->validate([
                'receiver_id' => 'required|exists:users,id',
            ]);
            $receiverId = $request->input('receiver_id'); // Use the provided receiver_id
        }


        $message = Message::create([
            ...$validated,
            'receiver_id' => $receiverId,
            'sender_id' => auth('sanctum')->id(),
        ]);

        // Notify the receiver
        Notification::sendNow($message->receiver, new NewMessageNotification($message));

        return new MessageResource($message);
    }

    /**
     * @OA\Put(
     *     path="/messages/{id}/read",
     *     tags={"Messages"},
     *     summary="Mark a message as read",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the message to mark as read",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Message marked as read",
     *         @OA\JsonContent(@OA\Property(property="message", type="string", example="Marked as read"))
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Message not found",
     *         @OA\JsonContent(ref="#/components/schemas/NotFoundError")
     *     )
     * )
     */
    public function markAsRead($id)
    {
        $message = Message::where('receiver_id', auth('sanctum')->id())
            ->findOrFail($id);

        $message->markAsRead();

        return response()->json(['message' => 'Marked as read']);
    }
    /**
     * @OA\Get(
     *     path="/messages/unread-count",
     *     tags={"Messages"},
     *     summary="Get the count of unread messages",
     *     @OA\Response(
     *         response=200,
     *         description="Count of unread messages",
     *         @OA\JsonContent(@OA\Property(property="unread_count", type="integer"))
     *     )
     * )
     */
    public function getUnreadCount()
    {
        $count = Message::where('receiver_id', auth('sanctum')->id())
            ->where('is_read', false)
            ->count();

        return response()->json(['unread_count' => $count]);
    }

    /**
     * @OA\Get(
     *     path="/messages/conversation/{orderId}",
     *     tags={"Messages"},
     *     summary="Get messages for a specific conversation",
     *     @OA\Parameter(
     *         name="orderId",
     *         in="path",
     *         required=true,
     *         description="ID of the order to get messages for",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="A list of messages for the conversation",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/MessageResource")
     *         )     
     *     )
     * )
     */
    public function getConversation(Order $order)
    {
        $messages = Message::where('order_id', $order->id)
            ->where(function ($query) {
                $query->where('sender_id', auth('sanctum')->id())
                    ->orWhere('receiver_id', auth('sanctum')->id());
            })
            ->latest()
            ->paginate(15);

        return MessageResource::collection($messages);
    }
}
