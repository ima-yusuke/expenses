<x-template title="家計簿">
    <section class="flex flex-col justify-center items-center gap-6 py-6">
        <div class="flex flex-col items-center gap-1">
            <p class="text-lg">{{$currentMonth}}月の合計額</p>
            <h1 class="text-3xl pb-2 border-b border-solid border-black">¥{{number_format($total)}}</h1>
        </div>
        <div class="w-full flex justify-center">
            <form method="post" action="{{route('AddProduct')}}" class="flex flex-col items-center gap-4 w-[90%]">
                @csrf
                <div class="flex flex-col gap-1 w-full">
                    <label for="p_name" class="block text-gray-700 text-xl">【商品名】</label>
                    <input type="text" id="p_name" name="p_name" placeholder="商品名" class="w-full rounded-md">
                </div>

                <div class="flex flex-col gap-1 w-full">
                    <label for="price" class="block text-gray-700 text-xl">【価格】</label>
                    <input type="text" id="price" name="price" placeholder="価格" class="w-full rounded-md">
                </div>

                <div class="flex flex-col gap-1 w-full">
                    <label for="quantity" class="block text-gray-700 text-xl">【数量】</label>
                    <input type="number" id="quantity" name="quantity" placeholder="数量" class="w-full rounded-md">
                </div>

                <div class="flex flex-col gap-1 w-full">
                    <label for="date" class="block text-gray-700 text-xl">【購入日】</label>
                    <input type="date" id="date" name="date" class="w-full rounded-md">
                </div>
                <button type="submit" class="border border-solid border-black px-4 py-2 rounded-lg shadow-xl w-[100px]">登録</button>
            </form>
        </div>

        <div class="w-full flex justify-center">
            <table class="w-[95%] custom-table">
                <thead>
                    <tr>
                        <th class="text-lg">商品名</th>
                        <th class="text-lg">合計</th>
                        <th class="text-lg">購入日</th>
                        <th class="text-lg">確認</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($products as $product)
                    <tr class="py-4">
                        <td class="text-center text-lg pb-4">{{ $product->name }}</td>
                        <td class="text-center text-lg pb-4">¥{{ number_format($product->price * $product->quantity) }}</td>
                        <td class="text-center text-lg pb-4">{{ \Carbon\Carbon::parse($product->date)->format('n/j') }}</td>
                        <td class="text-center text-lg pb-4">
                            <form action="{{ route('CheckProduct', ['id' => $product->id]) }}" method="POST" onsubmit="return confirm('【{{ $product->name }}】を確認しましたか？');">
                                @csrf
                                @if($product->flag===1)
                                    <span class="text-white bg-blue-500 py-2 px-2 rounded-lg">済</span>
                                @else
                                    <button type="submit" class="text-white bg-red-500 py-1 px-2 rounded-lg">未</button>
                                @endif
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </section>

</x-template>
