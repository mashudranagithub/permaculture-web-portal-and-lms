<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create Starter Permissions
        $permissions = [
            ['name' => 'Manage Users', 'slug' => 'manage-users', 'description' => 'Can approve, edit and delete users'],
            ['name' => 'Manage Roles', 'slug' => 'manage-roles', 'description' => 'Can create and assign roles'],
            ['name' => 'Manage Permissions', 'slug' => 'manage-permissions', 'description' => 'Can create system permissions'],
            ['name' => 'Manage Courses', 'slug' => 'manage-courses', 'description' => 'Can create, edit and delete courses'],
        ];

        foreach ($permissions as $p) {
            Permission::updateOrCreate(['slug' => $p['slug']], $p);
        }

        // 2. Create Default Roles
        $roles = [
            ['name' => 'Super Admin', 'slug' => 'super-admin'],
            ['name' => 'Administrator', 'slug' => 'admin'],
            ['name' => 'Course Admin', 'slug' => 'course-admin'],
            ['name' => 'Teacher', 'slug' => 'teacher'],
            ['name' => 'Student', 'slug' => 'student'],
        ];

        foreach ($roles as $role) {
            $roleModel = Role::updateOrCreate(['slug' => $role['slug']], $role);
            
            // Assign all permissions to Super Admin role
            if ($role['slug'] === 'super-admin') {
                $roleModel->permissions()->sync(Permission::all()->pluck('id'));
            }
        }

        // 2. Create the Super Admin User
        $admin = User::updateOrCreate(
            ['email' => 'mashudtech@gmail.com'],
            [
                'name' => 'Mashud Rana',
                'password' => Hash::make('password123'), // You should change this!
                'email_verified_at' => now(),
                'is_approved' => true,
            ]
        );

        // 3. Assign Super Admin Role
        $adminRole = Role::where('slug', 'super-admin')->first();
        if ($adminRole) {
            $admin->roles()->sync([$adminRole->id]);
        }

        echo "Seeding completed! \n";
        echo "Login: [EMAIL_ADDRESS] \n";
        echo "Password: password \n";
    }
}
