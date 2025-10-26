<?php

namespace App\Services;

use App\Models\UserConfig;
use App\Models\UserMessage;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Api;
use Telegram\Bot\Keyboard\Keyboard;
use Telegram\Bot\Objects\Update;

class TelegramBotService
{
    protected Api $telegram;

    public function __construct(Api $telegram)
    {
        // The Telegram API client is injected, which is a good practice.
        // The SDK's ServiceProvider will handle the configuration via TELEGRAM_BOT_TOKEN
        $this->telegram = $telegram;
    }

    /**
     * Main handler for incoming Telegram updates.
     */
    public function handleUpdate(array $updateData): void
    {
        $update = new Update($updateData);

        // We only care about message updates for this simple bot
        if (!$update->getMessage()) {
            return;
        }

        $message = $update->getMessage();
        $chatId = $message->getChat()->getId();
        $text = $message->getText();
        $from = $message->getFrom();

        // 1. Find or create user config
        $userConfig = UserConfig::firstOrCreate(
            ['telegram_user_id' => $from->getId()],
            [
                'username' => $from->getUsername(),
                'first_name' => $from->getFirstName(),
                'last_name' => $from->getLastName(),
            ]
        );

        // 2. Store the incoming message
        UserMessage::create([
            'user_config_id' => $userConfig->id,
            'telegram_user_id' => $from->getId(),
            'username' => $from->getUsername(),
            'message' => $text,
            'raw_update' => $updateData,
        ]);

        // 3. Handle commands/menu
        $response = $this->handleCommand($text, $userConfig);

        // 4. Send reply
        $this->sendReply($chatId, $response['text'], $response['keyboard'] ?? null);
    }

    /**
     * Handle specific commands or menu selections.
     *
     * @return array{text: string, keyboard: ?Keyboard}
     */
    protected function handleCommand(string $text, UserConfig $userConfig): array
    {
        $text = strtolower(trim($text));

        return match ($text) {
            '/start', 'start', 'main menu' => $this->handleStart($userConfig),
            '1. ask a question' => $this->handleAskQuestion(),
            '2. my config' => $this->handleMyConfig($userConfig),
            '3. help' => $this->handleHelp(),
            default => $this->handleFreeText($text),
        };
    }

    /**
     * Handles the /start command, showing the main menu.
     *
     * @return array{text: string, keyboard: Keyboard}
     */
    protected function handleStart(UserConfig $userConfig): array
    {
        $text = "Hello, {$userConfig->first_name}! Welcome to the bot. Please choose an option from the menu below.";
        return [
            'text' => $text,
            'keyboard' => $this->getStartMenuKeyboard(),
        ];
    }

    /**
     * Handles the "Ask a question" option.
     *
     * @return array{text: string, keyboard: Keyboard}
     */
    protected function handleAskQuestion(): array
    {
        return [
            'text' => "Please type your question. We will store it and get back to you (eventually!).",
            'keyboard' => $this->getStartMenuKeyboard(),
        ];
    }

    /**
     * Handles the "My config" option.
     *
     * @return array{text: string, keyboard: Keyboard}
     */
    protected function handleMyConfig(UserConfig $userConfig): array
    {
        $notifications = $userConfig->notifications_enabled ? 'Enabled' : 'Disabled';
        $text = "Your Current Config:\n"
            . "• Language: {$userConfig->language}\n"
            . "• Notifications: {$notifications}\n\n"
            . "To change your language to Spanish, type: `set language es`\n"
            . "To toggle notifications, type: `toggle notifications`\n\n"
            . "Type 'Main Menu' to go back.";

        // Simple free-text commands for config update
        if (preg_match('/^set language (en|es)$/i', trim($userConfig->message), $matches)) {
            $userConfig->language = strtolower($matches[1]);
            $userConfig->save();
            $text = "Language updated to {$userConfig->language}.";
        } elseif (preg_match('/^toggle notifications$/i', trim($userConfig->message))) {
            $userConfig->notifications_enabled = !$userConfig->notifications_enabled;
            $userConfig->save();
            $notifications = $userConfig->notifications_enabled ? 'Enabled' : 'Disabled';
            $text = "Notifications toggled. They are now {$notifications}.";
        }

        return [
            'text' => $text,
            'keyboard' => $this->getStartMenuKeyboard(),
        ];
    }

    /**
     * Handles the "Help" option.
     *
     * @return array{text: string, keyboard: Keyboard}
     */
    protected function handleHelp(): array
    {
        return [
            'text' => "This is a simple Laravel bot. Use the menu to interact or send any message to get a standard reply.",
            'keyboard' => $this->getStartMenuKeyboard(),
        ];
    }

    /**
     * Handles any free-text message not covered by a command.
     *
     * @return array{text: string, keyboard: Keyboard}
     */
    protected function handleFreeText(string $text): array
    {
        Log::info("Received free text: {$text}");
        return [
            'text' => "Thank you for your message! We have stored your query and will process it shortly. Please use the menu for specific actions.",
            'keyboard' => $this->getStartMenuKeyboard(),
        ];
    }

    /**
     * Sends a reply message to the user.
     */
    public function sendReply(int $chatId, string $text, ?Keyboard $keyboard = null): void
    {
        $params = [
            'chat_id' => $chatId,
            'text' => $text,
        ];

        if ($keyboard) {
            $params['reply_markup'] = $keyboard;
        }

        try {
            $this->telegram->sendMessage($params);
        } catch (\Exception $e) {
            Log::error("Failed to send Telegram message: " . $e->getMessage());
        }
    }

    /**
     * Generates the main menu reply keyboard.
     */
    protected function getStartMenuKeyboard(): Keyboard
    {
        return Keyboard::make([
            'keyboard' => [
                ['1. Ask a question'],
                ['2. My config', '3. Help'],
            ],
            'resize_keyboard' => true,
            'one_time_keyboard' => false,
        ]);
    }
}

