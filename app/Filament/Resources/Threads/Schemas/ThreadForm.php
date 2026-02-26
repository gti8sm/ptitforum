<?php

namespace App\Filament\Resources\Threads\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ThreadForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('group_id')
                    ->label('Groupe')
                    ->relationship('group', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                TextInput::make('title')
                    ->label('Titre')
                    ->required(),
                Textarea::make('body')
                    ->label('Description')
                    ->columnSpanFull(),
                Toggle::make('is_pinned')
                    ->label('Épinglé')
                    ->required(),
                Toggle::make('is_locked')
                    ->label('Verrouillé')
                    ->required(),
            ]);
    }
}
