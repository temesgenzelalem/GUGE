<?php

namespace Database\Factories;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AuditLogFactory extends Factory
{
    protected $model = AuditLog::class;

    public function definition(): array
    {
        return [
            'actor_id' => User::factory(),
            'action' => $this->faker->randomElement([
                'created', 'updated', 'deleted',
                'login', 'logout', 'viewed',
            ]),
            'auditable_type' => $this->faker->randomElement([
                'App\\Models\\Region',
                'App\\Models\\Product',
                'App\\Models\\Story',
                'App\\Models\\Creator',
                'App\\Models\\User',
            ]),
            'auditable_id' => $this->faker->numberBetween(1, 100),
            'metadata' => ['note' => $this->faker->sentence()],
        ];
    }

    public function forActor(User $user): self
    {
        return $this->state(fn (array $attributes) => [
            'actor_id' => $user->id,
        ]);
    }
}
