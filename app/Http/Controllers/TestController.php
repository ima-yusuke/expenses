<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Word;

class TestController extends Controller
{
    public function ShowTest()
    {
        $words = Word::with('japanese')->get();

        if ($words->count() < 4) {
            return redirect()->route('ShowIndex')->with('error', '単語が4つ以上必要です');
        }

        // ランダムに1つの単語を選択
        $correctWord = $words->random();

        // 正解の意味を取得（複数ある場合は最初の1つ）
        $correctMeaning = $correctWord->japanese->first()->japanese;

        // 不正解の選択肢を3つ取得
        $wrongWords = $words->where('id', '!=', $correctWord->id)->random(3);
        $wrongMeanings = $wrongWords->map(function($word) {
            return $word->japanese->first()->japanese;
        });

        // 選択肢をシャッフル
        $options = collect([$correctMeaning])->merge($wrongMeanings)->shuffle();

        return view('test', compact('correctWord', 'correctMeaning', 'options'));
    }

    public function CheckAnswer(Request $request)
    {
        $selectedAnswer = $request->input('answer');
        $correctAnswer = $request->input('correct_answer');
        $wordId = $request->input('word_id');

        $isCorrect = $selectedAnswer === $correctAnswer;

        $word = Word::with('japanese')->findOrFail($wordId);

        return view('test-result', compact('isCorrect', 'selectedAnswer', 'correctAnswer', 'word'));
    }
}
