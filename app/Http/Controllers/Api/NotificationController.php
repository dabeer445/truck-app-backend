<?php

namespace App\Http\Controllers\Api;

use App\Models\Order;
use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;


/**
 * @OA\Tag(
 *     name="Notifications",
 *     description="API Endpoints for managing Notifications"
 * )
 */
class NotificationController extends Controller
{
    use AuthorizesRequests;
    /**
     * @OA\Get(
     *     path="/api/notifications",
     *     tags={"Notifications"},
     *     summary="Get a list of notifications",
     *     @OA\Parameter(
     *         name="read",
     *         in="query",
     *         required=false,
     *         description="Filter by read/unread status",
     *         @OA\Schema(type="boolean")
     *     ),
     *     @OA\Parameter(
     *         name="type",
     *         in="query",
     *         required=false,
     *         description="Filter by notification type",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="from",
     *         in="query",
     *         required=false,
     *         description="Filter notifications from this date",
     *         @OA\Schema(type="string", format="date-time")
     *     ),
     *     @OA\Parameter(
     *         name="to",
     *         in="query",
     *         required=false,
     *         description="Filter notifications to this date",
     *         @OA\Schema(type="string", format="date-time")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="A list of notifications",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/NotificationResource"))
     *     )
     * )
     */
    public function index(Request $request)
    {
        $query = request()->user()->notifications();
        // Filter by read/unread
        if ($request->has('read')) {
            $query = $request->read ?
                $query->whereNotNull('read_at') :
                $query->whereNull('read_at');
        }

        // Filter by type
        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        // Filter by date range
        if ($request->has('from')) {
            $query->where('created_at', '>=', $request->from);
        }

        if ($request->has('to')) {
            $query->where('created_at', '<=', $request->to);
        }

        return $query->paginate($request->per_page ?? 15);
    }
    /**
     * @OA\Post(
     *     path="/api/notifications/{id}/read",
     *     tags={"Notifications"},
     *     summary="Mark a notification as read",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the notification to mark as read",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Notification marked as read",
     *         @OA\JsonContent(type="object", @OA\Property(property="message", type="string"))
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Notification not found"
     *     )
     * )
     */
    public function markAsRead($id)
    {
        $notification = request()->user()
            ->notifications()
            ->findOrFail($id);

        $notification->markAsRead();

        return response()->json(['message' => 'Marked as read']);
    }
    /**
     * @OA\Post(
     *     path="/api/notifications/read",
     *     tags={"Notifications"},
     *     summary="Mark all notifications as read",
     *     @OA\Response(
     *         response=200,
     *         description="All notifications marked as read",
     *         @OA\JsonContent(type="object", @OA\Property(property="message", type="string"))
     *     )
     * )
     */
    public function markAllAsRead()
    {
        request()->user()->unreadNotifications
            ->markAsRead();

        return response()->json(['message' => 'All notifications marked as read']);
    }
    /**
     * @OA\Delete(
     *     path="/api/notifications/{id}",
     *     tags={"Notifications"},
     *     summary="Delete a notification",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the notification to delete",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Notification deleted",
     *         @OA\JsonContent(type="object", @OA\Property(property="message", type="string"))
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Notification not found"
     *     )
     * )
     */
    public function destroy($id)
    {
        $notification = request()->user()
            ->notifications()
            ->findOrFail($id);

        $notification->delete();

        return response()->json(['message' => 'Notification deleted']);
    }
}
