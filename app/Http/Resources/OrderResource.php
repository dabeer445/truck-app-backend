<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'pickup_location' => $this->pickup_location,
            'delivery_location' => $this->delivery_location,
            'cargo_details' => $this->cargo_details,
            'pickup_time' => $this->pickup_time,
            'delivery_time' => $this->delivery_time,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}