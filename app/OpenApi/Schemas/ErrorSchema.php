<?php

namespace App\OpenApi\Schemas;

/**
 * @OA\Schema(
 *     schema="Error",
 *     required={"message"},
 *     @OA\Property(
 *         property="message",
 *         type="string",
 *         example="Error message"
 *     )
 * )
 *
 *
 * @OA\Schema(
 *     schema="ValidationError",
 *     type="object",
 *     @OA\Property(property="message", type="string"),
 *     @OA\Property(property="errors", type="object")
 * )
 *
 *
 * @OA\Schema(
 *     schema="NotFoundError",
 *     type="object",
 *     @OA\Property(property="message", type="string", example="Resource not found")
 * )
 */

 class ErrorSchema {}
