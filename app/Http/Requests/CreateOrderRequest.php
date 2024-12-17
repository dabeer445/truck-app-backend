<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateOrderRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'pickup_location' => 'required|string|max:255',
            'delivery_location' => 'required|string|max:255',
            'cargo_details' => 'required|array',
            'cargo_details.weight' => 'required|numeric|min:1|max:5000',
            'cargo_details.dimensions' => 'array',
            'cargo_details.dimensions.length' => 'numeric|min:1',
            'cargo_details.dimensions.width' => 'numeric|min:1',
            'cargo_details.dimensions.height' => 'numeric|min:1',
            'pickup_time' => 'required|date|after:now',
            'delivery_time' => 'required|date|after:pickup_time',
        ];
    }
}