<?php

namespace App\Filament\Resources\Invitations\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class InvitationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('email')
                    ->label('Adresse e-mail')
                    ->email()
                    ->required(),
                Select::make('group_id')
                    ->label('Groupe')
                    ->relationship('group', 'name')
                    ->searchable()
                    ->preload(),
                DateTimePicker::make('expires_at')
                    ->label('Expire le'),
            ]);
    }
}
