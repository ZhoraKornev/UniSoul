<?php

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
        Schema::create('user_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_config_id')->constrained()->onDelete('cascade');
            $table->bigInteger('telegram_user_id')->index();
            $table->string('username')->nullable();
            $table->text('message')->nullable();
            $table->json('raw_update')->nullable();
            $table->boolean('is_bot_reply')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_messages');
    }
};

