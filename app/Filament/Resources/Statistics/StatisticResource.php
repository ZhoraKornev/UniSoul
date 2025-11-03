<?php

namespace App\Filament\Resources\Statistics;

use App\Models\Statistic;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class StatisticResource extends Resource
{
    protected static ?string $model = Statistic::class;

    protected static string|null|BackedEnum $navigationIcon = 'heroicon-o-chart-bar';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->sortable(),
                Tables\Columns\TextColumn::make('chat_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('action')
                    ->searchable(),
                Tables\Columns\TextColumn::make('category')
                    ->searchable(),
                Tables\Columns\TextColumn::make('collected_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category'),
                Tables\Filters\SelectFilter::make('action'),
            ])
            ->defaultSort('collected_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStatistics::route('/'),
        ];
    }
}
