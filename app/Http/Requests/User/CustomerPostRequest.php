<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class CustomerPostRequest extends FormRequest
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
            'email' => 'required|email|unique:users',
            'password' => 'required|string|confirmed|between:6,128',
            'photo' => 'nullable|image|max:8192',
            'phone' => 'required|string|size:9',
            'nif' => 'nullable|string|size:9',
            'default_payment_type' => 'nullable|in:VISA,PAYPAL,MBWAY|required_with:default_payment_reference',
            'default_payment_reference' => 'nullable|string|max:255|required_with:default_payment_type',
        ];
    }
}
