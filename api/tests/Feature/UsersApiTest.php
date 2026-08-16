<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UsersApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config(['services.api.key' => 'test-secret']);
    }

    public function test_users_endpoint_rejects_missing_api_key(): void
    {
        $this->getJson('/api/users')->assertUnauthorized();
    }

    public function test_users_endpoint_rejects_wrong_api_key(): void
    {
        $this->withHeader('X-Api-Key', 'wrong-secret')
            ->getJson('/api/users')
            ->assertUnauthorized();
    }

    public function test_users_endpoint_returns_users_with_active_and_inactive_roles(): void
    {
        $user = User::factory()->create([
            'name' => 'کاربر تست',
            'email' => 'test@rasad.test',
        ]);

        $activeRole = Role::query()->create([
            'name' => 'مدیر',
            'slug' => 'admin',
        ]);

        $inactiveRole = Role::query()->create([
            'name' => 'مشاهده‌گر',
            'slug' => 'viewer',
        ]);

        $user->roles()->attach($activeRole->id, ['is_active' => true]);
        $user->roles()->attach($inactiveRole->id, ['is_active' => false]);

        $response = $this->withHeader('X-Api-Key', 'test-secret')
            ->getJson('/api/users');

        $response->assertOk()
            ->assertJsonPath('data.0.id', $user->id)
            ->assertJsonPath('data.0.name', 'کاربر تست')
            ->assertJsonPath('data.0.email', 'test@rasad.test')
            ->assertJsonPath('data.0.roles.0.is_active', true)
            ->assertJsonPath('data.0.roles.1.is_active', false)
            ->assertJsonMissingPath('data.0.password');
    }
}
