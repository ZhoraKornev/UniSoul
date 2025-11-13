<?php

use App\Enums\State;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();

            // --- ADMIN / FILAMENT FIELDS (NULLABLE FOR TELEGRAM USERS) ---
            // Note: Unique constraints on 'email' must now be handled at the application/validation level
            // for non-NULL values, allowing multiple NULLs for Telegram users.

            $table->string('name')
                ->nullable()
                ->comment('Full name for Filament/Admin users, nullable for Telegram users.');

            $table->string('email')
                ->nullable()
                ->comment('Email address for Filament/Admin login. Unique check is handled by application logic to allow multiple NULLs for Telegram users.');

            $table->timestamp('email_verified_at')
                ->nullable()
                ->comment('Timestamp when the email was verified.');

            $table->string('password')
                ->nullable() // Made nullable to allow Telegram users without a password
                ->comment('Hashed password for Filament/Admin login.');

            $table->rememberToken()
                ->comment('Remember me token for web sessions.');

            // --- TELEGRAM BOT FIELDS ---
            // Note: Unique constraint removed to allow multiple admin users (who have NULL telegram_user_id).
            // Uniqueness for actual Telegram IDs must be enforced by application logic.

            $table->unsignedBigInteger('telegram_user_id')
                ->nullable()
                ->comment('Unique user ID provided by Telegram. Uniqueness should be enforced by application logic to allow multiple NULLs for Admin users.');

            $table->string('first_name')
                ->nullable()
                ->comment('First name provided by Telegram.');

            $table->string('last_name')
                ->nullable()
                ->comment('Last name provided by Telegram.');

            $table->string('username')
                ->nullable()
                ->comment('Username provided by Telegram (e.g., @user_name).');

            // --- BOT STATE ENUM CODES (Integer Status Grouping) ---
            // 1x: Active/Ready States (e.g., 10=READY)
            // 2x: Non-Active/Restricted States (e.g., 20=BANNED, 21=STOPPED)
            // 3x: Temporary/Flow States (e.g., 30=AWAITING_NAME)
            $table->unsignedSmallInteger('current_state')
                ->default(State::AwaitingConfirmation->value) // Default value from Enum
                ->comment('Integer status code for the bot conversation flow. Uses grouped statuses: 1x (Active), 2x (Non-Active), 3x (Flow). Default: '.State::AwaitingConfirmation->value);

            $table->json('configuration')
                ->nullable()
                ->comment('JSON field to store user-specific configuration data (settings, preferences).');

            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
