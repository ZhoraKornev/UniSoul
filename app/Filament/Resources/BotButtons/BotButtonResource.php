<?php

namespace App\Filament\Resources\BotButtons;

use App\Enums\BotCallback;
use App\Filament\Resources\BotButtons\Pages\ManageBotButtons;
use App\Models\BotButton;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class BotButtonResource extends Resource
{
    protected static ?string $model = BotButton::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChatBubbleBottomCenterText;

    protected static ?string $recordTitleAttribute = 'text';

    public static function form(Schema $schema): Schema
    {
        $callbackOptions = collect(BotCallback::cases())
            ->mapWithKeys(fn ($enum) => [$enum->value => $enum->label()])
            ->toArray();

        return $schema
            ->components([
                TextInput::make('parent_id')
                    ->numeric(),
                Tabs::make('Translations')
                    ->tabs(collect(config('app.supported_languages'))->map(fn($lang) =>
                    Tabs\Tab::make(strtoupper($lang))
                        ->schema([
                            TextInput::make("text.{$lang}")
                                ->label("Text ({$lang})")
                                ->required($lang === 'en'),
                        ]))->toArray()),
                Select::make('callback_data')
                    ->options($callbackOptions)
                    ->required()
                    ->searchable()
                    ->placeholder('Select callback data'),
                TextInput::make('order')
                    ->required()
                    ->numeric()
                    ->default(0),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('text')
            ->columns([
                TextColumn::make('parent_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('text')
                    ->searchable(),
                TextColumn::make('callback_data')
                    ->searchable(),
                TextColumn::make('order')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageBotButtons::route('/'),
        ];
    }


}
