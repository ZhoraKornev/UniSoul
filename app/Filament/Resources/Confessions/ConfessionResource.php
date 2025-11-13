<?php

namespace App\Filament\Resources\Confessions;

use App\Filament\Resources\Confessions\RelationManagers\BotButtonsRelationManager;
use App\Models\Confession;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class ConfessionResource extends Resource
{
    protected static ?string $model = Confession::class;

    protected static string|null|BackedEnum $navigationIcon = 'heroicon-o-building-library';

    public static function form(Schema $schema): Schema
    {
        $supportedLanguages = config('app.supported_languages', ['en', 'uk']);

        return $schema
            ->schema([
                Tabs::make('Tabs')
                    ->tabs([
                        Tabs\Tab::make('Translations')
                            ->schema([
                                Tabs::make('Language Tabs')
                                    ->tabs(collect($supportedLanguages)->map(fn ($lang) => Tabs\Tab::make(strtoupper($lang))
                                        ->schema([
                                            TextInput::make("name.{$lang}")
                                                ->label("Name ({$lang})")
                                                ->required($lang === 'en')
                                                ->maxLength(255),

                                            TextInput::make("full_name.{$lang}")
                                                ->label("Full Name ({$lang})")
                                                ->required($lang === 'en')
                                                ->maxLength(255),

                                            Textarea::make("description.{$lang}")
                                                ->rows(15)
                                                ->label("Description ({$lang})"),
                                        ]))->toArray()),
                            ]),

                        Tabs\Tab::make('General')
                            ->schema([
                                TextInput::make('emoji')
                                    ->maxLength(10)
                                    ->label('Emoji'),

                                Toggle::make('active')
                                    ->default(true)
                                    ->label('Active Status'),
                            ]),

                        Tabs\Tab::make('Settings')
                            ->schema([
                                Select::make('countries')
                                    ->label('Available Countries')
                                    ->relationship('countries', 'name')
                                    ->multiple()
                                    ->preload()
                                    ->searchable()
                                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->name),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->sortable(),

                Tables\Columns\TextColumn::make('name')
                    ->getStateUsing(fn ($record) => $record->getTranslation('name', app()->getLocale()))
                    ->searchable(),

                Tables\Columns\TextColumn::make('emoji'),

                Tables\Columns\IconColumn::make('active')
                    ->boolean(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('active'),
            ])
            ->recordActions([
                EditAction::make(),
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
            'index' => Pages\ListConfessions::route('/'),
            'create' => Pages\CreateConfession::route('/create'),
            'edit' => Pages\EditConfession::route('/{record}/edit'),
        ];
    }

    public static function getRelations(): array
    {
        return [
            BotButtonsRelationManager::class,
        ];
    }
}
