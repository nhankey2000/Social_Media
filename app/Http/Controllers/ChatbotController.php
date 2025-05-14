<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChatbotController extends Controller
{
    public function handleMessage(Request $request)
    {
        $message = $request->input('message');

        if (!$message) {
            return response()->json(['reply' => 'Bạn chưa nhập nội dung.'], 400);
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . env('GROQ_API_KEY'),
                'Content-Type'  => 'application/json',
            ])->post('https://api.groq.com/openai/v1/chat/completions', [
                'model'    => 'llama3-8b-8192', // ✅ miễn phí, phản hồi nhanh
                'messages' => [
                    ['role' => 'user', 'content' => $message],
                ],
                'temperature' => 0.7,
                'max_tokens'  => 300,
            ]);
            Log::info('Groq raw response: ' . $response->body());

            if ($response->failed()) {
                Log::error('Groq API lỗi: ' . $response->body());
                return response()->json(['reply' => 'Groq gặp lỗi, thử lại sau.'], 500);
            }

            $data = $response->json();
            $reply = $data['choices'][0]['message']['content'] ?? 'Tôi không hiểu.';

            return response()->json(['reply' => $reply]);
        } catch (\Exception $e) {
            Log::error('Groq Exception: ' . $e->getMessage());
            return response()->json(['reply' => 'Lỗi server Groq.'], 500);
        }
    }
}
