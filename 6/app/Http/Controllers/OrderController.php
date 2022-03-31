<?php

namespace App\Http\Controllers;

use App\Models\Order;

class OrderController extends Controller {

    public function index() {
        $orders = Order::whereUserId(auth()->user()->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        $statuses = Order::STATUSES;
        return view('user.order.index', compact('orders', 'statuses'));
    }

    // show client's orders
    public function show(Order $order) {
        $statuses = Order::STATUSES;
        return view('user.order.show', compact('order', 'statuses'));
    }



    public function update(Request $request) {
        $id = $request->input('id');
        $order = Order::find($id);
        if (auth()->user()->id !== $order->user_id) {
            abort(404);
        } else {
            $data =request()->except(['_token']);
            $order->forceFill($data)->save();
        }
        return redirect('/orders');
    }


    public function finish(Request $request) {
        if ($request->session()->exists('order_id')) {
            $order_id = $request->session()->pull('order_id');
            $order = Order::findOrFail($order_id);
            $amount=$order->amount;
//            create kind of invoice
            $content="Order id: ".$order_id."\nTotal amount: ".$amount;
            Storage::disk('invoices')->put($order_id,$content);
            return view('order.success', compact('order'));
        } else {
            return redirect()->route('cart.index');
        }

    }

    public function download_invoice(Request $request){
        $order = $request->post('order');
        $bill_path = storage_path('app/invoices/'.$order);
        return response()->download($bill_path);
    }




}
