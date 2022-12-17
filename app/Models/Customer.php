<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory, SoftDeletes;

    protected $dates = ['updated_at', 'created_at', 'deleted_at'];
    //protected $with = ['user'];

    protected $fillable = [
        'user_id',
        'phone',
        'nif',
        'default_payment_type',
        'default_payment_reference',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
