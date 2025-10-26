<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Api;
use Telegram\Bot\Exceptions\TelegramSDKException;

class TelegramSetWebhook extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'telegram:set-webhook {--url=}
                                                {--remove : Remove the webhook instead of setting it}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set or remove the Telegram bot webhook URL.';

    /**
     * Execute the console command.
     */
    public function handle(Api $telegram): int
    {
        if ($this->option('remove')) {
            return $this->removeWebhook($telegram);
        }

        $url = $this->option('url') ?? config('app.url') . '/telegram/webhook';

        if (empty(config('telegram.bots.default.token'))) {
            $this->error('The TELEGRAM_BOT_TOKEN is not set in your .env file or config.');
            return Command::FAILURE;
        }

        $this->info("Attempting to set webhook to: {$url}");

        try {
            $response = $telegram->setWebhook(['url' => $url]);

            if ($response) {
                $this->info('Webhook successfully set.');
                $this->line("Response: " . json_encode($response->toArray(), JSON_PRETTY_PRINT));
                return Command::SUCCESS;
            } else {
                $this->error('Failed to set webhook. Telegram API returned a non-successful response.');
                return Command::FAILURE;
            }
        } catch (TelegramSDKException $e) {
            $this->error("Telegram SDK Error: " . $e->getMessage());
            Log::error("Telegram Set Webhook Error: " . $e->getMessage());
            return Command::FAILURE;
        }
    }

    /**
     * Remove the webhook.
     */
    protected function removeWebhook(Api $telegram): int
    {
        $this->info('Attempting to remove webhook...');

        try {
            $response = $telegram->removeWebhook();

            if ($response) {
                $this->info('Webhook successfully removed.');
                $this->line("Response: " . json_encode($response->toArray(), JSON_PRETTY_PRINT));
                return Command::SUCCESS;
            } else {
                $this->error('Failed to remove webhook.');
                return Command::FAILURE;
            }
        } catch (TelegramSDKException $e) {
            $this->error("Telegram SDK Error: " . $e->getMessage());
            Log::error("Telegram Remove Webhook Error: " . $e->getMessage());
            return Command::FAILURE;
        }
    }
}

