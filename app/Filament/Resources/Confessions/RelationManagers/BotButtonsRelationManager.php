<?php

namespace App\Filament\Resources\Confessions\RelationManagers;

use App\Enums\BotCallback;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Resources\RelationManagers\RelationManager;

class BotButtonsRelationManager extends RelationManager
{
    protected static string $relationship = 'botButtons';
    protected static ?string $recordTitleAttribute = 'text';

    public function form(Schema $schema): Schema
    {
        $callbackOptions = collect(BotCallback::cases())
            ->mapWithKeys(fn ($enum) => [$enum->value => $enum->label()])
            ->toArray();

        return $schema
            ->schema([
                // Parent select (safe mapping via models)
                Forms\Components\Select::make('parent_id')
                    ->label('Parent Button')
                    ->options(function () {
                        $owner = $this->getOwnerRecord();
                        /** @var \App\Models\Confession $owner */
                        return \App\Models\BotButton::query()
                            ->where('entity_type', $owner::class)
                            ->where('entity_id', $owner->id)
                            ->get()
                            ->mapWithKeys(function ($button) {
                                /** @var \App\Models\BotButton $button */
                                $label = $button->getTranslation('text', app()->getLocale()) ?: '[No label]';
                                return [$button->id => $label];
                            })
                            ->toArray();
                    })

                    ->searchable()
                    ->nullable()
                    ->preload()
                    ->placeholder('— No Parent —'),

                Forms\Components\Select::make('callback_data')
                    ->label('Callback Action')
                    ->options($callbackOptions)
                    ->required()
                    ->searchable(),

                Forms\Components\TextInput::make('order')
                    ->numeric()
                    ->default(0)
                    ->required(),

                Forms\Components\KeyValue::make('text')
                    ->label('Translations')
                    ->addActionLabel('Add language')
                    ->helperText('Example: en → Home, uk → Головна'),
            ]);
    }

    public function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable(),

                Tables\Columns\TextColumn::make('text')
                    ->label('Label')
                    ->formatStateUsing(fn ($state) => is_array($state)
                        ? ($state[app()->getLocale()] ?? json_encode($state))
                        : (string) $state)
                    ->searchable(),

                // Parent column — read related model (safe) and render its translation
                Tables\Columns\TextColumn::make('parent_id')
                    ->label('Parent')
                    ->formatStateUsing(function ($state, $record) {
                        if (!$record || !$record->parent) {
                            return null;
                        }

                        /** @var \App\Models\BotButton $parent */
                        $parent = $record->parent;

                        return $parent->getTranslation('text', app()->getLocale()) ?: '[No label]';
                    })
                    ->toggleable(),

                Tables\Columns\TextColumn::make('callback_data')
                    ->label('Callback')
                    ->searchable(),

                Tables\Columns\TextColumn::make('order')->sortable(),
            ])
            ->defaultSort('order')
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->headerActions([
                CreateAction::make(),
            ]);
    }
}
