<?php

namespace App\Support;

class PhonePrivacy
{
    public static function hash(?string $phone): ?string
    {
        if ($phone === null) {
            return null;
        }

        $normalized = preg_replace('/\D+/', '', $phone) ?? '';
        if ($normalized === '') {
            return null;
        }

        $key = (string) config('app.key', 'fallback-app-key');

        return hash_hmac('sha256', $normalized, $key);
    }
}
