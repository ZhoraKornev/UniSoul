🕊️ **UniSoul: Spiritual Guide (Telegram Bot)**  

🚨 **PROJECT STATUS:** *Testing Mode*  

# Laravel 12 Telegram Bot (Sail Ready)

This project is a modern, Laravel 12 (PHP 8.2) compatible Telegram bot implementation using **Laravel Sail** for development and the official `irazasyed/telegram-bot-sdk` (v3.x) for handling updates and sending messages.

## 1. Setup and Installation

This guide assumes you have Docker installed and running on your system.

### A. Project Files

Ensure you have the following files in place (these are the files provided by the patch):

- `app/Http/Kernel.php`
- `app/Http/Middleware/*` (All updated middleware files)
- `app/Http/Controllers/TelegramController.php`
- `app/Services/TelegramBotService.php`
- `app/Models/UserConfig.php`
- `app/Models/UserMessage.php`
- `app/Console/Commands/TelegramSetWebhook.php`
- `app/Providers/AppServiceProvider.php` (Updated to register the command)
- `database/migrations/*_create_user_configs_table.php`
- `database/migrations/*_create_user_messages_table.php`
- `routes/api.php`
- `composer.json` (Updated with Laravel 12/PHP 8.2 requirements)
- `tests/Feature/TelegramWebhookTest.php`
- `database/factories/UserConfigFactory.php`

### B. Environment Variables

Copy the contents of the provided `.env.additions.txt` into your `.env` file and replace the placeholder with your actual bot token.

```dotenv
# .env additions
TELEGRAM_BOT_TOKEN="<YOUR_BOT_TOKEN_FROM_BOTFATHER>"

# Standard MySQL DB Config (Sail default)
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=sail
DB_PASSWORD=password
```

### C. Running with Laravel Sail

Use the following commands to get the project running:

1.  **Start the Sail environment:**
    ```bash
    ./vendor/bin/sail up -d
    ```
    *(If you don't have the `vendor` directory yet, you may need to run `composer install` first. If you don't have the `sail` executable, you may need to run `composer global require laravel/sail` or use `docker run --rm -v $(pwd):/app -w /app composer install`)*

2.  **Install PHP dependencies inside the container:**
    ```bash
    ./vendor/bin/sail composer install
    ```

3.  **Run database migrations:**
    ```bash
    ./vendor/bin/sail artisan migrate
    ```
    This will create the `user_configs` and `user_messages` tables.

## 2. Webhook Setup and Local Testing (via ngrok)

For the Telegram bot to work, you must tell Telegram where to send updates (the webhook URL).

1.  **Start ngrok** to expose your local Sail environment (which runs on port 80) to the public internet.
    ```bash
    ngrok http 80
    ```
    Copy the public HTTPS URL (e.g., `https://<your-ngrok-id>.ngrok-free.app`).

2.  **Set the webhook** using the custom Artisan command:
    ```bash
    # Replace the URL with your ngrok public HTTPS URL
    ./vendor/bin/sail artisan telegram:set-webhook --url=https://<your-ngrok-id>.ngrok-free.app/telegram/webhook
    ```
    If successful, you will see a confirmation message from the Telegram API.

3.  **Test the bot:** Open Telegram, find your bot, and send the `/start` command. You should see the main menu reply.

## 3. Testing the Code

The project includes a feature test to verify the webhook functionality and database persistence.

1.  **Run the tests** inside the Sail container:
    ```bash
    ./vendor/bin/sail artisan test
    ```
    This will run `tests/Feature/TelegramWebhookTest.php` and confirm that incoming updates are correctly handled, stored in the database, and that a reply is attempted (by mocking the Telegram API client).

## 4. Compatibility Fixes Summary

The following files were replaced/updated to ensure full Laravel 12 compatibility and PHP 8.2 type-hinting:

| Outdated File | Laravel 12 Replacement/Change |
| :--- | :--- |
| `app/Http/Middleware/*` | Replaced with modern Laravel 12 versions, ensuring correct namespaces, use statements, and return type declarations (e.g., `Response` from `Symfony\Component\HttpFoundation\Response`). |
| `app/Http/Kernel.php` | Updated to the Laravel 12 structure, including the correct registration of the updated middleware files in `$middleware` and `$routeMiddleware` arrays. `TrustProxies` is explicitly included and configured to trust all proxies (`*`), which is essential for working correctly within Docker/Sail and behind ngrok. |
| `routes/api.php` | The webhook route `POST /telegram/webhook` was added, using the modern `[Controller::class, 'method']` array syntax. |
| `app/Http/Middleware/VerifyCsrfToken.php` | The webhook route `/telegram/webhook` was added to the `$except` array to bypass CSRF protection for external API calls, as is standard practice for webhooks. |
| **New Files** | New files like `app/Models/UserConfig.php`, `app/Services/TelegramBotService.php`, and `app/Console/Commands/TelegramSetWebhook.php` were created following Laravel 12 idioms and best practices. |

## 5. Main Flow Examples

The `TelegramBotService` implements the following flows:

| User Action | Bot Response | Persistence |
| :--- | :--- | :--- |
| `/start` or "Main Menu" | Greets user, shows main menu keyboard. | Creates `UserConfig` if new user. Stores message in `UserMessage`. |
| "1. Ask a question" | Prompts user to type a question. | Stores message in `UserMessage`. |
| "2. My config" | Displays current config (language, notifications). | Stores message in `UserMessage`. |
| Free-text message | Acknowledges the query with a standard reply. | Stores message in `UserMessage`. |
| `set language es` (in config menu) | Updates the user's language preference. | Updates `UserConfig` record. |
| `toggle notifications` (in config menu) | Toggles the notification setting. | Updates `UserConfig` record. |

The core logic is within `app/Services/TelegramBotService.php`, which handles parsing the update, managing the database records, and sending replies using the injected `Telegram\Bot\Api` client.
