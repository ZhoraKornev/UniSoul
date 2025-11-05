<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained()->onDelete('cascade');
            $table->json('name');
            $table->json('position');
            $table->enum('sex', ['male', 'female']);
            $table->integer('age');
            $table->boolean('is_available')->default(true);
            $table->boolean('active')->default(true);
            $table->string('phone')->nullable();
            $table->string('telegram_nickname')->nullable();
            $table->json('address');
            $table->json('other_info')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
