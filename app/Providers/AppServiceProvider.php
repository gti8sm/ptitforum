<?php

namespace App\Providers;

use App\Support\Settings;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (! Schema::hasTable('settings')) {
            return;
        }

        $siteTitle = Settings::get(Settings::KEY_SITE_TITLE);

        if (filled($siteTitle)) {
            config(['app.name' => $siteTitle]);
        }

        $smtpHost = Settings::get(Settings::KEY_SMTP_HOST);
        $smtpPort = Settings::get(Settings::KEY_SMTP_PORT);
        $smtpUsername = Settings::get(Settings::KEY_SMTP_USERNAME);
        $smtpPassword = Settings::get(Settings::KEY_SMTP_PASSWORD);
        $smtpEncryption = Settings::get(Settings::KEY_SMTP_ENCRYPTION);

        $mailFromAddress = Settings::get(Settings::KEY_MAIL_FROM_ADDRESS);
        $mailFromName = Settings::get(Settings::KEY_MAIL_FROM_NAME);

        if (filled($smtpHost)) {
            config(['mail.mailers.smtp.host' => $smtpHost]);
        }

        if (filled($smtpPort)) {
            config(['mail.mailers.smtp.port' => (int) $smtpPort]);
        }

        if (filled($smtpUsername)) {
            config(['mail.mailers.smtp.username' => $smtpUsername]);
        }

        if (filled($smtpPassword)) {
            config(['mail.mailers.smtp.password' => $smtpPassword]);
        }

        if (! is_null($smtpEncryption)) {
            config(['mail.mailers.smtp.encryption' => $smtpEncryption ?: null]);
        }

        if (filled($mailFromAddress)) {
            config(['mail.from.address' => $mailFromAddress]);
        }

        if (filled($mailFromName)) {
            config(['mail.from.name' => $mailFromName]);
        }
    }
}
