<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Сервіс для взаємодії з API Google Gemini (або іншим LLM).
 * Він імітує функціонал, необхідний для чат-бота з можливістю збереження історії (thread).
 * * ВАЖЛИВО: Для реальної роботи потрібно додати ваш API ключ та налаштувати
 * обробку HTTP-запитів та відповідей відповідно до документації Gemini API.
 */
class GeminiAIService
{
    protected string $apiKey;
    protected string $apiUrl = 'https://generativelanguage.googleapis.com/v1beta/models/';
    protected string $model = 'gemini-2.5-flash-preview-09-2025'; // Актуальна модель для чату

    public function __construct()
    {
        // Отримання ключа з конфігурації Laravel
        $this->apiKey = config('services.gemini.key');
    }

    /**
     * Створює новий потік розмови і повертає його унікальний ID.
     * У реальному Gemini API це може бути просто порожній ID або імітація.
     * Для цього прикладу ми просто повертаємо унікальний ID, який буде
     * використаний для ідентифікації історії повідомлень у сесії.
     */
    public function startThread(int $sessionId): string
    {
        // У більшості чат-API (як-от Google AI) 'потік' створюється автоматично при першому
        // повідомленні, або ми можемо використовувати ID сесії як ідентифікатор потоку.
        // Тут ми використовуємо простий UUID як імітацію.
        return (string) \Illuminate\Support\Str::uuid();
    }

    /**
     * Надсилає повідомлення користувача до LLM і повертає відповідь.
     * * @param string $threadId Унікальний ID потоку розмови
     * @param string $message Повідомлення від користувача
     * @param string $systemInstruction Інструкція для ШІ (його роль)
     * @return string Відповідь ШІ
     */
    public function sendMessage(string $threadId, string $message, string $systemInstruction): string
    {
        $payload = [
            'contents' => [
                ['role' => 'user', 'parts' => [['text' => $message]]],
            ],
            'systemInstruction' => [
                'parts' => [['text' => $systemInstruction]],
            ],
            // В реальному застосуванні тут потрібно додати логіку
            // для передачі попередньої історії повідомлень ($threadId)
        ];

        try {
            // Виконання POST запиту до Gemini API
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])
                ->post("{$this->apiUrl}{$this->model}:generateContent?key={$this->apiKey}", $payload);

            if ($response->successful()) {
                $result = $response->json();

                // Перевірка на наявність тексту у відповіді
                $text = $result['candidates'][0]['content']['parts'][0]['text'] ?? null;

                if ($text) {
                    return $text;
                }
            }

            Log::error('Gemini API Error: ' . $response->body());
            return 'Вибачте, сталася внутрішня помилка при з\'єднанні з ШІ. Спробуйте пізніше.';

        } catch (\Exception $e) {
            Log::error('Gemini Service Exception: ' . $e->getMessage());
            return 'Вибачте, сталася помилка з\'єднання з сервісом ШІ.';
        }
    }

    /**
     * Повертає історію розмови для поточного потоку.
     * * @param string $threadId Унікальний ID потоку розмови
     * @return array Масив повідомлень
     */
    public function getHistory(string $threadId): array
    {
        // У реальному застосуванні тут буде запит до бази даних або до Gemini API
        // для отримання історії повідомлень, пов'язаних з $threadId.
        // Для прикладу, повертаємо пустий масив.
        return [];
    }
}
