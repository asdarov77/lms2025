<?php

namespace Tests\Feature\Controllers;

use App\Models\User;
use App\Models\Group;
use App\Models\Permission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register(): void
    {
        $group = Group::factory()->create();

        $response = $this->postJson('/api/register', [
            'fio' => 'Test User',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'group_id' => $group->id,
            'role' => 'Пользователь',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure(['user' => ['id', 'fio', 'email']]);
    }

    public function test_user_cannot_register_without_fio(): void
    {
        $response = $this->postJson('/api/register', [
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('fio');
    }

    public function test_user_cannot_register_without_password_confirmation(): void
    {
        $response = $this->postJson('/api/register', [
            'fio' => 'Test User',
            'password' => 'password123',
        ]);

        $response->assertStatus(422);
    }

    public function test_user_can_login_with_valid_credentials(): void
    {
        $user = User::factory()->create([
            'fio' => 'Test User',
            'password' => \Hash::make('password123'),
        ]);

        $response = $this->postJson('/api/login', [
            'fio' => 'Test User',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['user', 'token', 'permissions']);
    }

    public function test_user_cannot_login_with_invalid_credentials(): void
    {
        $response = $this->postJson('/api/login', [
            'fio' => 'Non Existent User',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(401)
            ->assertJson(['message' => 'неверный логин или пароль']);
    }

    public function test_authenticated_user_can_logout(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/logout');

        $response->assertStatus(200)
            ->assertJson(['message' => 'logout']);
    }

    public function test_admin_can_get_all_users(): void
    {
        $admin = User::factory()->admin()->create();
        User::factory()->count(3)->create();
        Sanctum::actingAs($admin);

        $response = $this->getJson('/api/users');

        $response->assertStatus(200)
            ->assertJsonCount(4);
    }

    public function test_non_admin_can_only_get_users_from_same_group(): void
    {
        $group1 = Group::factory()->create();
        $group2 = Group::factory()->create();
        
        $user = User::factory()->create(['group_id' => $group1->id, 'role' => 'Пользователь']);
        User::factory()->count(2)->create(['group_id' => $group1->id]);
        User::factory()->count(2)->create(['group_id' => $group2->id]);
        
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/users');

        $response->assertStatus(200)
            ->assertJsonCount(3);
    }

    public function test_admin_can_delete_user(): void
    {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->create();
        Sanctum::actingAs($admin);

        $response = $this->deleteJson("/api/users/{$user->id}");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    public function test_cannot_delete_super_user(): void
    {
        $admin = User::factory()->admin()->create();
        Sanctum::actingAs($admin);

        $response = $this->deleteJson('/api/users/1');

        $response->assertStatus(500)
            ->assertJson(['message' => 'невозможно удалить супер пользователя']);
    }

    public function test_admin_can_update_user(): void
    {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->create();
        Sanctum::actingAs($admin);

        $response = $this->putJson("/api/users/{$user->id}", [
            'fio' => 'Updated Name',
            'role' => 'Преподаватель',
        ]);

        $response->assertStatus(200)
            ->assertJson(['fio' => 'Updated Name', 'role' => 'Преподаватель']);
    }

    public function test_admin_can_change_user_password(): void
    {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->create();
        Sanctum::actingAs($admin);

        $response = $this->postJson("/api/users/{$user->id}/chpass", [
            'password' => 'newpassword123',
        ]);

        $response->assertStatus(200);
        
        $this->assertTrue(\Hash::check('newpassword123', $user->fresh()->password));
    }
}
