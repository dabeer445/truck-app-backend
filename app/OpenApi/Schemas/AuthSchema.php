<?php

namespace App\OpenApi\Schemas;

/**
 * @OA\Schema(
 *     schema="LoginRequest",
 *     required={"email", "password"},
 *     @OA\Property(property="email", type="string", format="email", example="john@example.com"),
 *     @OA\Property(property="password", type="string", format="password", example="password123")
 * )
 * 
 * @OA\Schema(
 *     schema="AuthResponse",
 *     @OA\Property(
 *         property="user",
 *         ref="#/components/schemas/User"
 *     ),
 *     @OA\Property(
 *         property="token",
 *         type="string",
 *         example="1|laravel_sanctum_token..."
 *     )
 * )
 * 
 * @OA\Schema(
 *     schema="MessageResponse",
 *     @OA\Property(property="message", type="string", example="Operation successful")
 * )
 */
class AuthSchema {}