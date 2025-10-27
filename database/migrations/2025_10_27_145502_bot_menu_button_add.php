<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bot_buttons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->nullable()->constrained('bot_buttons')->onDelete('cascade');
            $table->json('text'); // Translatable button text
            $table->string('callback_data')->nullable(); // Action identifier for handlers
            $table->integer('order')->default(0); // Display order
            $table->boolean('active')->default(true); // Enable/disable button
            $table->timestamps();

            $table->index(['parent_id', 'order']);
            $table->index('callback_data');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bot_buttons');
    }
};
