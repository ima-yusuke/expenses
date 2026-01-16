<x-template title="返信文の提案">
    <div class="min-h-screen bg-gray-50">
        <section class="py-12 px-4 sm:px-6 lg:px-8">
            <div class="max-w-3xl mx-auto">
                <div class="mb-8">
                    <a href="{{route('ShowReplyAssistant')}}" class="text-sm text-gray-600 hover:text-gray-900">
                        ← 返信アシスタントに戻る
                    </a>
                </div>

                <div class="space-y-6">
                    <!-- 友達のメッセージ -->
                    <div class="bg-white border border-gray-200 rounded-lg p-6">
                        <h3 class="text-sm font-medium text-gray-700 mb-2">友達からのメッセージ</h3>
                        <p class="text-gray-900">{{$friendMessage}}</p>
                    </div>

                    <!-- あなたの返信意図 -->
                    <div class="bg-white border border-gray-200 rounded-lg p-6">
                        <h3 class="text-sm font-medium text-gray-700 mb-2">あなたの返信意図</h3>
                        <p class="text-gray-900">{{$replyIntent}}</p>
                    </div>

                    <!-- AI生成の返信文 -->
                    <div class="bg-white border border-gray-200 rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">提案された返信文</h3>
                        <div class="prose max-w-none">
                            <div class="whitespace-pre-wrap text-gray-900">{{$generatedText}}</div>
                        </div>
                    </div>

                    <!-- 使用された単語 -->
                    @if(count($usedWords) > 0)
                        <div class="bg-white border border-gray-200 rounded-lg p-6">
                            <h3 class="text-sm font-medium text-gray-700 mb-4">使用された単語帳の単語</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach($usedWords as $word)
                                    <div class="border border-gray-200 rounded p-4">
                                        <p class="font-semibold text-gray-900 mb-1">{{$word->word}}</p>
                                        @foreach($word->japanese as $ja_word)
                                            <p class="text-sm text-gray-600">・{{$ja_word->japanese}}</p>
                                        @endforeach
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- アクションボタン -->
                    <div class="flex gap-3">
                        <a href="{{route('ShowReplyAssistant')}}" class="flex-1 text-center bg-gray-900 hover:bg-gray-800 text-white px-4 py-3 rounded font-medium transition-colors">
                            別の返信を作成
                        </a>
                        <a href="{{route('ShowIndex')}}" class="flex-1 text-center border border-gray-300 hover:border-gray-400 text-gray-700 px-4 py-3 rounded font-medium transition-colors">
                            単語帳に戻る
                        </a>
                    </div>

                    <!-- コピー機能 -->
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                        <p class="text-sm text-gray-600 mb-3">返信文をコピーして使いましょう！</p>
                        <button onclick="copyReply()" class="w-full bg-white hover:bg-gray-50 border border-gray-300 text-gray-700 px-4 py-2 rounded font-medium transition-colors">
                            返信文をコピー
                        </button>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <script>
        function copyReply() {
            const text = `{{$generatedText}}`;
            navigator.clipboard.writeText(text).then(() => {
                alert('返信文をコピーしました！');
            }).catch(err => {
                console.error('コピーに失敗しました:', err);
            });
        }
    </script>
</x-template>
