<x-template title="家計簿">
    <div>
        <form method="post" action="{{route('AddProduct')}}">
            @csrf
            <input type="text" name="p_name" placeholder="商品名">
            <input type="text" name="price" placeholder="価格">
            <input type="number" name="quantity" placeholder="数量">
            <input type="date" name="date" placeholder="購入日">
            <button type="submit">登録</button>
        </form>
    </div>

    <div>
        <table>
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
                    <td>{{$product->name}}</td>
                    <td>{{number_format($product->price, 0)}}</td>
                    <td>{{$product->quantity}}</td>
                    <td>{{number_format($product->price*$product->quantity)}}</td>
                    <td>{{\Carbon\Carbon::parse($product->date)->format('n/d')}}</td>
                    <td>
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
</x-template>
