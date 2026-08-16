<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['name' => 'مدیر', 'slug' => 'admin'],
            ['name' => 'اپراتور', 'slug' => 'operator'],
            ['name' => 'مشاهده‌گر', 'slug' => 'viewer'],
        ];

        foreach ($roles as $role) {
            Role::query()->firstOrCreate(
                ['slug' => $role['slug']],
                ['name' => $role['name']],
            );
        }
    }
}
