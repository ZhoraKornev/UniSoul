<?php

namespace App\Telegram\Conversations;

use App\Enums\BotCallback;
use App\Models\BotButton;
use App\Models\Confession;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;

class ConfessionConversation extends BaseConversation
{
    public const CONFESSION_PREFIX = 'view_confession';

    public function start(Nutgram $bot): void
    {
        $confessions = Confession::query()->where('active', true)->get();

        $this->clearButtons();
        $this->menuText(__('telegram.select_confession'));

        foreach ($confessions as $confession) {
            /** @var Confession $confession */
            $this->addButtonRow(
                InlineKeyboardButton::make(
                    text: $confession->emoji . ' ' . $confession->getTranslation('name', app()->getLocale()),
                    callback_data: $this->buildCallbackData(
                        self::CONFESSION_PREFIX,
                        BotCallback::PREFIX_FOR_FUNCTION . BotCallback::ViewConfession->name,
                        $confession->id
                    )
                )
            );
        }

        $this->addButtonRow(
            InlineKeyboardButton::make(
                text: __('telegram.button_back'),
                callback_data: BotCallback::MainMenu->value . '@handleMainMenu'
            )
        );

        if ($bot->isCallbackQuery()) {
            $bot->answerCallbackQuery();
        }

        $this->showMenu();
    }

    public function handleViewConfession(Nutgram $bot): void
    {
        $data = $bot->callbackQuery()->data ?? '';
        \Log::info('handleViewConfession', ['callback_data' => $data]);

        $parsed = $this->parseCallbackData($data);
        \Log::info('Parsed callback data', $parsed);

        $id = $parsed['id'];
        $confession = Confession::find($id);
        \Log::info('Confession lookup', ['id' => $id, 'found' => $confession ? 'yes' : 'no']);

        if (!$confession) {
            $bot->answerCallbackQuery(text: __('telegram.error_confession_not_found'), show_alert: true);
            MainMenuConversation::begin($bot);
            return;
        }

        /** @var Confession $confession */
        $locale = app()->getLocale();
        $this->clearButtons();
        // Add timestamp to ensure content is different from previous calls
        $this->menuText(
            $confession->emoji . ' ' . $confession->getTranslation('name', $locale) . "\n\n" .
            $confession->getTranslation('description', $locale) . "\n\n📅 " . now()->format('H:i:s')
        );

        /** @var BotButton|null $confessionRootButton */
        $confessionRootButton = BotButton::whereCallbackData(BotCallback::ConfessionListMenu->value)
            ->select('id')
            ->first();
        $confessionRootButtonId = $confessionRootButton?->id;
        \Log::info('Root button lookup', ['root_button_id' => $confessionRootButtonId]);

        if (!$confessionRootButtonId) {
            return;
        }

        $buttons = BotButton::query()
            ->where('entity_type', Confession::class)
            ->where('entity_id', $confession->id)
            ->where('parent_id', $confessionRootButtonId)
            ->where('active', true)
            ->orderBy('order')
            ->get();
        \Log::info('Buttons found', ['count' => $buttons->count()]);

        foreach ($buttons as $button) {
            /** @var BotButton $button */
            $callbackData = $this->buildCallbackData(
                $button->callback_data->value,
                BotCallback::PREFIX_FOR_FUNCTION . $button->callback_data->name,
                $confession->id
            );
            \Log::info('Adding button', ['text' => $button->getTranslation('text', $locale), 'callback' => $callbackData]);

            $this->addButtonRow(
                InlineKeyboardButton::make(
                    text: $button->getTranslation('text', $locale),
                    callback_data: $callbackData,
                )
            );
        }

        if ($bot->isCallbackQuery()) {
            $bot->answerCallbackQuery();
        }

        $this->showMenu();
    }

    //✨ Про конфесію
    public function handleLearnAboutConfession(Nutgram $bot): void
    {
        $this->handleConfessionAction($bot, '✨ Про конфесію');
    }

