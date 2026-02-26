<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Laravel\Scout\EngineManager;
use App\Scout\TypesenseEngine;
use Typesense\Client;

class TypesenseServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        $this->app[EngineManager::class]->extend('typesense', function ($app) {
            $clientSettings = config('scout.typesense.client-settings');
            
            // Create Guzzle handler with SSL disabled
            $handlerStack = \GuzzleHttp\HandlerStack::create();
            $handlerStack->push(\GuzzleHttp\Middleware::redirect());
            
            $httpClient = new \GuzzleHttp\Client([
                'handler' => $handlerStack,
                'verify' => false, // Disable SSL verification
                'timeout' => 30,
            ]);
            
            $client = new Client([
                'api_key' => $clientSettings['api_key'],
                'nodes' => $clientSettings['nodes'],
                'nearest_node' => $clientSettings['nearest_node'],
                'connection_timeout_seconds' => $clientSettings['connection_timeout_seconds'],
                'healthcheck_interval_seconds' => $clientSettings['healthcheck_interval_seconds'],
                'num_retries' => $clientSettings['num_retries'],
                'retry_interval_seconds' => $clientSettings['retry_interval_seconds'],
                'client' => $httpClient,
            ]);

            return new TypesenseEngine($client, config('scout.typesense.import_action', 'create'));
        });
    }
}
