<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->call(RoleSeeder::class);

        $adminRole = Role::query()->where('slug', 'admin')->firstOrFail();
        $operatorRole = Role::query()->where('slug', 'operator')->firstOrFail();
        $viewerRole = Role::query()->where('slug', 'viewer')->firstOrFail();

        $admin = User::query()->firstOrCreate(
            ['email' => 'admin@rasad.test'],
            [
                'name' => 'کاربر مدیر',
                'password' => 'password',
                'email_verified_at' => now(),
            ],
        );

        $operator = User::query()->firstOrCreate(
            ['email' => 'operator@rasad.test'],
            [
                'name' => 'کاربر اپراتور',
                'password' => 'password',
                'email_verified_at' => now(),
            ],
        );

        $viewer = User::query()->firstOrCreate(
            ['email' => 'viewer@rasad.test'],
            [
                'name' => 'کاربر مشاهده‌گر',
                'password' => 'password',
                'email_verified_at' => now(),
            ],
        );

        $mixed = User::query()->firstOrCreate(
            ['email' => 'mixed@rasad.test'],
            [
                'name' => 'کاربر چندنقشی',
                'password' => 'password',
                'email_verified_at' => now(),
            ],
        );

        UserRole::query()->firstOrCreate(
            ['user_id' => $admin->id, 'role_id' => $adminRole->id],
            ['is_active' => true],
        );

        UserRole::query()->firstOrCreate(
            ['user_id' => $operator->id, 'role_id' => $operatorRole->id],
            ['is_active' => true],
        );

        UserRole::query()->firstOrCreate(
            ['user_id' => $viewer->id, 'role_id' => $viewerRole->id],
            ['is_active' => true],
        );

        UserRole::query()->firstOrCreate(
            ['user_id' => $mixed->id, 'role_id' => $operatorRole->id],
            ['is_active' => true],
        );

        UserRole::query()->firstOrCreate(
            ['user_id' => $mixed->id, 'role_id' => $viewerRole->id],
            ['is_active' => false],
        );
    }
}
