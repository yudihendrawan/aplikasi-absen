<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Enums\PermissionEnum;
use App\Enums\RoleEnum;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        foreach (PermissionEnum::all() as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $adminRole = Role::firstOrCreate(['name' => RoleEnum::ADMIN->value]);
        $adminRole->syncPermissions(Permission::all());

        $salesRole = Role::firstOrCreate(['name' => RoleEnum::SALES->value]);

        $salesPermissions = [
            PermissionEnum::VIEW_STORE->value,
            PermissionEnum::VIEW_SCHEDULE->value,
            PermissionEnum::CREATE_PRESENT->value,
            PermissionEnum::VIEW_PRESENT->value,
            PermissionEnum::CREATE_INVOICE_PAYMENT->value,
            PermissionEnum::VIEW_INVOICE->value,
            PermissionEnum::VIEW_INVOICE_PAYMENT->value,
            PermissionEnum::CREATE_LEAVE->value,
            PermissionEnum::VIEW_LEAVE->value,
        ];

        $salesRole->syncPermissions($salesPermissions);
    }
}
