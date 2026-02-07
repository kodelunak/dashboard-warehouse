<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // =========================
        // ROLES
        // =========================
        $roles = [
            'super-admin',
            'admin',
            'demo',
        ];

        // =========================
        // PERMISSIONS
        // =========================
        $permissions = [
            // User management
            'users.view', 'users.create', 'users.update', 'users.delete',

            // Roles & Permissions
            'roles.view', 'roles.create', 'roles.update', 'roles.delete',
            'permissions.view', 'permissions.create', 'permissions.update', 'permissions.delete',
            'user-roles.update',

            // Master Products
            'products.view', 'products.create', 'products.update', 'products.delete',
            'product-batches.view', 'product-batches.create', 'product-batches.update', 'product-batches.delete',
            'product-batches.report',

            // Penjualan
            'monthly-target.view', 'monthly-target.create', 'monthly-target.update', 'monthly-target.delete',
            'invoices.view', 'invoices.create', 'invoices.update', 'invoices.setor',
            'invoices.setor-update', 'invoices.delete', 'invoices.report',
            'surat-jalan.view', 'surat-jalan.create', 'surat-jalan.update',
            'surat-jalan.delete', 'surat-jalan.report',
            'transactions.view', 'transactions.create',

            // Finance
            'budget-target.view', 'budget-target.create', 'budget-target.update', 'budget-target.delete',
            'finance.input.view', 'finance.input.create', 'finance.input.update', 'finance.input.delete',
            'finance.history',

            // Customer
            'customers.view', 'customers.create', 'customers.update', 'customers.delete',
        ];

        // =========================
        // CREATE PERMISSIONS
        // =========================
        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission, 'web');
        }

        // =========================
        // CREATE ROLES
        // =========================
        foreach ($roles as $role) {
            Role::findOrCreate($role, 'web');
        }

        // =========================
        // ASSIGN PERMISSIONS
        // =========================
        $super = Role::findByName('super-admin', 'web');
        $admin = Role::findByName('admin', 'web');
        $demo  = Role::findByName('demo', 'web');

        // 🔥 Super Admin → ALL ACCESS
        $super->syncPermissions(Permission::all());

        // 🟡 Admin → Limited CRUD
        $admin->syncPermissions([
            'product-batches.view', 'product-batches.create', 'product-batches.update', 'product-batches.delete',
            'product-batches.report',

            'monthly-target.view',
            'invoices.view', 'invoices.create', 'invoices.update',
            'invoices.setor', 'invoices.delete', 'invoices.report',

            'surat-jalan.view', 'surat-jalan.create', 'surat-jalan.update',
            'surat-jalan.delete', 'surat-jalan.report',

            'transactions.view', 'transactions.create',

            'finance.input.view', 'finance.input.create', 'finance.input.update', 'finance.input.delete',
            'finance.history',

            'customers.view',
        ]);

        // 🟢 DEMO → VIEW ONLY
        $demo->syncPermissions([
            // Products
            'products.view',
            'product-batches.view',

            // Penjualan
            'monthly-target.view',
            'invoices.view',
            'surat-jalan.view',
            'transactions.view',

            // Finance (opsional, kalau mau tampil dashboard saja)
            'budget-target.view',
            'finance.input.view',

            // Customer
            'customers.view',
        ]);
    }
}
