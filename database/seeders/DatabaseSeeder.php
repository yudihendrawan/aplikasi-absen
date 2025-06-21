<?php

namespace Database\Seeders;

use App\Models\Invoice;
use App\Models\InvoicePayment;
use App\Models\Leave;
use App\Models\Present;
use App\Models\Schedule;
use App\Models\Store;
use App\Models\User;
use Database\Factories\VisitStoreFactory;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        Store::factory(5)->create();

        $this->call(RolePermissionSeeder::class);
        User::factory(10)->create()->each(function ($user) {
            $user->assignRole('admin');
        });

        User::factory(10)->create()->each(function ($user) {
            $user->assignRole('sales');
        });
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $user->assignRole('admin');
        // Present::factory(20)->create();
        // Leave::factory(10)->create();
        // Schedule::factory(30)->create([
        //     'created_by' => $user->id,
        // ]);

        // Invoice::factory(10)->create();
        // InvoicePayment::factory(15)->create();
    }
}
