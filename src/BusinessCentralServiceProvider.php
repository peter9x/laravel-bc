<?php

namespace Mupy\BusinessCentral;

use Illuminate\Support\ServiceProvider;

class BusinessCentralServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/businesscentral.php' => config_path('businesscentral.php'),
        ], 'config');
    }

    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/businesscentral.php', 'businesscentral'
        );

        $this->app->singleton(BusinessCentralClient::class, function ($app) {
            return new BusinessCentralClient(config('businesscentral'));
        });

        $this->app->alias(BusinessCentralClient::class, 'businesscentral');
    }
}
