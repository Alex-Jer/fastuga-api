<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MyCustomerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            //remove redunctant field
            /*"user_id" => $this->user_id,*/
            "customer_id" => $this->id,
            "phone" => $this->phone,
            "points" => $this->points,
            "nif" => $this->nif,
            "default_payment_type" => $this->default_payment_type,
            "default_payment_reference" => $this->default_payment_reference,
            /*"created_at" => $this->created_at,*/
        ];
    }
}
