<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;
use Typesense\Client;

class CreateTypesenseCollection extends Command
{
    protected $signature = 'typesense:create-collection';
    protected $description = 'Create Typesense collection for products';

    public function handle()
    {
        $clientSettings = config('scout.typesense.client-settings');
        
        $client = new Client([
            'api_key' => $clientSettings['api_key'],
            'nodes' => $clientSettings['nodes'],
            'nearest_node' => $clientSettings['nearest_node'],
            'verify_ssl' => false,
        ]);

        try {
            $collection = $client->collections['products'];
            $this->info('Collection "products" already exists');
            return 0;
        } catch (\Exception $e) {
            // Collection doesn't exist, create it
            $schema = [
                'name' => 'products',
                'fields' => [
                    ['name' => 'id', 'type' => 'string'],
                    ['name' => 'name', 'type' => 'string'],
                    ['name' => 'description', 'type' => 'string'],
                    ['name' => 'category', 'type' => 'string', 'facet' => true],
                    ['name' => 'price', 'type' => 'float'],
                    ['name' => 'in_stock', 'type' => 'bool', 'facet' => true],
                ],
                'default_sorting_field' => 'id'
            ];

            $client->collections->create($schema);
            $this->info('Collection "products" created successfully');
            return 0;
        }
    }
}
