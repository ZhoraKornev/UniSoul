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
        // Створення таблиці для країн, якщо вона ще не існує
        Schema::create('countries', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('code', 2)->unique(); // Наприклад, 'UA', 'DE', 'GE'
            $table->timestamps();
        });

        // Таблиця для конфесій з полем 'countries', яке містить JSON-масив ID країн
        Schema::create('confessions', function (Blueprint $table) {
            $table->id();
            // Поля, які будуть перекладатись, зберігаються як JSON для Spatie Translatable
            $table->json('name');        // Назва конфесії (Translatable: uk, en, ro, de, ka, ru)
            $table->json('full_name');   // Повна назва (Translatable)
            $table->json('description'); // Опис (Translatable)

            $table->string('emoji', 10);

            // Масив країн, у яких поширена конфесія (зберігаємо ID країн)
            $table->json('country_ids');

            // Активність конфесії
            $table->boolean('active')->default(false);
            // ВИПРАВЛЕНО: Видалено ->after('active') з Schema::create
            $table->json('available_actions')->nullable()
                ->comment('Доступні дії (послуги) для цієї конфесії, зберігається як масив значень ConfessionSubActions enum.');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('confessions');
        Schema::dropIfExists('countries');
    }
};
