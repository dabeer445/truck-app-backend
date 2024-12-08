<?php

namespace App\OpenApi\Schemas;

/**
 * @OA\Schema(
 *     schema="NotificationResource",
 *     type="object",
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="type", type="string"),
 *     @OA\Property(property="data", type="object"),
 *     @OA\Property(property="read_at", type="string", format="date-time"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */


class NotificationSchema {}