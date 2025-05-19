<?php

namespace App\Enums;

enum PermissionEnum: string
{
    // === USER & ROLE ===
    case CREATE_USER = 'create user';
    case EDIT_USER = 'edit user';
    case DELETE_USER = 'delete user';
    case VIEW_USER = 'view user';

        // === STORE ===
    case CREATE_STORE = 'create store';
    case EDIT_STORE = 'edit store';
    case DELETE_STORE = 'delete store';
    case VIEW_STORE = 'view store';

        // === PRESENTS (ABSENSI) ===
    case CREATE_PRESENT = 'create present';
    case EDIT_PRESENT = 'edit present';
    case DELETE_PRESENT = 'delete present';
    case VIEW_PRESENT = 'view present';

        // === SCHEDULE ===
    case CREATE_SCHEDULE = 'create schedule';
    case EDIT_SCHEDULE = 'edit schedule';
    case DELETE_SCHEDULE = 'delete schedule';
    case VIEW_SCHEDULE = 'view schedule';

        // === LEAVE (CUTI) ===
    case CREATE_LEAVE = 'create leave';
    case EDIT_LEAVE = 'edit leave';
    case DELETE_LEAVE = 'delete leave';
    case VIEW_LEAVE = 'view leave';

        // === INVOICE ===
    case CREATE_INVOICE = 'create invoice';
    case EDIT_INVOICE = 'edit invoice';
    case DELETE_INVOICE = 'delete invoice';
    case VIEW_INVOICE = 'view invoice';

        // === INVOICE PAYMENT ===
    case CREATE_INVOICE_PAYMENT = 'create invoice payment';
    case EDIT_INVOICE_PAYMENT = 'edit invoice payment';
    case DELETE_INVOICE_PAYMENT = 'delete invoice payment';
    case VIEW_INVOICE_PAYMENT = 'view invoice payment';



    public static function all(): array
    {
        return array_column(self::cases(), 'value');
    }
}
