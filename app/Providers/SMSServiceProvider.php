<?php

namespace App\Providers;


use App\Engine\SMS\Services\Messaging as MessagingService;
use Illuminate\Support\ServiceProvider;

class SMSServiceProvider extends ServiceProvider
{

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Bootstrap the application events.
     *
     * @return void
     * @codeCoverageIgnore
     */
    public function boot(){
        app()->configure('sms');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('codemojo.sms',function() {
            return new MessagingService();
        });

        $this->app->alias('codemojo.sms', 'App\Engine\SMS\Contracts\Services\Messaging');
    }

    public function provides()
    {
        return ['codemojo.sms', 'App\Engine\SMS\Contracts\Services\Messaging'];
    }
}