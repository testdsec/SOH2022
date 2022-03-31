<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Cookie;

class Cart extends Model {


    public function products() {
        return $this->belongsToMany(Product::class)->withPivot('quantity');
    }

    public function increase($id) {
        $this->change($id, 1);
    }

    public function decrease($id) {
        $this->change($id, -1 );
    }


    private function change($id, $count = 1) {
        if ($count == 0) {
            return;
        }
        $id = (int)$id;
        if ($this->products->contains($id)) {
            $pivotRow = $this->products()->where('product_id', $id)->first()->pivot;
            $quantity = $pivotRow->quantity + $count;
            if ($quantity > 0) {
                $pivotRow->update(['quantity' => $quantity]);
            } else {
                $pivotRow->delete();
            }
        } elseif ($count > 0) {
            $this->products()->attach($id, ['quantity' => $count]);
        }

        $this->touch();
    }

    public function remove($id) {
        $this->products()->detach($id);
        $this->touch();
    }


    public function clear() {
        $this->products()->detach();
        $this->touch();
    }


    public function getAmount() {
        $amount = 0.0;
        foreach ($this->products as $product) {
            $amount = $amount + $product->price * $product->pivot->quantity;
        }
        return $amount;
    }

    public static function getCart() {
        $cart_id = (int)request()->cookie('basket_id');
        if (!empty($cart_id)) {
            try {
                $cart = Cart::findOrFail($cart_id);
            } catch (ModelNotFoundException $e) {
                $cart = Cart::create();
            }
        } else {
            $cart = Cart::create();
        }
        Cookie::queue('cart_id', $cart->id, 525600);
        return $cart;
    }

    // because of some problem with our servers and delivery service total order quantity shouldn't be more than 100
    public function getQuantity() {
        $amount = 0;
        foreach ($this->products as $product) {
            $amount = $amount +  $product->pivot->quantity;
        }
        return $amount;
    }
}
