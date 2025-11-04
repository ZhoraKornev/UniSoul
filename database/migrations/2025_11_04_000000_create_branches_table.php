<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('branches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('confession_id')->constrained()->onDelete('cascade');
            $table->json('name');
            $table->json('address');
            $table->json('description')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->json('schedule')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();

            $table->index(['confession_id', 'active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('branches');
    }
};