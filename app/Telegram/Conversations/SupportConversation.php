<?php

namespace App\Telegram\Conversations;

use App\Models\Employee;
use App\Models\SupportManager;
use App\Models\SupportSession;
use App\Services\GeminiAIService;
use Illuminate\Support\Facades\Log;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardMarkup;
use Throwable;

class SupportConversation extends BaseConversation
{
    // Ці властивості автоматично зберігаються Nutgram між кроками
    public ?int $branchId = null;

    public ?int $employeeId = null; // Обраний співробітник

    public static function beginWithParams(Nutgram $bot, int $branchId, ?int $employeeId = null)
    {
        // Store params temporarily in bot's user data
        $bot->setUserData('temp_branch_id', $branchId);
        $bot->setUserData('temp_employee_id', $employeeId);

        // Begin conversation normally
        return self::begin($bot);
    }

    /**
     * Початковий крок розмови підтримки.
     */
    public function start(Nutgram $bot): ?string
    {
        // Retrieve params from bot's user data
        if (! $this->branchId) {
            $this->branchId = $bot->getUserData('temp_branch_id');
            $this->employeeId = $bot->getUserData('temp_employee_id');

            // Clean up temp data
            $bot->deleteUserData('temp_branch_id');
            $bot->deleteUserData('temp_employee_id');
        }

        if (! $this->branchId) {
            $bot->sendMessage(__('telegram.support.error_branch_missing'));
            $this->end();

            return null;
        }

        // 2. Перевіряємо, чи клієнт вже має активну сесію
        if (SupportSession::where('user_id', $bot->userId())->where('status', 'ACTIVE')->exists()) {
            $bot->sendMessage(__('telegram.support.already_active_wait'));
            // Якщо сесія є, перевіряємо режим і маршрутизуємо відповідно
            /** @var SupportSession|null $session */
            $session = SupportSession::where('user_id', $bot->userId())->where('status', 'ACTIVE')->first();

            if ($session && $session->getAttribute('mode') === 'HUMAN') {
                $this->next('routeMessage');

                return null;
            }
            if ($session && $session->getAttribute('mode') === 'AI') {
                $this->next('routeAIMessage');

                return null;
            }

            // Якщо не визначено
            $this->end();

            return null;
        }

        $bot->sendMessage(__('telegram.support.connecting_text')); // "Підключення... очікуйте"

        // 3. Спробуємо знайти пріоритетного менеджера (якщо він був обраний і доступний)
        $manager = $this->findPreferredManager();

        // 4. Якщо пріоритетний менеджер недоступний, шукаємо будь-якого доступного в цій філії
        if (! $manager) {
            $manager = $this->findAvailableManagerByBranch($this->branchId);
        }

        // 5. Якщо знайдено менеджера-людину
        if ($manager) {
            return $this->initHumanSupport($bot, $manager);
        }

        // 6. Резерв: Перенаправляємо на AI-бота
        return $this->initAISupport($bot);
    }

    /**
     * Ініціалізує сесію з менеджером-людиною.
     */
    protected function initHumanSupport(Nutgram $bot, SupportManager $manager): ?string
    {
        try {
            // Отримуємо дані Employee для імені
            /** @var Employee|null $employee */
            $employee = Employee::find($manager->getAttribute('employee_id'));

            $session = SupportSession::create([
                'branch_id' => $this->branchId,
                'user_id' => $bot->userId(),
                'manager_id' => $manager->getAttribute('employee_id'), // manager_id - це ID Employee
                'user_chat_id' => $bot->chatId(),
                'manager_chat_id' => $manager->getAttribute('telegram_chat_id'),
                'status' => 'ACTIVE',
                'mode' => 'HUMAN',
            ]);

            $managerName = $employee ? $employee->getAttribute('name') : 'Менеджер';

            // Повідомляємо менеджера
            $bot->sendMessage(
                text: __('telegram.support.new_session_manager', [
                    'user_id' => $bot->userId(),
                    'user_name' => $bot->user()->username ?? $bot->user()->first_name,
                ]),
                chat_id: $manager->getAttribute('telegram_chat_id'),
                reply_markup: InlineKeyboardMarkup::make()->addRow(
                    InlineKeyboardButton::make(
                        text: __('telegram.support.close_session_button'),
                        callback_data: 'manager_close_session@'.$session->getAttribute('id')
                    )
                )
            );

            $bot->sendMessage(__('telegram.support.session_started', ['manager_name' => $managerName]));

            // Переходимо до кроку маршрутизації
            $this->next('routeMessage');

            return null;
        } catch (Throwable $e) {
            $bot->sendMessage(__('telegram.support.error_general'));
            Log::error('Human Support Init Error: '.$e->getMessage());
            $this->end();

            return null;
        }
    }

