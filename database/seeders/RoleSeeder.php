<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            ['name' => 'Super Admin', 'slug' => 'super-admin'],
            ['name' => 'Admin', 'slug' => 'admin'],
            ['name' => 'Course Admin', 'slug' => 'course-admin'],
            ['name' => 'Course Instructor', 'slug' => 'instructor'],
            ['name' => 'Student', 'slug' => 'student'],
        ];

        foreach ($roles as $role) {
            \App\Models\Role::create($role);
        }
    }
}
