<?php

namespace App\Providers;


use CodeMojo\Client\Services\AuthenticationService;
use CodeMojo\Client\Services\DataSyncService;
use CodeMojo\Client\Services\GamificationService;
use CodeMojo\Client\Services\MetaService;
use CodeMojo\Client\Services\WalletService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use Symfony\Component\HttpFoundation\Request;

class CodemojoServiceProvider extends ServiceProvider
{

    protected $defer = true;

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $auth = new AuthenticationService(env('CODEMOJO_ID'), env('CODEMOJO_SECRET'), env('CODEMOJO_SERVER', 2), function($no, $msg){
            Log::error('Codemojo Service, ' . $no . ', ' . $msg);
        });
        $this->app->singleton('codemojo.wallet',function() use ($auth) {
            return new WalletService($auth);
        });
        $this->app->singleton('codemojo.sync',function() use ($auth) {
            return new DataSyncService($auth);
        });
        $this->app->singleton('codemojo.gamify',function() use ($auth) {
            return new GamificationService($auth);
        });
        $this->app->singleton('codemojo.meta',function() use ($auth) {
            return new MetaService($auth);
        });
    }

    public function provides()
    {
        return ['wallet', 'meta'];
    }
}