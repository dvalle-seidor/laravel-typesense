<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;
use Typesense\Client;

class Product extends Model
{
    use HasFactory, Searchable;

    protected $fillable = [
        'name',
        'description',
        'price',
        'category',
        'in_stock'
    ];

    /**
     * Get the indexable data array for the model.
     *
     * @return array
     */
    public function toSearchableArray()
    {
        return [
            'id' => (string) $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'category' => $this->category,
            'price' => (float) $this->price,
            'in_stock' => (bool) $this->in_stock,
        ];
    }

    /**
     * Determine if the model should be searchable.
     *
     * @return bool
     */
    public function shouldBeSearchable()
    {
        return true;
    }

    /**
     * Get the Typesense index name for the model.
     *
     * @return string
     */
    public function searchableAs()
    {
        return 'products';
    }

    /**
     * Get the Typesense client instance.
     *
     * @return \Typesense\Client
     */
    public static function getTypesenseClient()
    {
        return new Client([
            'api_key' => config('scout.typesense.client-settings.api_key'),
            'nodes' => config('scout.typesense.client-settings.nodes'),
            'nearest_node' => config('scout.typesense.client-settings.nearest_node'),
            'connection_timeout_seconds' => 2,
            'healthcheck_interval_seconds' => 30,
            'num_retries' => 3,
            'retry_interval_seconds' => 1,
            'verify_ssl' => false,
        ]);
    }
}
