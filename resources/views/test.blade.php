<x-template title="単語テスト">
    <div class="min-h-screen bg-gray-50">
        <section class="py-12 px-4 sm:px-6 lg:px-8">
            <div class="max-w-2xl mx-auto">
                <div class="mb-8">
                    <a href="{{route('ShowIndex')}}" class="text-sm text-gray-600 hover:text-gray-900">
                        ← 単語帳に戻る
                    </a>
                </div>

                <div class="bg-white border border-gray-200 rounded-lg p-8">
                    <h2 class="text-2xl font-semibold text-gray-900 mb-2 text-center">
                        この単語の意味は？
                    </h2>

                    <div class="my-8 text-center">
                        <p class="text-4xl font-bold text-gray-900">{{$correctWord->word}}</p>
                    </div>

                    @if($correctWord->en_example)
                        <div class="mb-8 bg-gray-50 rounded p-4">
                            <p class="text-sm text-gray-600 mb-1">例文:</p>
                            <p class="text-gray-700 italic">{{$correctWord->en_example}}</p>
                        </div>
                    @endif

                    <form method="post" action="{{route('CheckAnswer')}}" class="space-y-3 flex flex-col gap-4">
                        @csrf
                        <input type="hidden" name="correct_answer" value="{{$correctMeaning}}">
                        <input type="hidden" name="word_id" value="{{$correctWord->id}}">

                        @foreach($options as $option)
                            <button type="submit" name="answer" value="{{$option}}"
                                class="w-full text-left border border-gray-300 hover:border-gray-900 hover:bg-gray-50 rounded-lg p-4 transition-colors">
                                <span class="text-gray-900">{{$option}}</span>
                            </button>
                        @endforeach
                    </form>
                </div>
            </div>
        </section>
    </div>
</x-template>
