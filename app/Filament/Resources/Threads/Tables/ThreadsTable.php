<?php

namespace App\Filament\Resources\Threads\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class ThreadsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('last_activity_at', 'desc')
            ->columns([
                TextColumn::make('group.name')
                    ->label('Groupe')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('creator.name')
                    ->label('Créé par')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('title')
                    ->label('Titre')
                    ->searchable(),
                IconColumn::make('is_pinned')
                    ->label('Épinglé')
                    ->boolean(),
                IconColumn::make('is_locked')
                    ->label('Verrouillé')
                    ->boolean(),
                TextColumn::make('posts_count')
                    ->label('Messages')
                    ->counts('posts')
                    ->sortable(),
                TextColumn::make('last_activity_at')
                    ->label('Dernière activité')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Créé le')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Mis à jour le')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TernaryFilter::make('is_pinned')
                    ->label('Épinglé'),
                TernaryFilter::make('is_locked')
                    ->label('Verrouillé'),
                SelectFilter::make('group_id')
                    ->label('Groupe')
                    ->relationship('group', 'name'),
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
}
