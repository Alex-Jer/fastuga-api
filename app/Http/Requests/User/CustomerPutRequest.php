<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class CustomerPutRequest extends FormRequest
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
            'name' => 'required|string',
            'photo' => 'nullable|image|max:8192',
            'remove_photo' => 'nullable|boolean',
            'phone' => 'required|string|size:9',
            'nif' => 'nullable|string|size:9',
            'default_payment_type' => 'nullable|in:VISA,PAYPAL,MBWAY',
            'default_payment_reference' => 'nullable|string|max:255',
        ];
    }
}
