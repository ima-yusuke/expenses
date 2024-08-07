<x-template title="家計簿">
    <section class="flex flex-col justify-center items-center gap-4 py-6">
        <h1 class="text-xl">今井家 家計簿</h1>
        <div class="w-full flex justify-center">
            <form method="post" action="{{route('AddProduct')}}" class="flex flex-col items-center gap-2 w-[90%]">
                @csrf
                <input type="text" name="p_name" placeholder="商品名" class="w-full rounded-md">
                <input type="text" name="price" placeholder="価格" class="w-full rounded-md">
                <input type="number" name="quantity" placeholder="数量" class="w-full rounded-md">
                <input type="date" name="date" placeholder="購入日" class="w-full rounded-md">
                <button type="submit" class="border border-solid border-black px-4 py-2 rounded-lg shadow-xl w-[100px]">登録</button>
            </form>
        </div>

        <div class="w-full flex justify-center">
            <table class="w-[90%]">
                <tr>
                    <th>商品名</th>
                    <th>価格</th>
                    <th>数量</th>
                    <th>合計</th>
                    <th>購入日</th>
                    <th>削除</th>
                </tr>
                @foreach($products as $product)
                    <tr>
                        <td class="text-center">{{$product->name}}</td>
                        <td class="text-center">{{number_format($product->price, 0)}}</td>
                        <td class="text-center">{{$product->quantity}}</td>
                        <td class="text-center">{{number_format($product->price*$product->quantity)}}</td>
                        <td class="text-center">{{\Carbon\Carbon::parse($product->date)->format('n/j')}}</td>
                        <td class="text-center">
                            <form action="{{ route('DeleteProduct', ['id' => $product->id]) }}" method="POST" onsubmit="return confirm('本当に削除しますか？');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500">削除</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </table>
        </div>
    </section>

</x-template>