    public function handleConfessionMenuAction(Nutgram $bot): void
    {
        $data = $bot->callbackQuery()->data ?? '';
        $parsed = $this->parseCallbackData($data);
        $id = $parsed['id'];
        $confession = Confession::find($id);

        if (!$confession) {
            $bot->answerCallbackQuery(text: __('telegram.error_confession_not_found'), show_alert: true);
            MainMenuConversation::begin($bot);
            return;
        }

        /** @var Confession $confession */
        $locale = app()->getLocale();
        /** @var BotButton|null $parent */
        $parent = BotButton::whereCallbackData($parsed['action'])
            ->where('entity_type', Confession::class)
            ->where('entity_id', $confession->id)
            ->where('active', true)
            ->first();

        if (!$parent) {
            $bot->answerCallbackQuery(text: __('telegram.error_action'), show_alert: true);
            MainMenuConversation::begin($bot);
            return;
        }

        /** @var BotButton $parent */
        $childrenButtons = BotButton::where('parent_id', $parent->id)->orderBy('order')->get();
        \Log::info('Children buttons', ['count' => $childrenButtons->count()]);

        $this->clearButtons();
        $this->menuText($parent->getTranslation('text', $locale));

        foreach ($childrenButtons as $btn) {
            /** @var BotButton $btn */
            $this->addButtonRow(
                InlineKeyboardButton::make(
                    text: $btn->getTranslation('text', $locale),
                    callback_data: $this->buildCallbackData(
                        $btn->callback_data->value,
                        BotCallback::PREFIX_FOR_FUNCTION . $btn->callback_data->name,
                        $confession->id
                    )
                )
            );
        }


        if ($bot->isCallbackQuery()) {
            $bot->answerCallbackQuery();
        }

        $this->showMenu();
    }

    public function handleSorokoust(Nutgram $bot): void
    {
        $this->handleConfessionAction($bot, 'sorokoust');
    }

    public function handleLightACandle(Nutgram $bot): void
    {
        $this->handleConfessionAction($bot, 'light_a_candle');
    }

    public function handleSubmitPrayerNote(Nutgram $bot): void
    {
        $this->handleConfessionAction($bot, 'submit_prayer_note');
    }

    public function handleReadAkathists(Nutgram $bot): void
    {
        $this->handleConfessionAction($bot, 'read_akathists');
    }

    public function handleShowBranches(Nutgram $bot): void
    {
        $this->handleConfessionAction($bot, 'show_branches');
    }

    public function handleReadUnceasingPsalter(Nutgram $bot): void
    {
        $this->handleConfessionAction($bot, 'read_unceasing_psalter');
    }

    public function handleMemorialService(Nutgram $bot): void
    {
        $this->handleConfessionAction($bot, 'memorial_service');
    }

    public function handlePriestsList(Nutgram $bot): void
    {
        $this->handleConfessionAction($bot, 'priests_list');
    }

    public function handleDonate(Nutgram $bot): void
    {
        $this->handleConfessionAction($bot, 'donate');
    }

    private function handleConfessionAction(Nutgram $bot, string $action): void
    {
        $data = $bot->callbackQuery()->data ?? '';
        $parsed = $this->parseCallbackData($data);
        $id = $parsed['id'];
        $confession = Confession::find($id);

        if (!$confession) {
            $bot->answerCallbackQuery(text: __('telegram.error_confession_not_found'), show_alert: true);
            MainMenuConversation::begin($bot);
            return;
        }

        /** @var Confession $confession */
        $this->clearButtons();
        $this->menuText(__('telegram.confession_actions.' . $action) . ' - ' . __('telegram.coming_soon'));

        $this->addButtonRow(
            InlineKeyboardButton::make(
                text: __('telegram.button_back'),
                callback_data: $this->buildCallbackData('view_confession', 'handleViewConfession', $confession->id)
            )
        );

        if ($bot->isCallbackQuery()) {
            $bot->answerCallbackQuery();
        }

        $this->showMenu();
    }

    public function handleMainMenu(Nutgram $bot): void
    {
        MainMenuConversation::begin($bot);
    }

    public function handleBackButton(Nutgram $bot): void
    {
        $this->start($bot);
    }
}
