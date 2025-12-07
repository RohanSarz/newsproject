<?php

namespace Database\Seeders;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = Role::findOrCreate('admin');
        $moderator = Role::findOrCreate('moderator');
        $instructor = Role::findOrCreate('instructor');
        $student = Role::findOrCreate('student');

        $permissions = ['manage_users', 'manage_roles', 'create_course', 'edit_course', 'view_course', 'delete_course', 'create_lesson', 'edit_lesson', 'view_lesson', 'delete_lesson', 'enroll_lesson', 'enroll_course'];
        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission);
        }


        $admin->givePermissionTo($permissions);
        $moderator->givePermissionTo(['manage_users', 'manage_roles']);
        $instructor->givePermissionTo(['create_course', 'edit_course', 'view_course', 'delete_course', 'create_lesson', 'edit_lesson', 'view_lesson', 'delete_lesson']);
        $student->givePermissionTo(['view_course', 'enroll_course']);
    }
}
