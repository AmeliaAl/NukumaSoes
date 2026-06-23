<?php

namespace App\Enums;

enum UserRole: string
{
    case ADMIN = 'admin';
    case PEMILIK = 'pemilik';

    public function getLabel(): string
    {
        return match($this) {
            self::ADMIN => 'Administrator',
            self::PEMILIK => 'Pemilik',
        };
    }

    public static function options(): array
    {
        return [
            self::ADMIN->value => self::ADMIN->getLabel(),
            self::PEMILIK->value => self::PEMILIK->getLabel(),
        ];
    }
}
