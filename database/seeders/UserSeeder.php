<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Pastikan role sudah ada
        $superRole = Role::findByName('super-admin', 'web');
        $adminRole = Role::findByName('admin', 'web');
        $demoRole = Role::findByName('demo', 'web');

        // ============================================
        // SUPER ADMIN
        // ============================================
        $superAdmin = User::firstOrCreate(
            ['email' => 'superadmin@gmail.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
                'status' => 'active',
            ]
        );

        if (!$superAdmin->hasRole($superRole->name)) {
            $superAdmin->assignRole($superRole);
        }

        // ============================================
        // ADMIN
        // ============================================
        $admin = User::firstOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password'),
                'status' => 'active',
            ]
        );

        if (!$admin->hasRole($adminRole->name)) {
            $admin->assignRole($adminRole);
        }

        // ============================================
        // DEMO USER (tanpa role / optional admin)
        // ============================================
        $demo = User::firstOrCreate(
            ['email' => 'demo@gmail.com'],
            [
                'name' => 'Demo User',
                'password' => Hash::make('password'),
                'status' => 'active',
            ]
        );

        $demo->assignRole($demoRole);

        // Output info
        $this->command->info('✅ Default users created successfully:');
        $this->command->info('   - Super Admin: superadmin@gmail.com');
        $this->command->info('   - Admin: admin@gmail.com');
        $this->command->info('   - Demo: demo@gmail.com');
        $this->command->info('   Default password: password');
    }
}
