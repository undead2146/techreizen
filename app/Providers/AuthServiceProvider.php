<?php

namespace App\Providers;

use App\Models\User;
use App\Models\Traveller;
use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Set a custom URL for password reset links
        ResetPassword::createUrlUsing(function ($user, string $token) {
            return url(route('password.reset', [
                'token' => $token,
                'email' => $user->getEmailForPasswordReset(),
            ], false));
        });

        // Register custom user provider that can look up users by traveller email
        Auth::provider('traveller_email', function ($app, array $config) {
            return new class($app['hash'], $config['model']) extends EloquentUserProvider {
                public function retrieveByCredentials(array $credentials)
                {
                    if (isset($credentials['email'])) {
                        // Find traveller by email
                        $traveller = Traveller::where('email', $credentials['email'])->first();
                        
                        if ($traveller) {
                            // Then find the user
                            return User::find($traveller->user_id);
                        }
                        return null;
                    }
                    
                    // Otherwise use normal retrieval
                    return parent::retrieveByCredentials($credentials);
                }
            };
        });
    }
}
