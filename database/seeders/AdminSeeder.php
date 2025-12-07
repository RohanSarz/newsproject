<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = [
            'name' => 'RAdmin',
            'email' => 'admin@mail.com',
            'password' => bcrypt('admin@mail.com'),
            'email_verified_at' => now(),
        ];
        $admin = User::create($admin);
        $admin->assignRole(['admin', 'moderator', 'instructor', 'student']);

        $moderator = [
            'name' => 'RoModerator',
            'email' => 'moderator@mail.com',
            'password' => bcrypt('moderator@mail.com'),
            'email_verified_at' => now(),
        ];
        $moderator = User::create($moderator);
        $moderator->syncRoles(['moderator']);

        $student = [
            'name' => 'StuRo',
            'email' => 'student@mail.com',
            'password' => bcrypt('student@mail.com'),
            'email_verified_at' => now(),
        ];
        $student = User::create($student);
        $student->syncRoles(['student']);

        $instructor = [
            'name' => 'InstRo',
            'email' => 'instructor@mail.com',
            'password' => bcrypt('instructor@mail.com'),
            'email_verified_at' => now(),
        ];
        $instructor = User::create($instructor);
        $instructor->syncRoles(['instructor']);
    }
}
