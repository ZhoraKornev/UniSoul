<?php

namespace App\Http\Controllers;

use App\Services\TelegramBotService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TelegramController extends Controller
{
    protected TelegramBotService $botService;

    /**
     * Inject the bot service.
     */
    public function __construct(TelegramBotService $botService)
    {
        $this->botService = $botService;
    }

    /**
     * Handles the incoming Telegram webhook update.
     */
    public function webhook(Request $request): JsonResponse
    {
        $update = $request->all();

        if (empty($update)) {
            Log::warning('Received empty update from Telegram webhook.');
            return response()->json(['status' => 'ok', 'message' => 'Empty update received'], 200);
        }

        try {
            // Process the update through the service
            $this->botService->handleUpdate($update);

            // Telegram expects a 200 OK response immediately
            return response()->json(['status' => 'ok']);
        } catch (\Exception $e) {
            Log::error('Error processing Telegram update: ' . $e->getMessage(), ['update' => $update]);
            // Still return 200 OK to Telegram to prevent repeated attempts, but log the error
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 200);
        }
    }
}
