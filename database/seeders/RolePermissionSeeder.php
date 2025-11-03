<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create Permissions
        $permissions = [
            // Product permissions
            'product.view',
            'product.create',
            'product.edit',
            'product.delete',
            'product.manage-stock',
            
            // User permissions
            'user.view',
            'user.create',
            'user.edit',
            'user.delete',
            'user.manage-roles',
            
            // Order permissions (for future use)
            'order.view',
            'order.create',
            'order.edit',
            'order.delete',
            'order.manage',
            
            // System permissions
            'system.admin',
            'system.analytics',
            'system.settings',
            
            // Email permissions
            'email.send-notifications',
            'email.manage-templates',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create Roles and assign permissions
        
        // Admin Role - Full access
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $adminRole->givePermissionTo(Permission::all());

        // Customer Role - Limited access
        $customerRole = Role::firstOrCreate(['name' => 'customer']);
        $customerRole->givePermissionTo([
            'product.view',
            'order.view',
            'order.create',
        ]);

        // Create Admin User
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'System Administrator',
                'email' => 'admin@example.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $admin->assignRole('admin');

        // Create Sample Customer Users
        $customers = [
            [
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'password' => Hash::make('password'),
            ],
            [
                'name' => 'Jane Smith',
                'email' => 'jane@example.com',
                'password' => Hash::make('password'),
            ],
            [
                'name' => 'Bob Johnson',
                'email' => 'bob@example.com',
                'password' => Hash::make('password'),
            ]
        ];

        foreach ($customers as $customerData) {
            $customer = User::firstOrCreate(
                ['email' => $customerData['email']],
                array_merge($customerData, ['email_verified_at' => now()])
            );
            $customer->assignRole('customer');
        }

        $this->command->info('Roles and permissions created successfully!');
        
        // Display summary
        $this->command->table(
            ['Role', 'Permissions Count', 'Users Count'],
            [
                ['Admin', $adminRole->permissions()->count(), $adminRole->users()->count()],
                ['Customer', $customerRole->permissions()->count(), $customerRole->users()->count()],
            ]
        );

        $this->command->table(
            ['User', 'Email', 'Roles'],
            User::with('roles')->get()->map(function ($user) {
                return [
                    $user->name,
                    $user->email,
                    $user->roles->pluck('name')->join(', ')
                ];
            })->toArray()
        );
    }
}
