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

            $table->foreignId('parent_id')
                ->nullable()
                ->constrained('bot_buttons')
                ->onDelete('cascade');

            $table->nullableMorphs('entity'); // <─ Laravel auto sets index

            $table->json('text');
            $table->string('callback_data'); // enum value identifier

            $table->unsignedInteger('order')->default(0);
            $table->boolean('active')->default(true);

            $table->timestamps();

            // Extra indexes
            $table->index(['parent_id', 'order']);
            $table->index('callback_data');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bot_buttons');
    }
};

