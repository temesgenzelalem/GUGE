<?php

namespace Tests\Unit;

use App\Models\User;
use Tests\TestCase;

/**
 * Tests the HasPermissions trait which bridges the legacy `role` column
 * with Spatie permission system.
 */
class HasPermissionsTest extends TestCase
{
    public function test_admin_user_has_admin_role(): void
    {
        $admin = User::factory()->admin()->make();

        $this->assertTrue($admin->hasRole('admin'));
        $this->assertTrue($admin->isAdmin());
    }

    public function test_super_admin_user_is_admin(): void
    {
        $superAdmin = User::factory()->superAdmin()->make();

        $this->assertTrue($superAdmin->isAdmin());
        $this->assertTrue($superAdmin->isSuperAdmin());
    }

    public function test_customer_user_is_not_admin(): void
    {
        $customer = User::factory()->make(['role' => 'customer']);

        $this->assertFalse($customer->isAdmin());
        $this->assertFalse($customer->isSuperAdmin());
    }

    public function test_moderator_has_correct_role(): void
    {
        $moderator = User::factory()->make(['role' => 'moderator']);

        $this->assertTrue($moderator->hasRole('moderator'));
        $this->assertFalse($moderator->isAdmin());
    }

    public function test_admin_has_role_from_array(): void
    {
        $admin = User::factory()->admin()->make();

        $this->assertTrue($admin->hasRole(['admin', 'super_admin']));
        $this->assertFalse($admin->hasRole(['customer', 'moderator']));
    }

    public function test_suspended_user_still_has_role(): void
    {
        $user = User::factory()->suspended()->make(['role' => 'admin']);

        // Suspension affects access control at policy level, not role check
        $this->assertTrue($user->hasRole('admin'));
        $this->assertEquals('suspended', $user->status);
    }

    public function test_role_name_returns_correct_normalized_role(): void
    {
        $user = User::factory()->make(['role' => 'admin']);

        $this->assertEquals('admin', $user->roleName());
    }
}
