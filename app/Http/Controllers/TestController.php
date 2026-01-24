<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Word;

class TestController extends Controller
{
    public function ShowTestStart()
    {
        // セッションをクリア
        session()->forget('test_questions');
        session()->forget('test_total_count');

        return view('test-start');
    }

    public function StartTest(Request $request)
    {
        $count = $request->input('count', 10);

        // 問題を一括生成
        $questionQueue = $this->generateQuestionBatch($count);

        if (empty($questionQueue)) {
            return redirect()->route('ShowTest')->with('error', '問題の生成に失敗しました');
        }

        session([
            'test_questions' => $questionQueue,
            'test_total_count' => $count
        ]);

        return redirect()->route('ShowQuestion');
    }

    public function ShowQuestion()
    {
        // セッションから問題リストを取得
        $questionQueue = session('test_questions', []);

        if (empty($questionQueue)) {
            return redirect()->route('ShowTest');
        }

        // 最初の問題を取り出す
        $currentQuestion = array_shift($questionQueue);

        // 残りの問題をセッションに保存
        session(['test_questions' => $questionQueue]);

        // ビューに渡すデータを準備
        $correctWord = $currentQuestion['word'];
        $correctMeaning = $currentQuestion['correct_meaning'];
        $options = collect($currentQuestion['options']);
        $remainingQuestions = count($questionQueue);
        $totalCount = session('test_total_count', 10);
        $currentQuestionNumber = $totalCount - $remainingQuestions;

        return view('test', compact('correctWord', 'correctMeaning', 'options', 'remainingQuestions', 'currentQuestionNumber', 'totalCount'));
    }

    private function generateQuestionBatch($count = 10)
    {
        $words = Word::with('japanese')->get();

        if ($words->count() < 1) {
            return [];
        }

        // 最大指定問題数（または単語数が少ない場合はその数）
        $questionCount = min($count, $words->count());
        $selectedWords = $words->random($questionCount);

        // 1回のAPI呼び出しで全問題を生成
        $apiKey = config('services.gemini.api_key');
        $questions = [];

        if ($apiKey) {
            // プロンプト用に単語リストを作成
            $wordList = [];
            foreach ($selectedWords as $word) {
                $wordList[] = [
                    'english' => $word->word,
                    'japanese' => $word->japanese->first()->japanese
                ];
            }

            $wordListText = '';
            foreach ($wordList as $index => $item) {
                $wordListText .= ($index + 1) . ". {$item['english']} - {$item['japanese']}\n";
            }

            $prompt = "以下の英単語リストについて、それぞれ4択クイズの紛らわしい不正解選択肢を3つずつ作成してください。

【単語リスト】
{$wordListText}

要件:
- 各単語の正解の意味に似ているが微妙に違う日本語の意味を3つ
- 英語学習者が間違えやすい、似た意味を選ぶこと
- 各選択肢は15文字以内の簡潔な日本語で

以下の形式で出力してください:

1. {$wordList[0]['english']}
1-1. (不正解1)
1-2. (不正解2)
1-3. (不正解3)

2. {$wordList[1]['english']}
2-1. (不正解1)
2-2. (不正解2)
2-3. (不正解3)

（以下同様に全ての単語について）";

            try {
                \Log::info('Calling Gemini API for batch test generation');

                $response = Http::timeout(30)->post(
                    "https://generativelanguage.googleapis.com/v1beta/models/gemini-3-flash-preview:generateContent?key={$apiKey}",
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

                    \Log::info('Generated batch test options', ['text' => $generatedText]);

                    // 各単語の選択肢を抽出
                    foreach ($selectedWords as $index => $word) {
                        $correctMeaning = $word->japanese->first()->japanese;
                        $wordNum = $index + 1;

                        // この単語の不正解選択肢を抽出
                        $pattern = "/{$wordNum}\.\s+.*?\n{$wordNum}-1\.\s*(.+?)\n{$wordNum}-2\.\s*(.+?)\n{$wordNum}-3\.\s*(.+?)(\n|$)/s";

                        if (preg_match($pattern, $generatedText, $matches)) {
                            $wrongOptions = [
                                trim($matches[1]),
                                trim($matches[2]),
                                trim($matches[3])
                            ];

                            $options = collect([$correctMeaning])->merge($wrongOptions)->shuffle();

                            $questions[] = [
                                'word' => $word,
                                'correct_meaning' => $correctMeaning,
                                'options' => $options->toArray()
                            ];
                        }
                    }
                }
            } catch (\Exception $e) {
                \Log::error('Gemini API batch generation error: ' . $e->getMessage());
            }
        }

        // API生成に失敗した場合は従来の方法で補完
        if (count($questions) < $questionCount && $words->count() >= 4) {
            $remainingWords = $selectedWords->slice(count($questions));

            foreach ($remainingWords as $word) {
                $correctMeaning = $word->japanese->first()->japanese;
                $wrongWords = $words->where('id', '!=', $word->id)->random(min(3, $words->count() - 1));
                $wrongMeanings = $wrongWords->map(function($w) {
                    return $w->japanese->first()->japanese;
                });
                $options = collect([$correctMeaning])->merge($wrongMeanings)->shuffle();

                $questions[] = [
                    'word' => $word,
                    'correct_meaning' => $correctMeaning,
                    'options' => $options->toArray()
                ];
            }
        }

        return $questions;
    }

    private function generateSimilarOptions($englishWord, $correctMeaning)
    {
        $apiKey = config('services.gemini.api_key');

        if (!$apiKey) {
            \Log::error('Gemini API key not found for test generation');
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
            \Log::info('Calling Gemini API for test generation', ['word' => $englishWord]);

            $response = Http::timeout(15)->post(
                "https://generativelanguage.googleapis.com/v1beta/models/gemini-3-flash-preview:generateContent?key={$apiKey}",
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

            \Log::info('Gemini API response status', ['status' => $response->status()]);

            if ($response->successful()) {
                $result = $response->json();
                $generatedText = $result['candidates'][0]['content']['parts'][0]['text'] ?? '';

                \Log::info('Generated text from API', ['text' => $generatedText]);

                // 選択肢を抽出
                $wrongOptions = [];
                if (preg_match_all('/^\d+\.\s*(.+)$/m', $generatedText, $matches)) {
                    $wrongOptions = array_slice($matches[1], 0, 3);
                }

                \Log::info('Extracted wrong options', ['options' => $wrongOptions, 'count' => count($wrongOptions)]);

                // 正解と不正解をシャッフル
                if (count($wrongOptions) === 3) {
                    return collect([$correctMeaning])->merge($wrongOptions)->shuffle();
                } else {
                    \Log::warning('Not enough options generated', ['count' => count($wrongOptions)]);
                }
            } else {
                \Log::error('Gemini API request failed', ['status' => $response->status(), 'body' => $response->body()]);
            }
        } catch (\Exception $e) {
            \Log::error('Gemini API error in test generation: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
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

        $remainingQuestions = count(session('test_questions', []));
        $hasMoreQuestions = $remainingQuestions > 0;

        return view('test-result', compact('isCorrect', 'selectedAnswer', 'correctAnswer', 'word', 'hasMoreQuestions'));
    }
}
