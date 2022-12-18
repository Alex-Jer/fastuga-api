<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class OrderPostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'payment_type' => 'required|string|in:VISA,PAYPAL,MBWAY', // VERIFICATION FOR PAYMENT SERVICE
            'payment_reference' => 'required|string',
            'points_used' => 'nullable|numeric|min:0',
            'cart' => 'required|array:id,quantity,note',
        ];
    }
}
