<?php

namespace App\Http\Controllers;

use App\Services\GeminiAIService;
use Illuminate\Http\Request;

class BotController extends Controller
{
    public function testGemini(Request $request)
    {
        $aiService = app(GeminiAIService::class);
        $threadId = $aiService->startThread(12345);

        $response = $aiService->sendMessage(
            threadId: $threadId,
            message: 'What is the meaning of life?',
            systemInstruction: 'You are a helpful spiritual guide. Answer briefly and wisely.'
        );

        return response()->json([
            'thread_id' => $threadId,
            'response' => $response,
        ]);
    }
}
