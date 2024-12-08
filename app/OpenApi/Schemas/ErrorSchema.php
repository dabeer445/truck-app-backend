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
 */
class ErrorSchema {}
