<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use App\Models\User;
use App\Policies\GroupPolicy;
use App\Policies\SpacePolicy;
use App\Policies\MessagePolicy;
use App\Policies\UserPolicy;
use App\Policies\NotificationPolicy;
use App\Policies\AdminPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        'App\Models\Message' => 'App\Policies\MessagePolicy',
        'App\Events\Messages' => 'App\Policies\MessagesPolicy',
        'App\Models\User' => 'App\Policies\UserPolicy',
        'App\Models\Space' => 'App\Policies\SpacePolicy',
        'App\Models\Group' => 'App\Policies\GroupPolicy',
        'App\Models\Notification' => 'App\Policies\NotificationPolicy',
        'App\Models\Admin' => 'App\Policies\AdminPolicy',
        Space::class => SpacePolicy::class,
        Message::class => MessagePolicy::class,
        User::class => UserPolicy::class,
        Group::class => GroupPolicy::class,
        Notification::class => NotificationPolicy::class,
        Admin::class => AdminPolicy::class
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
    }
}
