<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nom')
                    ->required(),
                TextInput::make('email')
                    ->label('Adresse e-mail')
                    ->email()
                    ->required(),
                DateTimePicker::make('email_verified_at')
                    ->label('E-mail vérifié le'),
                TextInput::make('password')
                    ->label('Mot de passe')
                    ->password()
                    ->required(fn (string $operation): bool => $operation === 'create')
                    ->dehydrated(fn (?string $state): bool => filled($state))
                    ->dehydrateStateUsing(fn (string $state): string => Hash::make($state)),
                Select::make('role')
                    ->label('Rôle')
                    ->required()
                    ->options([
                        'member' => 'Membre',
                        'moderator' => 'Modérateur',
                        'admin' => 'Admin',
                    ])
                    ->default('member'),
            ]);
    }
}
