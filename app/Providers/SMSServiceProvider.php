<?php

namespace App\Providers;


use App\Engine\SMS\Dial2Verify;
use Illuminate\Support\ServiceProvider;

class SMSServiceProvider extends ServiceProvider
{

    protected $defer = true;

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('dial2verify.sms',function() {
            return new Dial2Verify();
        });
    }

    public function provides()
    {
        return ['dial2verify.sms', 'App\Engine\SMS\Dial2Verify'];
    }
}