<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Word;

class TestController extends Controller
{
    public function ShowTest()
    {
        $words = Word::with('japanese')->get();

        if ($words->count() < 1) {
            return redirect()->route('ShowIndex')->with('error', '単語が1つ以上必要です');
        }

        // ランダムに1つの単語を選択
        $correctWord = $words->random();

        // 正解の意味を取得（複数ある場合は最初の1つ）
        $correctMeaning = $correctWord->japanese->first()->japanese;

        // Gemini APIで似た意味の選択肢を生成
        $options = $this->generateSimilarOptions($correctWord->word, $correctMeaning);

        // APIエラーの場合は従来の方法にフォールバック
        if (empty($options)) {
            if ($words->count() < 4) {
                return redirect()->route('ShowIndex')->with('error', '単語が4つ以上必要です');
            }

            $wrongWords = $words->where('id', '!=', $correctWord->id)->random(3);
            $wrongMeanings = $wrongWords->map(function($word) {
                return $word->japanese->first()->japanese;
            });
            $options = collect([$correctMeaning])->merge($wrongMeanings)->shuffle();
        }

        return view('test', compact('correctWord', 'correctMeaning', 'options'));
    }

    private function generateSimilarOptions($englishWord, $correctMeaning)
    {
        $apiKey = config('services.gemini.api_key');

        if (!$apiKey) {
            return [];
        }

        $prompt = "英単語「{$englishWord}」の日本語の意味は「{$correctMeaning}」です。

この単語の4択クイズを作成したいので、紛らわしい不正解の選択肢を3つ作成してください。

要件:
1. 正解の意味「{$correctMeaning}」に似ているが微妙に違う日本語の意味を3つ
2. 英語学習者が間違えやすい、似た意味の単語の意味を選ぶこと
3. 完全に無関係な意味は避けること
4. 各選択肢は15文字以内の簡潔な日本語で

以下の形式で出力してください（番号と改行のみ、他の説明は不要）:

1. (不正解の選択肢1)
2. (不正解の選択肢2)
3. (不正解の選択肢3)";

        try {
            $response = Http::timeout(15)->post(
                "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key={$apiKey}",
                [
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => $prompt]
                            ]
                        ]
                    ]
                ]
            );

            if ($response->successful()) {
                $result = $response->json();
                $generatedText = $result['candidates'][0]['content']['parts'][0]['text'] ?? '';

                // 選択肢を抽出
                $wrongOptions = [];
                if (preg_match_all('/^\d+\.\s*(.+)$/m', $generatedText, $matches)) {
                    $wrongOptions = array_slice($matches[1], 0, 3);
                }

                // 正解と不正解をシャッフル
                if (count($wrongOptions) === 3) {
                    return collect([$correctMeaning])->merge($wrongOptions)->shuffle();
                }
            }
        } catch (\Exception $e) {
            \Log::error('Gemini API error in test generation: ' . $e->getMessage());
        }

        return [];
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
