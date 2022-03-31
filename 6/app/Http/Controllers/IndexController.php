<?php

namespace App\Http\Controllers;

class IndexController extends Controller
{
    public function index(Request $request) {
        if ($request->has('status')) {
            $status = $request->input('status');
            $items = Product::where('status', $status);
        } else {
            $items = Product::where('status', '>', '0');
        }

        return view('shop.index', [
            'products' => $items,
        ]);
    }
}