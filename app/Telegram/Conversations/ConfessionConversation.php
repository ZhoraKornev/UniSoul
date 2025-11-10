<?php

namespace App\Telegram\Conversations;

use App\Enums\BotCallback;
use App\Models\BotButton;
use App\Models\Branch;
use App\Models\Confession;
use App\Models\Employee;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardMarkup;

class ConfessionConversation extends BaseConversation
{
    public const CONFESSION_PREFIX = 'view_confession';

    public function start(Nutgram $bot): void
    {
        $confessions = Confession::query()->where('active', true)->get();

        $this->clearButtons();
        $this->menuText(__('telegram.select_confession'));

        foreach ($confessions as $confession) {
            \Log::info('ConfessionConversation  start', ['$confession->id' => $confession->id]);
            /** @var Confession $confession */
            $this->addButtonRow(
                InlineKeyboardButton::make(
                    text: $confession->emoji . ' ' . $confession->getTranslation('name', app()->getLocale()),
                    callback_data: $this->buildCallbackData(
                        self::CONFESSION_PREFIX,
                        $confession->id,
                        BotCallback::ViewConfession->name,
                        BotCallback::PREFIX_FOR_FUNCTION . BotCallback::ViewConfession->name
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

        $callBackDTO = $this->parseCallbackData($data);

        $confession = Confession::find($callBackDTO->confessionId);

        if (!$confession) {
            \Log::info('handleViewConfession', ['$confession' => $confession, '$callBackDTO->confessionId' => $callBackDTO->confessionId]);

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
                $confession->id,
                $button->callback_data->name,
                BotCallback::PREFIX_FOR_FUNCTION . $button->callback_data->name
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

    public function handleLearnAboutConfession(Nutgram $bot): void
    {
        $data = $bot->callbackQuery()->data ?? '';
        $parsed = $this->parseCallbackData($data);
        $confession = Confession::find($parsed->confessionId);

        if (!$confession) {
            $bot->answerCallbackQuery(text: __('telegram.error_confession_not_found'), show_alert: true);
            MainMenuConversation::begin($bot);
            return;
        }

        $learnButton = BotButton::where('callback_data', BotCallback::LearnAboutConfession)
            ->where('entity_type', Confession::class)
            ->where('entity_id', $confession->id)
            ->first();

        if (!$learnButton) {
            $bot->answerCallbackQuery(text: __('telegram.error_action'), show_alert: true);
            return;
        }

        $childButtons = BotButton::where('parent_id', $learnButton->id)
            ->where('entity_type', Confession::class)
            ->where('entity_id', $confession->id)
            ->where('active', true)
            ->orderBy('order')
            ->get();

        $this->clearButtons();
        $locale = app()->getLocale();
        $this->menuText($learnButton->getTranslation('text', $locale));

        foreach ($childButtons as $button) {
            $this->addButtonRow(
                InlineKeyboardButton::make(
                    text: $button->getTranslation('text', $locale),
                    callback_data: $button->callback_data->value . ':' . $confession->id . '@' . BotCallback::PREFIX_FOR_FUNCTION . $button->callback_data->name
                )
            );
        }

        $this->addButtonRow(
            InlineKeyboardButton::make(
                text: __('telegram.button_back'),
                callback_data: $this->buildCallbackData(
                    self::CONFESSION_PREFIX,
                    $confession->id,
                    'ViewConfession',
                    'handleViewConfession'
                )
            )
        );

        if ($bot->isCallbackQuery()) {
            $bot->answerCallbackQuery();
        }

        $this->showMenu();
    }

    public function handleConfessionMenuAction(Nutgram $bot): void
    {
        $data = $bot->callbackQuery()->data ?? '';
        $callbackDataDTO = $this->parseCallbackData($data);

        if (Confession::whereId($callbackDataDTO->confessionId)->exists() === false) {
            $bot->answerCallbackQuery(text: __('telegram.error_confession_not_found'), show_alert: true);
            MainMenuConversation::begin($bot);
            return;
        }

        $locale = app()->getLocale();
        /** @var BotButton|null $parent */
        $parent = BotButton::whereCallbackData($callbackDataDTO->action)
            ->where('entity_type', Confession::class)
            ->where('entity_id', $callbackDataDTO->confessionId)
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
                        $callbackDataDTO->confessionId,
                        $btn->callback_data->name,
                        BotCallback::PREFIX_FOR_FUNCTION . $btn->callback_data->name
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
        $data = $bot->callbackQuery()->data ?? '';
        $parsed = $this->parseCallbackData($data);
        $confession = Confession::find($parsed->confessionId);

        if (!$confession) {
            $bot->answerCallbackQuery(text: __('telegram.error_confession_not_found'), show_alert: true);
            MainMenuConversation::begin($bot);
            return;
        }

        $branches = Branch::where('confession_id', $confession->id)
            ->where('active', true)
            ->get();

        $this->clearButtons();
        $locale = app()->getLocale();

        if ($branches->isEmpty()) {
            $this->menuText(__('telegram.no_branches_found'));
        } else {
            $this->menuText(__('telegram.branches_for_confession', ['confession' => $confession->getTranslation('name', $locale)]));

            foreach ($branches as $branch) {
                /** @var Branch $branch */
                $this->addButtonRow(
                    InlineKeyboardButton::make(
                        text: "📍 " . $branch->getTranslation('name', $locale),
                        callback_data: $this->buildCallbackData(
                            'branch_details',
                            $confession->id,
                            'BranchDetails',
                            'handleBranchDetails',
                            $branch->id
                        )
                    )
                );
            }
        }

        $this->addButtonRow(
            InlineKeyboardButton::make(
                text: __('telegram.button_back'),
                callback_data: $this->buildCallbackData(
                    self::CONFESSION_PREFIX,
                    $confession->id,
                    'ViewConfession',
                    'handleViewConfession'
                )
            )
        );

        if ($bot->isCallbackQuery()) {
            $bot->answerCallbackQuery();
        }

        $this->showMenu();
    }

    public function handleBranchDetails(Nutgram $bot): void
    {
        $data = $bot->callbackQuery()->data ?? '';
        $parsed = $this->parseCallbackData($data);
        $confession = Confession::find($parsed->confessionId);
        $branch = Branch::find($parsed->actionId);

        if (!$confession || !$branch) {
            $bot->answerCallbackQuery(text: __('telegram.error_not_found'), show_alert: true);
            MainMenuConversation::begin($bot);
            return;
        }

        $this->clearButtons();
        $locale = app()->getLocale();

        $text = "📍 " . $branch->getTranslation('name', $locale) . "\n\n";
        $text .= "📧 " . $branch->getTranslation('address', $locale) . "\n";
        if ($branch->phone) {
            $text .= "📞 " . $branch->phone . "\n";
        }
        $this->menuText($text);

        $buttons = BotButton::where('entity_type', Branch::class)
            ->where('entity_id', $branch->id)
            ->where('active', true)
            ->orderBy('order')
            ->get();

        foreach ($buttons as $button) {
            /** @var BotButton $button */
            $this->addButtonRow(
                InlineKeyboardButton::make(
                    text: $button->getTranslation('text', $locale),
                    callback_data: $this->buildCallbackData(
                        $button->callback_data->value,
                        $confession->id,
                        $button->callback_data->name,
                        BotCallback::PREFIX_FOR_FUNCTION . $button->callback_data->name,
                        $branch->id
                    )
                )
            );
        }

        $this->addButtonRow(
            InlineKeyboardButton::make(
                text: __('telegram.button_back'),
                callback_data: $this->buildCallbackData(
                    'show_branches',
                    $confession->id,
                    'ShowBranches',
                    'handleShowBranches'
                )
            )
        );

        if ($bot->isCallbackQuery()) {
            $bot->answerCallbackQuery();
        }

        $this->showMenu();
    }

    public function handlePriestsList(Nutgram $bot): void
    {
        $data = $bot->callbackQuery()->data ?? '';
        \Log::info('handlePriestsList', ['data' => $data]);
        $parsed = $this->parseCallbackData($data);
        $confession = Confession::find($parsed->confessionId);

        if (!$confession) {
            $bot->answerCallbackQuery(text: __('telegram.error_confession_not_found'), show_alert: true);
            MainMenuConversation::begin($bot);

            return;
        }

        \Log::info('handlePriestsList  $confession', ['data' => $confession->toArray()]);
        $employees = Employee::whereHas('branch', function ($query) use ($confession) {
            $query->where('confession_id', $confession->id);
        })->where('is_available', true)->get();

        $this->clearButtons();
        $locale = app()->getLocale();

        if ($employees->isNotEmpty()) {
            $this->menuText(__('telegram.available_priests'));

            foreach ($employees as $employee) {
                $this->addButtonRow(
                    InlineKeyboardButton::make(
                        text: "👤 " . $employee->getTranslation('name', $locale),
                        callback_data: $this->buildCallbackData(
                            'employer_menu',
                            $confession->id,
                            'EmployerOpenMenu',
                            'handleEmployerOpenMenu',
                            $employee->id
                        )
                    )
                );
            }
        } else {
            $this->menuText(__('telegram.no_priests_available'));
        }

        $this->addButtonRow(
            InlineKeyboardButton::make(
                text: __('telegram.button_back'),
                callback_data: $this->buildCallbackData(
                    self::CONFESSION_PREFIX,
                    $confession->id,
                    'ViewConfession',
                    'handleViewConfession'
                )
            )
        );

        if ($bot->isCallbackQuery()) {
            $bot->answerCallbackQuery();
        }

        $this->showMenu();
    }

    public function handleEmployerOpenMenu(Nutgram $bot): void
    {
        $data = $bot->callbackQuery()->data ?? '';
        $parsed = $this->parseCallbackData($data);
        $confession = Confession::find($parsed->confessionId);
        $employee = Employee::find($parsed->actionId);

        if (!$confession || !$employee) {
            $bot->answerCallbackQuery(text: __('telegram.error_not_found'), show_alert: true);
            MainMenuConversation::begin($bot);
            return;
        }

        $locale = app()->getLocale();
        $this->clearButtons();

        $text = "👤 " . $employee->getTranslation('name', $locale) . "\n";
        $text .= "📋 " . $employee->getTranslation('position', $locale) . "\n";
        if ($employee->phone) {
            $text .= "📞 " . $employee->phone . "\n";
        }
        $this->menuText($text);

        $buttons = BotButton::where('entity_type', Employee::class)
            ->where('entity_id', $employee->id)
            ->where('active', true)
            ->orderBy('order')
            ->get();

        foreach ($buttons as $button) {
            $this->addButtonRow(
                InlineKeyboardButton::make(
                    text: $button->getTranslation('text', $locale),
                    callback_data: $this->buildCallbackData(
                        $button->callback_data->value,
                        $confession->id,
                        $button->callback_data->name,
                        BotCallback::PREFIX_FOR_FUNCTION . $button->callback_data->name,
                        $employee->id
                    )
                )
            );
        }

        $this->addButtonRow(
            InlineKeyboardButton::make(
                text: __('telegram.button_back'),
                callback_data: $this->buildCallbackData(
                    'priests_list',
                    $confession->id,
                    'PriestsList',
                    'handlePriestsList'
                )
            )
        );

        if ($bot->isCallbackQuery()) {
            $bot->answerCallbackQuery();
        }

        $this->showMenu();
    }

    public function handleReadUnceasingPsalter(Nutgram $bot): void
    {
        $this->handleConfessionAction($bot, 'read_unceasing_psalter');
    }

    public function handleMemorialService(Nutgram $bot): void
    {
        $this->handleConfessionAction($bot, 'memorial_service');
    }

    public function handleDonate(Nutgram $bot): void
    {
        // Оновлено: Перенаправлення на загальну дію пожертви
        $this->handleConfessionAction($bot, 'donate');
    }

    public function handleLearnVideosConfession(Nutgram $bot): void
    {
        $this->handleConfessionAction($bot, 'learn_videos_confession');
    }

    public function handleLearnImportantNotationAboutConfession(Nutgram $bot): void
    {
        $this->handleConfessionAction($bot, 'learn_important_notation_about_confession');
    }

    public function handleLearnBooksAboutConfession(Nutgram $bot): void
    {
        $this->handleConfessionAction($bot, 'learn_books_about_confession');
    }

    /**
     * Оновлена функція, яка перевіряє необхідність пожертви
     * або починає розмову про підтримку.
     */
    public function handleContactEmployer(Nutgram $bot): void
    {
        $data = $bot->callbackQuery()->data ?? '';
        $parsed = $this->parseCallbackData($data);
        $confession = Confession::find($parsed->confessionId);
        $employee = Employee::find($parsed->actionId);

        if (!$confession || !$employee) {
            $bot->answerCallbackQuery(text: __('telegram.error_not_found'), show_alert: true);
            MainMenuConversation::begin($bot);
            return;
        }

        $button = BotButton::where('callback_data', BotCallback::ContactEmployer)
            ->where('entity_type', Employee::class)
            ->where('entity_id', $employee->id)
            ->first();

        $this->clearButtons();

        // 1. ПЕРЕВІРКА: Чи потрібна пожертва
        if ($button && $button->need_donations) {

            if ($bot->isCallbackQuery()) {
                $bot->answerCallbackQuery();
            }

            // Перенаправляємо на дію пожертви
            $bot->sendMessage(
                text: __('telegram.donation_required_message'),
                reply_markup: InlineKeyboardMarkup::make()->addRow(
                    InlineKeyboardButton::make(
                        text: __('telegram.button_donate'),
                        callback_data: $this->buildCallbackData(
                            BotCallback::Donate->value,
                            $confession->id,
                            BotCallback::Donate->name,
                            'handleDonate'
                        )
                    )
                )
            );

            // Показуємо меню назад для повернення
            $this->menuText(__('telegram.select_action'));
            $this->addButtonRow(
                InlineKeyboardButton::make(
                    text: __('telegram.button_back'),
                    callback_data: $this->buildCallbackData(
                        'employer_menu',
                        $confession->id,
                        'EmployerOpenMenu',
                        'handleEmployerOpenMenu',
                        $employee->id
                    )
                )
            );
            $this->showMenu();

            return;
        }

        // 2. ДІЯ: Якщо пожертва не потрібна, починаємо розмову підтримки.
        if ($bot->isCallbackQuery()) {
            $bot->answerCallbackQuery();
        }

        /** @var SupportConversation $supportConversation */
        SupportConversation::beginWithParams($bot, $employee->branch_id, $employee->id);
    }

    private function handleConfessionAction(Nutgram $bot, string $action): void
    {
        $data = $bot->callbackQuery()->data ?? '';
        $parsed = $this->parseCallbackData($data);
        $id = $parsed->confessionId;
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
                callback_data: $this->buildCallbackData(
                    'confession',
                    $confession->id,
                    'ViewConfession',
                    'handleViewConfession'
                )
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
