<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Word;
use App\Models\Japanese;

class MainController extends Controller
{

    public function ShowIndex(){
        $words = Word::with('japanese')->get();
        return view("index",compact('words'));
    }

    public function AddWord(Request $request){

        // フォームから送信された意味を配列として取得
        $meanings = $request->input('meaningArray');

        // データベースにデータを保存する
        $word = new Word();
        $word->word = $request->word;
        $word->en_example = $request->en_example;
        $word->jp_example = $request->jp_example;
        $word->save();

        // Japaneseの保存
        for ($i = 0; $i < count($meanings); $i++) {
            $japanese = new Japanese();
            $japanese->word_id = $word->id;
            $japanese->japanese = $meanings[$i];
            $japanese->save();
        }


        return redirect()->back();
    }

    public function DeleteWord(Request $request)
    {
        $id = $request->id;
        $word = Word::findOrFail($id); // 単語が見つからない場合は404エラー
        $word->delete();
        return redirect()->back();
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
