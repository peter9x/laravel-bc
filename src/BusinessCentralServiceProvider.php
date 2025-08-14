<?php

namespace Mupy\BusinessCentral;

use Illuminate\Support\ServiceProvider;

class BusinessCentralServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/businesscentral.php' => config_path('businesscentral.php'),
        ], 'config');
    }

    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/businesscentral.php',
            'businesscentral'
        );

        $this->app->singleton(BusinessCentralClient::class, function ($app) {
            /** @var array{
             *     connections: array<string, array{client_id: string, secret: string}>,
             *     api_url: string
             * } $config */
            $config = config('businesscentral');

            return new BusinessCentralClient($config);
        });

        $this->app->alias(BusinessCentralClient::class, 'businesscentral');
    }
}
