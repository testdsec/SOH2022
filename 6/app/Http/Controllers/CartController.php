<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use Illuminate\Http\Request;

class CartController extends Controller {

    private $cart;

    public function __construct() {
        $this->cart = Cart::getCart();
    }


    public function index() {
        $products = $this->cart->products;
        $amount = $this->cart->getAmount();
        return view('cart.index', compact('products', 'amount'));
    }


    public function checkout(Request $request) {
        $profile = null;
        $profiles = null;
        if (auth()->check()) {
            $user = auth()->user();
            $profiles = $user->profiles;
            $prof_id = (int)$request->input('profile_id');
            if ($prof_id) {
                $profile = $user->profiles()->whereIdAndUserId($prof_id, $user->id)->first();
            }
        }
        return view('cart.checkout', compact('profiles', 'profile'));
    }


     public function saveOrder(Request $request) {
        $this->validate($request, [
            'name' => 'required|max:100',
            'email' => 'required|email|max:40',
            'phone' => 'required|max:255',
            'address' => 'required|max:255',
            'delivery_time' => 'required|date_format:Y-m-d|after:today',
        ]);
        $user_id = auth()->check() ? auth()->user()->id : null;
        $order = Order::create(
            $request->all() + ['amount' => $this->cart->getAmount(), 'user_id' => $user_id]
        );

        foreach ($this->cart->products as $product) {
            $order->items()->create([
                'product_id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'quantity' => $product->pivot->quantity,
                'cost' => $product->price * $product->pivot->quantity,
            ]);
        }
        $this->cart->clear();
        return redirect()
            ->route('order_finish')
            ->with('order_id', $order->id);
    }


//   you cant add product if total_quantity=100
    public function add(Request $request, $id) {
        $total_quantity = $this->cart->getCount();
        $quantity = $request->input('quantity') ?? 1;
        if ($total_quantity<100){
            $this->cart->increase($id, $quantity);
            if ( ! $request->ajax()) {
                return back();
            }
            $positions = $this->cart->products()->count();
            return view('cart.part.cart', compact('positions'));
        }

    }


    public function add_item($id) {
//        $total_quantity = $this->basket->getQuantity();
//        if ($total_quantity<100){
            $this->basket->increase($id);

        return redirect()->route('cart.index');
    }


    public function reduce($id) {
        $this->cart->decrease($id);
        return redirect()->route('cart.index');
    }


    public function remove($id) {
        $this->cart->remove($id);
        return redirect()->route('cart.index');
    }


    public function clear() {
        $this->cart->delete();
        return redirect()->route('cart.index');
    }
}
