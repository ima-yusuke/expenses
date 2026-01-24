<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Word;

class ReplyController extends Controller
{
    public function ShowReplyAssistant()
    {
        return view('reply-assistant');
    }

    public function GenerateReply(Request $request)
    {
        $friendMessage = $request->input('friend_message');
        $replyIntent = $request->input('reply_intent');

        // 単語帳の全単語を取得
        $words = Word::with('japanese')->get();

        // 単語帳の単語リストを作成（英単語のみ）
        $wordList = $words->map(function($word) {
            return $word->word;
        })->implode(', ');

        // Gemini APIキーを取得
        $apiKey = config('services.gemini.api_key');

        if (!$apiKey) {
            return back()->with('error', 'Gemini API キーが設定されていません。.envファイルにGEMINI_API_KEYを追加してください。');
        }

        // Gemini APIにリクエスト
        $prompt = "あなたは友達とのカジュアルな英語チャットをサポートするアシスタントです。

友達からのメッセージ: \"{$friendMessage}\"

ユーザーの返信したい内容: \"{$replyIntent}\"

以下の英単語リストの中から、自然に使えそうなものがあれば1-2個程度使って返信文を作成してください。
これらの単語の意味やニュアンス、イディオム、様々な使い方を考慮して、最も自然な形で使用してください。

【単語リスト】
{$wordList}

重要な要件:
1. 最優先: 自然でカジュアルな友達同士の会話表現
2. 友達のメッセージに対する適切な返信
3. ユーザーの返信意図を正確に反映
4. 単語リストの単語は無理に使わず、自然に使える場合のみ1-2個程度使用
5. 単語の持つ様々な意味やイディオム表現も考慮して使用
6. 短くてシンプルな文章（長すぎないこと）

返信文とその日本語訳、使用した単語を以下の形式で出力してください:

【英語返信】
(返信文)

【日本語訳】
(日本語訳)

【使用した単語帳の単語】
(使用した場合のみ: 単語1, 単語2)";

        try {
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

                // 英語返信部分を抽出
                $englishReply = '';
                if (preg_match('/【英語返信】\s*\n(.+?)(\n|$)/s', $generatedText, $matches)) {
                    $englishReply = trim($matches[1]);
                } else if (preg_match('/英語返信[:\s]*\n(.+?)(\n|$)/s', $generatedText, $matches)) {
                    $englishReply = trim($matches[1]);
                }

                // 使用した単語を抽出
                $usedWords = [];
                foreach ($words as $word) {
                    if (stripos($generatedText, $word->word) !== false) {
                        $usedWords[] = $word;
                    }
                }

                return view('reply-result', compact('friendMessage', 'replyIntent', 'generatedText', 'englishReply', 'usedWords'));
            } else {
                return back()->with('error', 'APIリクエストに失敗しました: ' . $response->body());
            }
        } catch (\Exception $e) {
            return back()->with('error', 'エラーが発生しました: ' . $e->getMessage());
        }
    }
}