    /**
     * Ініціалізує сесію з AI-ботом.
     */
    protected function initAISupport(Nutgram $bot): ?string
    {
        //        try {
        // Використання app() для отримання сервісу
        $aiService = app(GeminiAIService::class);
        $threadId = $aiService->startThread($bot->userId()); // Створюємо новий потік для ШІ

        $session = SupportSession::create([
            'branch_id' => $this->branchId,
            'user_id' => $bot->userId(),
            'manager_id' => null, // Немає менеджера-людини
            'user_chat_id' => $bot->chatId(),
            'manager_chat_id' => null, // Немає чату менеджера
            'status' => 'ACTIVE',
            'mode' => 'AI', // Режим ШІ
            'ai_thread_id' => $threadId,
            'ai_handoff_at' => now(),
        ]);

        $bot->sendMessage(__('telegram.support.ai_mode_active'));

        // Переходимо до кроку обробки ШІ
        $this->next('routeAIMessage');

        return null;
        //        } catch (Throwable $e) {
        //            $bot->sendMessage(__('telegram.support.error_general_ai'));
        //            Log::error('AI Support Init Error: '.$e->getMessage());
        //            $this->end();
        //
        //            return null;
        //        }
    }

    /**
     * Маршрутизація повідомлень між клієнтом і менеджером-людиною.
     */
    public function routeMessage(Nutgram $bot): ?string
    {
        // Оновлюємо, щоб використовувати chatId() для пошуку
        $session = SupportSession::where('user_chat_id', $bot->chatId())
            ->where('status', 'ACTIVE')
            ->where('mode', 'HUMAN')
            ->first();

        if ($session && $session->getAttribute('manager_chat_id')) {
            // Пересилаємо повідомлення менеджеру
            $bot->forwardMessage(
                chat_id: $session->getAttribute('manager_chat_id'),
                from_chat_id: $bot->chatId(),
                message_id: $bot->messageId()
            );
            $this->next('routeMessage');

            return null;
        }

        // Якщо сесія перейшла в режим AI або закрита
        return $this->handleSessionEnd($bot);
    }

    /**
     * Обробка повідомлень, коли активований AI-режим.
     */
    public function routeAIMessage(Nutgram $bot): ?string
    {
        /** @var SupportSession|null $session */
        $session = SupportSession::where('user_chat_id', $bot->chatId())
            ->where('status', 'ACTIVE')
            ->where('mode', 'AI')
            ->first();

        if ($session && $session->ai_thread_id) {
            $aiService = app(GeminiAIService::class);

            // 1. Формуємо системну інструкцію
            $systemInstruction = __('telegram.support.ai_system_prompt');

            // 2. Надсилаємо повідомлення до ШІ (перевірка на тип контенту)
            // Тимчасовий текст, якщо це не текстове повідомлення
            $userMessage = $bot->message()->text ?? 'Користувач надіслав файл або нетекстове повідомлення.';

            $aiResponse = $aiService->sendMessage(
                threadId: $session->ai_thread_id,
                message: $userMessage,
                systemInstruction: $systemInstruction
            );

            // 3. Відправляємо відповідь ШІ користувачу
            $bot->sendMessage($aiResponse);

            // Тут потрібно додати логіку перевірки доступності менеджера для 'handoff'

            $this->next('routeAIMessage');

            return null;
        }

        // Якщо сесія не знайдена або закрита
        return $this->handleSessionEnd($bot);
    }

    protected function handleSessionEnd(Nutgram $bot): ?string
    {
        $bot->sendMessage(__('telegram.support.session_ended_auto'));

        $this->end();

        return null;
    }

    /**
     * Шукає обраного співробітника в таблиці SupportManager, якщо він доступний.
     */
    protected function findPreferredManager(): ?SupportManager
    {
        if (! $this->employeeId) {
            return null;
        }

        /** @var SupportManager|null $manager */
        $manager = SupportManager::where('employee_id', $this->employeeId)
            ->where('is_available', true)
            ->first();

        return $manager;
    }

    /**
     * Знаходить першого доступного менеджера для заданої філії.
     */
    protected function findAvailableManagerByBranch(int $branchId): ?SupportManager
    {
        return SupportManager::where('branch_id', $branchId)
            ->where('is_available', true)
            ->inRandomOrder()
            ->first();
    }
}
