<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class MainController extends Controller
{

    public function ShowIndex(){
        $products = Product::all();
        return view("index",compact("products"));
    }

    public function AddProduct(Request $request){
        // データベースにデータを保存する
        $product = new Product();
        $product->name = $request->p_name;
        $product->price = $request->price;
        $product->quantity = $request->quantity;
        $product->date = $request->date;
        $product->save();

        return redirect()->back();
    }

    public function DeleteProduct($id)
    {
        $product = Product::findOrFail($id); // 商品が見つからない場合は404エラー
        $product->delete();
        return redirect()->back();
    }
}
