<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    public $timestamps = false;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'order_id',
        'order_local_number',
        'product_id',
        'status',
        'price',
        'notes',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'preparation_by');
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function preparation()
    {
        return $this->belongsTo(User::class, 'preparation_by');
    }

    public static function withOrder()
    {
        return OrderItem::join('orders', 'orders.id', 'order_items.order_id');
    }
}
