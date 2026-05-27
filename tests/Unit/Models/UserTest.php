<?php

namespace Tests\Unit\Models;

use App\Models\User;
use App\Models\Group;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_has_fillable_attributes(): void
    {
        $user = new User();
        $fillable = $user->getFillable();

        $this->assertContains('name', $fillable);
        $this->assertContains('email', $fillable);
        $this->assertContains('password', $fillable);
    }

    public function test_user_password_is_hashed(): void
    {
        $user = User::factory()->create([
            'password' => 'plain-text-password',
        ]);

        $this->assertNotEquals('plain-text-password', $user->password);
        $this->assertTrue(\Hash::check('plain-text-password', $user->password));
    }

    public function test_user_email_verified_at_is_cast_to_datetime(): void
    {
        $user = User::factory()->create();
        
        $this->assertInstanceOf(\DateTime::class, $user->email_verified_at);
    }

    public function test_user_can_have_group(): void
    {
        $group = Group::factory()->create();
        $user = User::factory()->create(['group_id' => $group->id]);

        $this->assertEquals($group->id, $user->group_id);
        $this->assertEquals($group->groupname, $user->group->groupname);
    }

    public function test_user_can_have_roles(): void
    {
        $user = User::factory()->create();
        $role = Role::factory()->create();

        $user->roles()->attach($role);

        $this->assertTrue($user->roles->contains($role));
    }

    public function test_user_can_have_permissions(): void
    {
        $user = User::factory()->create();
        $permission = Permission::factory()->create();

        $user->permissions()->attach($permission);

        $this->assertTrue($user->permissions->contains($permission));
    }

    public function test_user_hidden_attributes(): void
    {
        $user = User::factory()->create();
        $array = $user->toArray();

        $this->assertArrayNotHasKey('password', $array);
        $this->assertArrayNotHasKey('remember_token', $array);
    }

    public function test_admin_role_user(): void
    {
        $user = User::factory()->admin()->create();
        
        $this->assertEquals('Администратор', $user->role);
    }

    public function test_teacher_role_user(): void
    {
        $user = User::factory()->teacher()->create();
        
        $this->assertEquals('Преподаватель', $user->role);
    }
}
