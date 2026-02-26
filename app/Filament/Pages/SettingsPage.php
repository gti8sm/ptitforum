<?php

namespace App\Filament\Pages;

use App\Support\Settings;
use Filament\Actions\Action;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class SettingsPage extends Page
{
    use InteractsWithForms;

    protected static ?string $navigationLabel = 'Paramètres';

    protected static string|\UnitEnum|null $navigationGroup = 'Configuration';

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static ?string $slug = 'settings';

    protected string $view = 'filament.pages.settings-page';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'site_title' => Settings::get(Settings::KEY_SITE_TITLE, config('app.name')),

            'smtp_host' => Settings::get(Settings::KEY_SMTP_HOST, config('mail.mailers.smtp.host')),
            'smtp_port' => Settings::get(Settings::KEY_SMTP_PORT, config('mail.mailers.smtp.port')),
            'smtp_username' => Settings::get(Settings::KEY_SMTP_USERNAME, config('mail.mailers.smtp.username')),
            'smtp_password' => null,
            'smtp_encryption' => Settings::get(Settings::KEY_SMTP_ENCRYPTION, config('mail.mailers.smtp.encryption')),

            'mail_from_address' => Settings::get(Settings::KEY_MAIL_FROM_ADDRESS, config('mail.from.address')),
            'mail_from_name' => Settings::get(Settings::KEY_MAIL_FROM_NAME, config('mail.from.name')),
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                Placeholder::make('site_heading')
                    ->label('')
                    ->content('Site'),
                TextInput::make('site_title')
                    ->label('Titre du site')
                    ->required()
                    ->maxLength(255),

                Placeholder::make('smtp_heading')
                    ->label('')
                    ->content('E-mails (SMTP)'),
                TextInput::make('smtp_host')
                    ->label('Hôte SMTP')
                    ->maxLength(255),
                TextInput::make('smtp_port')
                    ->label('Port SMTP')
                    ->numeric(),
                TextInput::make('smtp_username')
                    ->label('Utilisateur SMTP')
                    ->maxLength(255),
                TextInput::make('smtp_password')
                    ->label('Mot de passe SMTP')
                    ->password()
                    ->revealable(),
                Select::make('smtp_encryption')
                    ->label('Chiffrement')
                    ->options([
                        '' => 'aucun',
                        'tls' => 'tls',
                        'ssl' => 'ssl',
                    ]),
                TextInput::make('mail_from_address')
                    ->label('Adresse expéditeur')
                    ->email()
                    ->maxLength(255),
                TextInput::make('mail_from_name')
                    ->label('Nom expéditeur')
                    ->maxLength(255),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('save')
                ->label('Enregistrer')
                ->action(fn () => $this->save()),
        ];
    }

    public function save(): void
    {
        $state = $this->form->getState();

        Settings::set(Settings::KEY_SITE_TITLE, $state['site_title'] ?? null);

        Settings::set(Settings::KEY_SMTP_HOST, $state['smtp_host'] ?? null);
        Settings::set(Settings::KEY_SMTP_PORT, $state['smtp_port'] ?? null);
        Settings::set(Settings::KEY_SMTP_USERNAME, $state['smtp_username'] ?? null);
        Settings::set(Settings::KEY_SMTP_ENCRYPTION, $state['smtp_encryption'] ?? null);

        if (filled($state['smtp_password'] ?? null)) {
            Settings::set(Settings::KEY_SMTP_PASSWORD, $state['smtp_password'] ?? null, true);
        }

        Settings::set(Settings::KEY_MAIL_FROM_ADDRESS, $state['mail_from_address'] ?? null);
        Settings::set(Settings::KEY_MAIL_FROM_NAME, $state['mail_from_name'] ?? null);

        Notification::make()
            ->title('Paramètres enregistrés')
            ->success()
            ->send();

        $this->mount();
    }
}
