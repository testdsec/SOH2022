<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Order extends Model {
    protected $fillable = [
        'user_id',
        'name',
        'email',
        'phone',
        'address',
        'delivery_time',
        'comment',
        'amount',
        'status',
        'is_paid',
    ];

    public const STATUSES = [
        0 => 'new',
        1 => 'processing',
        2 => 'delivered',
        3 => 'ended',
    ];

    public function items() {
        return $this->hasMany(OrderItem::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }
}
