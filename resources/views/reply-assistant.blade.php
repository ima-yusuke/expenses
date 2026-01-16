<x-template title="返信アシスタント">
    <div class="min-h-screen bg-gray-50">
        <section class="py-12 px-4 sm:px-6 lg:px-8">
            <div class="max-w-2xl mx-auto">
                <div class="mb-8">
                    <a href="{{route('ShowIndex')}}" class="text-sm text-gray-600 hover:text-gray-900">
                        ← 単語帳に戻る
                    </a>
                </div>

                <div class="bg-white border border-gray-200 rounded-lg p-8">
                    <h2 class="text-2xl font-semibold text-gray-900 mb-2">
                        返信アシスタント
                    </h2>
                    <p class="text-sm text-gray-600 mb-8">
                        友達からのメッセージとあなたの返信意図を入力すると、単語帳の単語を使った返信文を提案します。
                    </p>

                    @if(session('error'))
                        <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form method="post" action="{{route('GenerateReply')}}" class="space-y-6" id="replyForm" onsubmit="showLoading()">
                        @csrf

                        <div>
                            <label for="friend_message" class="block text-sm font-medium text-gray-700 mb-1">
                                友達からのメッセージ（英語）
                            </label>
                            <textarea id="friend_message" name="friend_message" required
                                placeholder="例: Hey! How have you been? I haven't seen you in a while."
                                class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:border-gray-900 focus:ring-1 focus:ring-gray-900 resize-none"
                                rows="4"></textarea>
                        </div>

                        <div>
                            <label for="reply_intent" class="block text-sm font-medium text-gray-700 mb-1">
                                返信したい内容（日本語でOK）
                            </label>
                            <textarea id="reply_intent" name="reply_intent" required
                                placeholder="例: 元気だよ！最近は仕事が忙しくて、なかなか会えなかったね。今度一緒にご飯でも行こう。"
                                class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:border-gray-900 focus:ring-1 focus:ring-gray-900 resize-none"
                                rows="4"></textarea>
                        </div>

                        <button type="submit" id="submitBtn"
                            class="w-full bg-gray-900 hover:bg-gray-800 text-white px-4 py-3 rounded font-medium transition-colors">
                            返信文を生成
                        </button>

                        <!-- ローディング表示 -->
                        <div id="loadingIndicator" class="hidden">
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 text-center">
                                <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-gray-900 mb-3"></div>
                                <p class="text-gray-900 font-medium mb-1">返信文を生成中...</p>
                                <p class="text-sm text-gray-600">少々お待ちください（10-30秒程度）</p>
                            </div>
                        </div>
                    </form>

                    <div class="mt-8 pt-8 border-t border-gray-200">
                        <h3 class="text-sm font-medium text-gray-700 mb-2">使い方</h3>
                        <ol class="text-sm text-gray-600 space-y-1 list-decimal list-inside">
                            <li>友達からの英語メッセージを入力</li>
                            <li>あなたが返信したい内容を日本語で入力</li>
                            <li>AIが単語帳の単語を使った自然な英語の返信文を提案</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <script>
        function showLoading() {
            // ボタンを無効化
            document.getElementById('submitBtn').disabled = true;
            document.getElementById('submitBtn').classList.add('opacity-50', 'cursor-not-allowed');

            // ローディング表示
            document.getElementById('loadingIndicator').classList.remove('hidden');

            // フォーム送信を続行
            return true;
        }
    </script>
</x-template>
