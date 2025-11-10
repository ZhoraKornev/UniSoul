<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('support_managers', function (Blueprint $table) {
            $table->id();

            // Зв'язок з основним записом співробітника (Employee)
            $table->unsignedBigInteger('employee_id')->unique();
            $table->unsignedBigInteger('branch_id'); // Для швидкого пошуку по філіях

            // Telegram Routing Info (Унікальні ID для маршрутизації)
            $table->bigInteger('telegram_user_id')->unique()->comment('Telegram User ID менеджера');
            $table->bigInteger('telegram_chat_id')->unique()->comment('Telegram Chat ID для надсилання повідомлень');

            // Спеціалізована доступність для системи підтримки (може відрізнятися від is_available в Employee)
            $table->boolean('is_available')->default(true)->comment('Доступний для прийому нових сесій');

            $table->timestamps();

            // Встановлення зовнішніх ключів
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('support_managers');
    }
};
