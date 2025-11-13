<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use SergiX49\Nutgram\Nutgram;
use Symfony\Component\Console\Command\Command as CommandCLI;
use Throwable;

class TelegramSetWebhook extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'telegram:set-webhook
                                                {--url= : Specify the webhook URL manually.}
                                                {--remove : Remove the webhook instead of setting it.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set or remove the Telegram bot webhook URL using Nutgram.';

    /**
     * Execute the console command.
     */
    public function handle(Nutgram $bot): int
    {
        if ($this->option('remove')) {
            return $this->removeWebhook($bot);
        }

        // Added spaces around the concatenation operator
        $url = $this->option('url') ?? config('app.url').config('nutgram.global.url');

        if (empty(config('nutgram.bots.default.token'))) {
            $this->error('The NUTGRAM_BOT_TOKEN is not set in your .env file or config.');

            return CommandCLI::FAILURE;
        }

        $this->info('Attempting to set webhook to: '.$url);

        try {
            // Set the webhook
            $bot->setWebhook(url: $url);

            // Fetch webhook info to confirm and display details
            $response = $bot->getWebhookInfo();

            if ($response->url === $url) {
                $this->info('Webhook successfully set.');
                $this->line('Current Webhook Info: '.json_encode($response->toArray(), JSON_PRETTY_PRINT));

                return CommandCLI::SUCCESS;
            }

            $this->error('Failed to set webhook or URL mismatch.');

            return CommandCLI::FAILURE;
        } catch (Throwable $e) {
            $this->error('Nutgram API Error: '.$e->getMessage());
            Log::error('Nutgram Set Webhook Error: '.$e->getMessage());

            return CommandCLI::FAILURE;
        }
    }

    /**
     * Remove the webhook.
     */
    protected function removeWebhook(Nutgram $bot): int
    {
        if (empty(config('nutgram.bots.default.token'))) {
            $this->error('The NUTGRAM_BOT_TOKEN is not set in your .env file or config.');

            return CommandCLI::FAILURE;
        }

        $this->info('Attempting to remove webhook...');

        try {
            $bot->deleteWebhook();

            $response = $bot->getWebhookInfo();

            if (empty($response->url)) {
                $this->info('Webhook successfully removed.');
                $this->line('Response: '.json_encode($response->toArray(), JSON_PRETTY_PRINT));

                return CommandCLI::SUCCESS;
            }

            $this->error('Failed to remove webhook. Webhook URL is still present.');

            return CommandCLI::FAILURE;
        } catch (Throwable $e) {
            $this->error('Nutgram API Error: '.$e->getMessage());
            Log::error('Nutgram Remove Webhook Error: '.$e->getMessage());

            return CommandCLI::FAILURE;
        }
    }
}
