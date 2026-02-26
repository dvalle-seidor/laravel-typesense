<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Typesense\Client;

class TestTypesenseConnection extends Command
{
    protected $signature = 'typesense:test';
    protected $description = 'Test Typesense connection and collections';

    public function handle()
    {
        $clientSettings = config('scout.typesense.client-settings');
        
        // Create Guzzle handler with SSL disabled (same as ServiceProvider)
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

        try {
            // List all collections
            $collections = $client->collections->retrieve();
            $this->info('Collections found:');
            foreach ($collections as $collection) {
                $this->info('  - ' . $collection['name']);
            }

            // Try to access products collection
            try {
                $productsCollection = $client->collections['products'];
                $this->info('Products collection accessible');
                
                // Get collection details
                $details = $productsCollection->retrieve();
                $this->info('Collection details: ' . json_encode($details, JSON_PRETTY_PRINT));
                
            } catch (\Exception $e) {
                $this->error('Cannot access products collection: ' . $e->getMessage());
            }

        } catch (\Exception $e) {
            $this->error('Connection failed: ' . $e->getMessage());
        }

        return 0;
    }
}
