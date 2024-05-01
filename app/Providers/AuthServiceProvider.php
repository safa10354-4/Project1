<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{


    protected $policies = [


    ];


    public function boot()
    {
         //Passport::routes();
        $this->registerPolicies();
        Passport::tokensCan([
            'user'=>'user Type',
            'admin'=>'Admin User Type',
        ]);
    }
}
