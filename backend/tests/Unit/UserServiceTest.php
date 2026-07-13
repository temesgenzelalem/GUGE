<?php

namespace Tests\Unit;

use App\Domain\User\Contracts\UserRepositoryInterface;
use App\Domain\User\Events\UserRegistered;
use App\Domain\User\Events\UserUpdated;
use App\Domain\User\UserService;
use App\Models\User;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserServiceTest extends TestCase
{
    private UserService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $repository = $this->app->make(UserRepositoryInterface::class);
        $this->service = new UserService($repository);
    }

    public function test_register_hashes_password(): void
    {
        Event::fake();

        $user = $this->service->register([
            'name' => 'Tigist Alemu',
            'email' => 'tigist@example.com',
            'password' => 'plainpassword',
        ]);

        $this->assertTrue(Hash::check('plainpassword', $user->password));
    }

    public function test_register_dispatches_user_registered_event(): void
    {
        Event::fake([UserRegistered::class]);

        $user = $this->service->register([
            'name' => 'Abebe Girma',
            'email' => 'abebe@example.com',
            'password' => 'secret123',
        ]);

        Event::assertDispatched(UserRegistered::class, fn ($e) => $e->user->id === $user->id);
    }

    public function test_login_returns_user_with_correct_credentials(): void
    {
        $user = User::factory()->create(['password' => Hash::make('correct_pass')]);

        $result = $this->service->login([
            'email' => $user->email,
            'password' => 'correct_pass',
        ]);

        $this->assertNotNull($result);
        $this->assertEquals($user->id, $result->id);
    }

    public function test_login_returns_null_with_wrong_password(): void
    {
        $user = User::factory()->create(['password' => Hash::make('correct_pass')]);

        $result = $this->service->login([
            'email' => $user->email,
            'password' => 'wrong_pass',
        ]);

        $this->assertNull($result);
    }

    public function test_login_returns_null_for_unknown_email(): void
    {
        $result = $this->service->login([
            'email' => 'nobody@example.com',
            'password' => 'anything',
        ]);

        $this->assertNull($result);
    }

    public function test_update_user_dispatches_user_updated_event(): void
    {
        Event::fake([UserUpdated::class]);

        $user = User::factory()->create();

        $this->service->updateUser($user, ['name' => 'Updated Name']);

        Event::assertDispatched(UserUpdated::class, fn ($e) => $e->user->id === $user->id);
    }

    public function test_update_password_hashes_new_password(): void
    {
        $user = User::factory()->create(['password' => Hash::make('oldpassword')]);

        $this->service->updatePassword($user, [
            'current_password' => 'oldpassword',
            'password' => 'newpassword123',
        ]);

        $this->assertTrue(Hash::check('newpassword123', $user->fresh()->password));
    }

    public function test_update_password_throws_when_current_password_wrong(): void
    {
        $user = User::factory()->create(['password' => Hash::make('correct')]);

        $this->expectException(\InvalidArgumentException::class);

        $this->service->updatePassword($user, [
            'current_password' => 'wrong',
            'password' => 'newpassword',
        ]);
    }

    public function test_create_user_hashes_password(): void
    {
        Event::fake();

        $user = $this->service->createUser([
            'name' => 'New Admin',
            'email' => 'newadmin@example.com',
            'password' => 'adminpass',
            'role' => 'admin',
        ]);

        $this->assertTrue(Hash::check('adminpass', $user->password));
    }

    public function test_delete_user_removes_from_database(): void
    {
        $user = User::factory()->create();
        $id = $user->id;

        $this->service->deleteUser($user);

        $this->assertDatabaseMissing('users', ['id' => $id]);
    }

    public function test_find_by_id_returns_correct_user(): void
    {
        $user = User::factory()->create();

        $found = $this->service->findById($user->id);

        $this->assertNotNull($found);
        $this->assertEquals($user->id, $found->id);
    }

    public function test_find_by_id_returns_null_for_missing_user(): void
    {
        $found = $this->service->findById(999999);

        $this->assertNull($found);
    }
}
