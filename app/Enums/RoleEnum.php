<?php


namespace App\Enums;

enum RoleEnum: string
{
    case ADMIN = 'admin';
    case SALES = 'sales';


    public static function all(): array
    {

        return array_column(self::cases(), 'value');
    }
}
