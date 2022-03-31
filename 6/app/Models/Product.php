<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Stem\LinguaStemRu;

class Product extends Model {

    protected $fillable = [
        'category_id',
        'brand_id',
        'name',
        'content',
        'price',
    ];

//    user shouldn't see product which status are 0
    public const STATUSES = [
        0 => 'preparing',
        1 => 'stock',
        2 => 'ended',
    ];

    public function cart() {
        return $this->belongsToMany(Cart::class)->withPivot('quantity');
    }

}
