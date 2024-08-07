<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class MainController extends Controller
{

    public function ShowIndex(){
        $products = Product::orderBy('date', 'asc')->get();
        $total = Product::selectRaw('SUM(price * quantity) as total')->value('total');
        $currentMonth = (new \DateTime())->format('n');
        return view("index",compact("products","total" , "currentMonth"));
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

    public function CheckProduct($id)
    {
        $product = Product::findOrFail($id); // 商品が見つからない場合は404エラー
        $product->flag = !$product->flag;
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
