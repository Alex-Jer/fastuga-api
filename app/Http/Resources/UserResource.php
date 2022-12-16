<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return array_merge([
            "user_id" => $this->id,
            "name" => $this->name,
            "email" => $this->email,
            "type" => $this->type,
            "photo_url" => $this->photo_url,
            "blocked" => $this->blocked
            //"email_verified_at" => $this->email_verified_at,
            /*"created_at" => $this->created_at,
            "updated_at" => $this->updated_at,*/
        ], ($this->customer ? ["customer" => new CustomerResource($this->customer)] : []));
    }
}
