<x-template title="単語帳">
    <section class="py-16">
        {{--新規登録(権限のあるユーザーでログインした場合のみ）--}}
        @hasanyrole('membership')
            <div class="w-full flex justify-center">
            <form method="post" action="{{route('AddWord')}}" class="flex flex-col items-center gap-4 w-[90%]" id="add_form">
                @csrf
                <div class="flex flex-col gap-1 w-full">
                    <label for="word" class="block text-gray-700 text-xl">【英単語】</label>
                    <input type="text" id="word" name="word" placeholder="英単語" class="w-full rounded-md">
                </div>

                <div class="flex flex-col gap-1 w-full">
                    <label for="jp_word_1" class="block text-gray-700 text-xl">
                        【意味<span class="inline-flex items-center justify-center rounded-full border border-solid border-black w-6 h-6 text-center">1</span>】
                    </label>
                    <input type="text" id="jp_word_1" name="meaningArray[]" placeholder="意味" class="w-full rounded-md">
                </div>
                <div id="add_meaning" class="border border-solid border-black px-4 py-2 rounded-lg shadow-xl w-[150px] text-center">意味を追加</div>

                <div class="flex flex-col gap-1 w-full">
                    <label for="en_example" class="block text-gray-700 text-xl">【例文：英語】</label>
                    <input type="text" id="en_example" name="en_example" placeholder="Example" class="w-full rounded-md">
                </div>

                <div class="flex flex-col gap-1 w-full">
                    <label for="jp_example" class="block text-gray-700 text-xl">【例文：日本語】</label>
                    <input type="text" id="jp_example" name="jp_example" placeholder="例文" class="w-full rounded-md">
                </div>

                <button type="submit" class="border border-solid border-black px-4 py-2 rounded-lg shadow-xl w-[150px]">登録</button>
            </form>
        </div>
        @endhasanyrole

        {{--単語一覧--}}
        <div class="w-full flex flex-col justify-center items-center gap-4">
            <h1>単語数：{{count($words)}}</h1>
            @foreach($words as $word)
                <div class="relative flex flex-col items-start justify-center rounded-lg p-4 w-[90%] shadow-xl border border-solid border-gray-100">
                    <h1 class="text-xl font-bold pb-2">{{$word["word"]}}</h1>
                    <p class="border-b-2 border-solid border-b-blue-200 w-full"></p>
                    <aside class="flex flex-col items-start pt-2">
                        @foreach($word->japanese as $ja_words)
                            <p class="font-bold">・{{$ja_words["japanese"]}}</p>
                        @endforeach
                    </aside>
                    <aside class="flex flex-col items-start pt-2">
                        <p>{{$word["en_example"]}}</p>
                        <p>{{$word["jp_example"]}}</p>
                    </aside>
                    @hasanyrole('membership')
                        <form method="post" action="{{route('DeleteWord')}}" onsubmit="return confirmDelete()">
                            @csrf
                            @method('DELETE')
                            <input type="hidden" name="id" value="{{$word["id"]}}">
                            <button type="submit" class="text-white bg-red-500 h-[25px] w-[25px] rounded-full absolute right-0 -top-2">✗</button>
                        </form>
                    @endhasanyrole
                </div>
            @endforeach

        </div>
    </section>
<script>
    const ADD_MEANING_BTN = document.getElementById('add_meaning');
    const form = document.getElementById('add_form');
    let count = 2;

    ADD_MEANING_BTN.addEventListener('click', () => {
        const div = document.createElement('div');
        div.classList.add('flex', 'flex-col', 'gap-1', 'w-full');
        div.innerHTML = `
            <label for="jp_word_${count}" class="block text-gray-700 text-xl">
                【意味<span class="inline-flex items-center justify-center rounded-full border border-solid border-black w-6 h-6 text-center">${count}</span>】
            </label>
            <input type="text" id="jp_word_${count}" name="meaningArray[]" placeholder="意味" class="w-full rounded-md">
        `;
        form.insertBefore(div, ADD_MEANING_BTN);
        count++;
    });

    function confirmDelete() {
        return confirm('本当に削除しますか？');
    }
</script>
</x-template>
