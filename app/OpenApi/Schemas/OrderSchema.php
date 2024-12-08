<?php

namespace App\OpenApi\Schemas;

/**
 * @OA\Schema(
 *     schema="OrderRequest",
 *     required={"pickup_location", "delivery_location", "cargo_details", "pickup_time", "delivery_time"},
 *     @OA\Property(property="pickup_location", type="string", example="123 Pickup St"),
 *     @OA\Property(property="delivery_location", type="string", example="456 Delivery Ave"),
 *     @OA\Property(
 *         property="cargo_details",
 *         type="object",
 *         example={"weight": "50kg", "dimensions": "100x50x75cm"}
 *     ),
 *     @OA\Property(property="pickup_time", type="string", format="date-time"),
 *     @OA\Property(property="delivery_time", type="string", format="date-time")
 * )
 * 
 * @OA\Schema(
 *     schema="Order",
 *     allOf={
 *         @OA\Schema(ref="#/components/schemas/OrderRequest"),
 *         @OA\Schema(
 *             @OA\Property(property="id", type="integer", format="int64", example=1),
 *             @OA\Property(property="user_id", type="integer", format="int64", example=1),
 *             @OA\Property(
 *                 property="status",
 *                 type="string",
 *                 enum={"pending", "in_progress", "completed", "cancelled"},
 *                 example="pending"
 *             ),
 *             @OA\Property(property="created_at", type="string", format="date-time"),
 *             @OA\Property(property="updated_at", type="string", format="date-time"),
 *             @OA\Property(property="deleted_at", type="string", format="date-time", nullable=true)
 *         )
 *     }
 * )
 * 
 * @OA\Schema(
 *     schema="OrderCollection",
 *     @OA\Property(
 *         property="data",
 *         type="array",
 *         @OA\Items(ref="#/components/schemas/Order")
 *     ),
 *     @OA\Property(
 *         property="meta",
 *         type="object",
 *         @OA\Property(property="current_page", type="integer"),
 *         @OA\Property(property="from", type="integer"),
 *         @OA\Property(property="last_page", type="integer"),
 *         @OA\Property(property="per_page", type="integer"),
 *         @OA\Property(property="total", type="integer")
 *     )
 * )
 * 
 * @OA\Schema(
 *     schema="CreateOrderRequest",
 *     required={"pickup_location", "delivery_location", "cargo_details", "pickup_time", "delivery_time"},
 *     @OA\Property(property="pickup_location", type="string", example="123 Pickup St"),
 *     @OA\Property(property="delivery_location", type="string", example="456 Delivery Ave"),
 *     @OA\Property(
 *         property="cargo_details",
 *         type="object",
 *         required={"weight", "dimensions"},
 *         @OA\Property(property="weight", type="number", format="float", example=100, minimum=1, maximum=5000),
 *         @OA\Property(
 *             property="dimensions",
 *             type="object",
 *             required={"length", "width", "height"},
 *             @OA\Property(property="length", type="number", format="float", example=10, minimum=1),
 *             @OA\Property(property="width", type="number", format="float", example=10, minimum=1),
 *             @OA\Property(property="height", type="number", format="float", example=10, minimum=1)
 *         )
 *     ),
 *     @OA\Property(property="pickup_time", type="string", format="date-time", example="2023-10-01T10:00:00Z"),
 *     @OA\Property(property="delivery_time", type="string", format="date-time", example="2023-10-02T10:00:00Z")
 * )
 */
class OrderSchema {}