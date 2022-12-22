<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $usr = ($this->customer_id ? $this->customer()->withTrashed()->get()[0]->user()->withTrashed()->get()[0] : null);
        return array_merge([
            'id' => $this->id,
            'ticket_number' => $this->ticket_number,
            'status' => $this->status,
            'customer_user_id' => ($this->customer_id ? $usr->id : null),
            'customer_name' => ($this->customer_id ? $usr->name : null),
            'customer_photo_url' => ($this->customer_id ? $usr->photo_url : null),
            'total_price' => $this->total_price,
            'total_paid' => $this->total_paid,
            'total_paid_with_points' => $this->total_paid_with_points,
            'points_gained' => $this->points_gained,
            'points_used_to_pay' => $this->points_used_to_pay,
            'date' => $this->date,
            'delivered_by' => ($this->delivered_by ? $this->delivered()->withTrashed()->get()[0]->name : null),
            'cancel_reason' => $this->cancelReason(),
            'all_dishes_ready' => $this->allDishesReady(),
        ], ['items' => OrderItemResource::collection($this->items)]);
    }
}
