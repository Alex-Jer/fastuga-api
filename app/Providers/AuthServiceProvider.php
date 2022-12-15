<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
        Passport::loadKeysFrom(__DIR__ . '/../Secrets');

        //Passport::hashClientSecrets();

        Passport::tokensCan([
            'view_orders' => 'View orders',
            'prepare_orders' => 'Prepare orders',
            'deliver_orders' => 'Deliver orders',
            'cancel_orders' => 'Cancel orders',
            'manage_users' => 'Manage users',
            'manage_products' => 'Manage products',
        ]);

        // Passport::setDefaultScope([
        // ]);

        /*
        view_orders			1 << 0
        prepare_orders		1 << 1
        deliver_orders		1 << 2
        cancel_orders		1 << 3
        manage_users		1 << 4
        manage_products		1 << 5
        */
    }
}
