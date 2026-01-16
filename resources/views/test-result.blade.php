<x-template title="テスト結果">
    <div class="min-h-screen bg-gray-50">
        <section class="py-12 px-4 sm:px-6 lg:px-8">
            <div class="max-w-2xl mx-auto">
                <div class="bg-white border border-gray-200 rounded-lg p-8 text-center">
                    @if($isCorrect)
                        <div class="mb-6">
                            <div class="inline-flex items-center justify-center w-20 h-20 bg-green-100 rounded-full mb-4">
                                <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                            <h2 class="text-3xl font-bold text-green-600 mb-2">正解！</h2>
                        </div>
                    @else
                        <div class="mb-6">
                            <div class="inline-flex items-center justify-center w-20 h-20 bg-red-100 rounded-full mb-4">
                                <svg class="w-10 h-10 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </div>
                            <h2 class="text-3xl font-bold text-red-600 mb-2">不正解</h2>
                        </div>
                    @endif

                    <div class="mb-8 pb-8 border-b border-gray-200">
                        <p class="text-2xl font-bold text-gray-900 mb-4">{{$word->word}}</p>

                        <div class="space-y-2 mb-4">
                            <p class="text-sm text-gray-600">正解:</p>
                            <p class="text-lg font-semibold text-green-600">{{$correctAnswer}}</p>
                        </div>

                        @if(!$isCorrect)
                            <div class="space-y-2">
                                <p class="text-sm text-gray-600">あなたの回答:</p>
                                <p class="text-lg font-semibold text-red-600">{{$selectedAnswer}}</p>
                            </div>
                        @endif
                    </div>

                    <div class="mb-8 text-left">
                        <p class="text-sm font-medium text-gray-700 mb-2">この単語の意味:</p>
                        @foreach($word->japanese as $ja_word)
                            <p class="text-gray-700 mb-1">・{{$ja_word->japanese}}</p>
                        @endforeach

                        @if($word->en_example || $word->jp_example)
                            <div class="mt-4 bg-gray-50 rounded p-4 space-y-2 text-sm">
                                @if($word->en_example)
                                    <p class="text-gray-600 italic">{{$word->en_example}}</p>
                                @endif
                                @if($word->jp_example)
                                    <p class="text-gray-600">{{$word->jp_example}}</p>
                                @endif
                            </div>
                        @endif
                    </div>

                    <div class="flex gap-3 justify-center">
                        <a href="{{route('ShowTest')}}" class="bg-gray-900 hover:bg-gray-800 text-white px-6 py-3 rounded font-medium transition-colors">
                            次の問題へ
                        </a>
                        <a href="{{route('ShowIndex')}}" class="border border-gray-300 hover:border-gray-400 text-gray-700 px-6 py-3 rounded font-medium transition-colors">
                            単語帳に戻る
                        </a>
                    </div>
                </div>
            </div>
        </section>
    </div>
</x-template>
