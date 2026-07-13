<?php

namespace App\Listeners;

use App\Domain\User\Events\UserRegistered;
use Illuminate\Contracts\Queue\ShouldQueue;

class HandleUserRegistered implements ShouldQueue
{
    /**
     * Send welcome notification and perform any post-registration tasks.
     *
     * Queue this listener so registration remains fast.
     */
    public function handle(UserRegistered $event): void
    {
        // Placeholder: send welcome email, assign default role, etc.
        // Implement when notification system is ready (Phase 3).
        //
        // Example future implementation:
        // $event->user->notify(new WelcomeNotification());
    }
}
