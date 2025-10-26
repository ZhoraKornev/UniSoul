<?php

namespace Tests\Feature;

use App\Models\UserConfig;
use App\Models\UserMessage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Telegram\Bot\Api;
use Mockery;

class TelegramWebhookTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Mock the Telegram API client to prevent actual API calls during testing
        $mockTelegramApi = Mockery::mock(Api::class);

        // Expect the sendMessage method to be called once with any arguments
        $mockTelegramApi->shouldReceive('sendMessage')
                        ->once()
                        ->andReturn(true); // Mock a successful send

        // Bind the mock to the service container
        $this->app->instance(Api::class, $mockTelegramApi);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * Test a successful webhook call that creates a new user and message record.
     */
    public function test_webhook_creates_user_and_message_and_replies(): void
    {
        // 1. Define a minimal Telegram update payload for a new user sending /start
        $updatePayload = [
            'update_id' => 123456789,
            'message' => [
                'message_id' => 123,
                'from' => [
                    'id' => 12345,
                    'is_bot' => false,
                    'first_name' => 'Test',
                    'username' => 'testuser',
                    'language_code' => 'en',
                ],
                'chat' => [
                    'id' => 12345,
                    'first_name' => 'Test',
                    'username' => 'testuser',
                    'type' => 'private',
                ],
                'date' => time(),
                'text' => '/start',
            ],
        ];

        // 2. Hit the webhook endpoint
        $response = $this->postJson('/telegram/webhook', $updatePayload);

        // 3. Assert the response is successful (Telegram expects 200 OK)
        $response->assertStatus(200);
        $response->assertJson(['status' => 'ok']);

        // 4. Assert that a new UserConfig was created in the database
        $this->assertDatabaseHas(UserConfig::class, [
            'telegram_user_id' => 12345,
            'username' => 'testuser',
            'first_name' => 'Test',
        ]);

        // 5. Assert that a new UserMessage was created in the database
        $userConfig = UserConfig::where('telegram_user_id', 12345)->first();
        $this->assertDatabaseHas(UserMessage::class, [
            'user_config_id' => $userConfig->id,
            'telegram_user_id' => 12345,
            'message' => '/start',
        ]);

        // 6. The mock setup already asserts that `sendMessage` was called once.
    }

    /**
     * Test a webhook call from an existing user sending a free-text message.
     */
    public function test_webhook_handles_existing_user_free_text(): void
    {
        // 1. Create an existing user
        $userConfig = UserConfig::factory()->create([
            'telegram_user_id' => 54321,
            'username' => 'existinguser',
            'first_name' => 'Existing',
        ]);

        // 2. Define a minimal Telegram update payload
        $updatePayload = [
            'update_id' => 987654321,
            'message' => [
                'message_id' => 456,
                'from' => [
                    'id' => 54321,
                    'is_bot' => false,
                    'first_name' => 'Existing',
                    'username' => 'existinguser',
                ],
                'chat' => [
                    'id' => 54321,
                    'first_name' => 'Existing',
                    'username' => 'existinguser',
                    'type' => 'private',
                ],
                'date' => time(),
                'text' => 'This is a free-text query.',
            ],
        ];

        // 3. Hit the webhook endpoint
        $response = $this->postJson('/telegram/webhook', $updatePayload);

        // 4. Assert the response is successful
        $response->assertStatus(200);
        $response->assertJson(['status' => 'ok']);

        // 5. Assert that a new UserMessage was created
        $this->assertDatabaseHas(UserMessage::class, [
            'user_config_id' => $userConfig->id,
            'telegram_user_id' => 54321,
            'message' => 'This is a free-text query.',
        ]);

        // 6. Assert that no new UserConfig was created
        $this->assertCount(1, UserConfig::all());
    }
}

