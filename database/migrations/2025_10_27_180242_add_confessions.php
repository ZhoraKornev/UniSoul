<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('confessions', function (Blueprint $table) {
            $table->id();
            $table->json('name');
            $table->json('full_name');
            $table->json('description');
            $table->string('emoji', 10);
            $table->boolean('active')->default(false);
            $table->json('available_actions')->nullable();
            $table->timestamps();
        });

        Schema::create('confession_country', function (Blueprint $table) {
            $table->foreignId('confession_id')
                ->constrained()
                ->onDelete('cascade');
            $table->string('country_iso_3166_2', 2);
            $table->primary(['confession_id', 'country_iso_3166_2']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('confession_country');
        Schema::dropIfExists('confessions');
    }
};
