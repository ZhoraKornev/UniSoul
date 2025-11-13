<?php

use App\Enums\State;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema; // Import the State Enum to use its value for the default

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('support_sessions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('branch_id'); // Контекст філії
            $table->bigInteger('user_id'); // Telegram User ID клієнта

            $table->unsignedBigInteger('manager_id')->nullable();

            $table->bigInteger('user_chat_id');
            $table->bigInteger('manager_chat_id')->nullable();

            // MODIFIED: Status now uses an integer column to store the State enum values
            $table->unsignedSmallInteger('status')->default(State::ActiveConversation->value);

            // Added columns from $fillable
            $table->string('mode')->default('human'); // Default mode
            $table->string('ai_thread_id')->nullable();
            $table->timestamp('ai_handoff_at')->nullable();

            $table->timestamps();

            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
            $table->foreign('manager_id')->references('id')->on('employees')->onDelete('set null');

            $table->index(['user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('support_sessions');
    }
};
