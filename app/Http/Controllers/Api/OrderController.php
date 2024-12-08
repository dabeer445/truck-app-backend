<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateOrderRequest;
use App\Models\Order;
use App\Http\Resources\OrderResource;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
class OrderController extends Controller
{
    protected $orderService;
    use AuthorizesRequests;

    /**
     * @OA\Post(
     *     path="/api/v1/orders",
     *     summary="Create a new order",
     *     tags={"Orders"},
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Order creation data",
     *         @OA\JsonContent(ref="#/components/schemas/CreateOrderRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Order created successfully",
     *         @OA\JsonContent(
     *             allOf={
     *                 @OA\Schema(ref="#/components/schemas/Order")
     *             }
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(ref="#/components/schemas/Error")
     *     )
     * )
     */
    public function store(CreateOrderRequest $request)
    {
        $this->authorize('create', Order::class);
        
        $order = Order::create([
            'user_id' => $request->user()->id,
            'status' => 'pending',
            ...$request->validated()
        ]);

        return new OrderResource($order);
    }
    /**
     * @OA\Get(
     *     path="/api/v1/orders",
     *     summary="Retrieve a list of orders",
     *     tags={"Orders"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="A list of orders",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/OrderCollection"))
     *     )
     * )
     */
    public function index(Request $request)
    {
        $orders = Order::where('user_id', $request->user()->id)
            ->latest()
            ->paginate(10);

        return OrderResource::collection($orders);
    }
    /**
     * @OA\Get(
     *     path="/api/v1/orders/{id}",
     *     summary="Retrieve a specific order",
     *     tags={"Orders"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Order retrieved successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Order")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Order not found"
     *     )
     * )
     */
    public function show(Order $order)
    {
        $this->authorize('view', $order);
        return new OrderResource($order);
    }
    /**
     * @OA\Put(
     *     path="/api/v1/orders/{id}",
     *     summary="Update a specific order",
     *     tags={"Orders"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"pickup_location","delivery_location","cargo_details","pickup_time","delivery_time"},
     *             @OA\Property(property="pickup_location", type="string"),
     *             @OA\Property(property="delivery_location", type="string"),
     *             @OA\Property(property="cargo_details", type="object",
     *                 @OA\Property(property="weight", type="number"),
     *                 @OA\Property(property="dimensions", type="object",
     *                     @OA\Property(property="length", type="number"),
     *                     @OA\Property(property="width", type="number"),
     *                     @OA\Property(property="height", type="number")
     *                 )
     *             ),
     *             @OA\Property(property="pickup_time", type="string", format="datetime"),
     *             @OA\Property(property="delivery_time", type="string", format="datetime"),
     *             @OA\Property(property="status", type="string"),
     *             @OA\Property(property="notes", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Order updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Order")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Order not found"
     *     )
     * )
     */
    public function update(Request $request, Order $order)
    {
        $this->authorize('update', $order);
        $validated = $request->validate([
            'pickup_location' => 'sometimes|string',
            'delivery_location' => 'sometimes|string',
            'cargo_details' => 'sometimes|array',
            'pickup_time' => 'sometimes|date|after:now',
            'delivery_time' => 'sometimes|date|after:pickup_time',
        ]);

        $order->update($validated);

        return new OrderResource($order);
    }
    /**
     * @OA\Delete(
     *     path="/api/v1/orders/{id}/cancel",
     *     summary="Cancel a specific order",
     *     tags={"Orders"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Order cancelled successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Order")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Only pending orders can be cancelled"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Order not found"
     *     )
     * )
     */
    public function cancel(Order $order)
    {
        $this->authorize('cancel', $order);

        if ($order->status !== 'pending') {
            return response()->json([
                'message' => 'Only pending orders can be cancelled'
            ], 422);
        }

        $order->update(['status' => 'cancelled']);

        return new OrderResource($order);
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/orders/{id}",
     *     summary="Delete a specific order",
     *     tags={"Orders"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Order deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Order not found"
     *     )
     * )
     */
    public function destroy(Order $order)
    {
        $this->authorize('delete', $order);
        $order->delete();

        return response()->json([
            'message' => 'Order deleted successfully'
        ]);
    }
}
