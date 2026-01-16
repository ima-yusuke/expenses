<x-template title="単語帳">
    <div class="min-h-screen bg-gray-50">
        <section class="py-12 px-4 sm:px-6 lg:px-8">
            {{--新規登録(権限のあるユーザーでログインした場合のみ）--}}
            @hasanyrole('membership')
                <div class="max-w-3xl mx-auto mb-12">
                    <div class="bg-white border border-gray-200 rounded-lg p-8">
                        <h2 class="text-2xl font-semibold text-gray-900 mb-6">
                            新しい単語を追加
                        </h2>

                        <form method="post" action="{{route('AddWord')}}" class="space-y-5" id="add_form">
                            @csrf
                            <div>
                                <label for="word" class="block text-sm font-medium text-gray-700 mb-1">
                                    英単語
                                </label>
                                <input type="text" id="word" name="word" placeholder="例: serendipity"
                                    class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:border-gray-900 focus:ring-1 focus:ring-gray-900">
                            </div>

                            <div>
                                <label for="jp_word_1" class="block text-sm font-medium text-gray-700 mb-1">
                                    意味 1
                                </label>
                                <input type="text" id="jp_word_1" name="meaningArray[]" placeholder="例: 偶然の幸運な発見"
                                    class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:border-gray-900 focus:ring-1 focus:ring-gray-900">
                            </div>

                            <button type="button" id="add_meaning"
                                class="text-sm text-gray-700 hover:text-gray-900 font-medium">
                                + 意味を追加
                            </button>

                            <div>
                                <label for="en_example" class="block text-sm font-medium text-gray-700 mb-1">
                                    例文（英語）
                                </label>
                                <textarea id="en_example" name="en_example" placeholder="例: It was pure serendipity that we met at the cafe."
                                    class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:border-gray-900 focus:ring-1 focus:ring-gray-900 resize-none" rows="2"></textarea>
                            </div>

                            <div>
                                <label for="jp_example" class="block text-sm font-medium text-gray-700 mb-1">
                                    例文（日本語）
                                </label>
                                <textarea id="jp_example" name="jp_example" placeholder="例: カフェで会ったのは純粋な偶然だった。"
                                    class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:border-gray-900 focus:ring-1 focus:ring-gray-900 resize-none" rows="2"></textarea>
                            </div>

                            <button type="submit"
                                class="w-full bg-gray-900 hover:bg-gray-800 text-white px-4 py-2.5 rounded font-medium transition-colors">
                                登録する
                            </button>
                        </form>
                    </div>
                </div>
            @endhasanyrole

            {{--単語一覧--}}
            <div class="max-w-3xl mx-auto">
                <div class="mb-8">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-xl font-semibold text-gray-900">単語一覧</h2>
                        <div class="flex gap-2">
                            <a href="{{route('ShowReplyAssistant')}}" class="bg-white hover:bg-gray-50 border border-gray-300 text-gray-700 px-4 py-2 rounded font-medium transition-colors">
                                返信アシスタント
                            </a>
                            <a href="{{route('ShowTest')}}" class="bg-gray-900 hover:bg-gray-800 text-white px-4 py-2 rounded font-medium transition-colors">
                                単語テスト
                            </a>
                        </div>
                    </div>

                    <form method="get" action="{{route('ShowIndex')}}" class="mb-6">
                        <div class="flex gap-2">
                            <input type="text" name="search" value="{{request('search')}}" placeholder="単語または意味を検索..."
                                class="flex-1 border border-gray-300 rounded px-4 py-2 focus:outline-none focus:border-gray-900 focus:ring-1 focus:ring-gray-900">
                            <button type="submit" class="bg-gray-900 hover:bg-gray-800 text-white px-6 py-2 rounded font-medium transition-colors">
                                検索
                            </button>
                            @if(request('search'))
                                <a href="{{route('ShowIndex')}}" class="border border-gray-300 hover:border-gray-400 text-gray-700 px-4 py-2 rounded font-medium transition-colors">
                                    クリア
                                </a>
                            @endif
                        </div>
                    </form>
                    <p class="text-sm text-gray-600">
                        @if(request('search'))
                            検索結果: {{count($words)}}件 / 総単語数: {{$totalCount}}件
                        @else
                            総単語数: {{count($words)}}件
                        @endif
                    </p>
                </div>

                <div class="space-y-4">
                    @foreach($words as $word)
                        <div class="bg-white border border-gray-200 rounded-lg p-6 hover:border-gray-300 transition-colors">
                            <div class="flex items-start justify-between mb-3">
                                <h2 class="text-xl font-semibold text-gray-900">
                                    {{$word["word"]}}
                                </h2>
                                @hasanyrole('membership')
                                    <form method="post" action="{{route('DeleteWord')}}" onsubmit="return confirmDelete()">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" name="id" value="{{$word["id"]}}">
                                        <button type="submit"
                                            class="text-gray-400 hover:text-red-600 transition-colors">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                        </button>
                                    </form>
                                @endhasanyrole
                            </div>

                            <div class="border-t border-gray-100 pt-3 mb-4">
                                @foreach($word->japanese as $ja_words)
                                    <p class="text-gray-700 mb-1">・{{$ja_words["japanese"]}}</p>
                                @endforeach
                            </div>

                            @if($word["en_example"] || $word["jp_example"])
                                <div class="bg-gray-50 rounded p-4 space-y-2 text-sm">
                                    @if($word["en_example"])
                                        <p class="text-gray-600 italic">{{$word["en_example"]}}</p>
                                    @endif
                                    @if($word["jp_example"])
                                        <p class="text-gray-600">{{$word["jp_example"]}}</p>
                                    @endif
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    </div>

<script>
    const ADD_MEANING_BTN = document.getElementById('add_meaning');
    const form = document.getElementById('add_form');
    let count = 2;

    ADD_MEANING_BTN.addEventListener('click', () => {
        const div = document.createElement('div');
        div.innerHTML = `
            <label for="jp_word_${count}" class="block text-sm font-medium text-gray-700 mb-1">
                意味 ${count}
            </label>
            <input type="text" id="jp_word_${count}" name="meaningArray[]" placeholder="意味を入力"
                class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:border-gray-900 focus:ring-1 focus:ring-gray-900">
        `;
        form.insertBefore(div, ADD_MEANING_BTN);
        count++;
    });

    function confirmDelete() {
        return confirm('本当にこの単語を削除しますか？');
    }
</script>
</x-template>
