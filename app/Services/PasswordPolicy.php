<?php

namespace App\Services;

class PasswordPolicy
{
    public static function rules(bool $confirmed = false): array
    {
        $rules = [
            'required',
            'string',
            'min:8',
            'regex:/[0-9]/',
            'regex:/[A-Z]/',
            'regex:/[a-z]/',
            'regex:/[!@#$%^&*()\-_+=]/',
            'regex:/^\S+$/',
        ];

        if ($confirmed) {
            $rules[] = 'confirmed';
        }

        return $rules;
    }

    public static function rulesWithConfirmation(): array
    {
        return self::rules(true);
    }

    public static function message(): string
    {
        return 'Password must be at least 8 characters long, include at least 1 number, 1 uppercase letter, 1 lowercase letter, 1 special character, and no spaces.';
    }
}
