<?php

namespace App\Enums;

enum Role : string
{
    case USER = 'user';
    case ADMIN = 'admin';
    case EMPLOYEE = 'employee';

    public function label(): string
    {
        return match($this) {
            self::USER => 'Người dùng',
            self::ADMIN => 'Quản trị viên',
            self::EMPLOYEE => 'Nhân viên',
        };
    }
}
