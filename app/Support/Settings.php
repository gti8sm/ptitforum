<?php

namespace App\Support;

use App\Models\Setting;
use Illuminate\Support\Facades\Crypt;

class Settings
{
    public const KEY_SITE_TITLE = 'site.title';

    public const KEY_SMTP_HOST = 'smtp.host';
    public const KEY_SMTP_PORT = 'smtp.port';
    public const KEY_SMTP_USERNAME = 'smtp.username';
    public const KEY_SMTP_PASSWORD = 'smtp.password';
    public const KEY_SMTP_ENCRYPTION = 'smtp.encryption';

    public const KEY_MAIL_FROM_ADDRESS = 'mail.from.address';
    public const KEY_MAIL_FROM_NAME = 'mail.from.name';

    public static function get(string $key, mixed $default = null): mixed
    {
        $row = Setting::query()->where('key', $key)->first();

        if (! $row) {
            return $default;
        }

        $value = $row->value;

        if ($row->is_encrypted && filled($value)) {
            try {
                $value = Crypt::decryptString($value);
            } catch (\Throwable $e) {
                return $default;
            }
        }

        return $value ?? $default;
    }

    public static function set(string $key, mixed $value, bool $encrypt = false): void
    {
        $valueToStore = $value;

        if (is_array($valueToStore) || is_object($valueToStore)) {
            $valueToStore = json_encode($valueToStore);
        }

        if ($encrypt && filled($valueToStore)) {
            $valueToStore = Crypt::encryptString((string) $valueToStore);
        }

        Setting::query()->updateOrCreate(
            ['key' => $key],
            [
                'value' => $valueToStore,
                'is_encrypted' => $encrypt,
            ],
        );
    }
}
