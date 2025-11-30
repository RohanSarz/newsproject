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
        $teacher = Role::findOrCreate('teacher');
        $student = Role::findOrCreate('student');

        $permissions = ['manage_users', 'manage_course', 'create_course', 'edit_course', 'delete_course', 'view_course', 'enroll_course'];
        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission);
        }

        $admin->givePermissionTo($permissions);
        $teacher->givePermissionTo(['create_course', 'edit_course', 'delete_course', 'view_course', 'manage_course']);
        $student->givePermissionTo(['view_course', 'enroll_course']);
    }
}
