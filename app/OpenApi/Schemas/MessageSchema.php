<?php

namespace App\OpenApi\Schemas;

/**
 * @OA\Schema(
 *     schema="MessageRequest",
 *     type="object",
 *     required={"order_id", "receiver_id", "content"},
 *     @OA\Property(property="order_id", type="integer"),
 *     @OA\Property(property="receiver_id", type="integer"),
 *     @OA\Property(property="content", type="string", maxLength=1000)
 * )
 *
 * @OA\Schema(
 *     schema="MessageResource",
 *     type="object",
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="order_id", type="integer"),
 *     @OA\Property(property="sender_id", type="integer"),
 *     @OA\Property(property="receiver_id", type="integer"),
 *     @OA\Property(property="content", type="string"),
 *     @OA\Property(property="is_read", type="boolean"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */

class MessageSchema {}