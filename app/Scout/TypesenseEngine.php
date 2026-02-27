<?php

namespace App\Scout;

use Laravel\Scout\Builder;
use Laravel\Scout\Engines\Engine;
use Typesense\Client;
use Typesense\Collection;
use Typesense\Exceptions\ObjectNotFound;
use Typesense\Document;

class TypesenseEngine extends Engine
{
    protected $client;
    protected $importAction;

    public function __construct(Client $client, string $importAction = 'create')
    {
        $this->client = $client;
        $this->importAction = $importAction;
    }

    public function update($models)
    {
        foreach ($models as $model) {
            $this->indexDocument($model);
        }
    }

    public function delete($models)
    {
        foreach ($models as $model) {
            $this->deleteDocument($model);
        }
    }

    public function search(Builder $builder)
    {
        return $this->performSearch($builder);
    }

    protected function indexDocument($model)
    {
        $collection = $this->ensureCollectionExists($model);
        $documentId = (string)$model->getScoutKey();
        
        try {
            // Try to update first
            $document = $collection->documents[$documentId];
            $document->update($model->toSearchableArray());
        } catch (ObjectNotFound $e) {
            // If not found, create it
            $collection->documents->create([
                'id' => $documentId,
                ...$model->toSearchableArray()
            ]);
        }
    }

    protected function ensureCollectionExists($model)
    {
        $collectionName = $model->searchableAs();
        
        try {
            // Try to get the collection
            $collection = $this->client->collections[$collectionName];
            // Test if it's accessible
            $collection->retrieve();
            return $collection;
        } catch (ObjectNotFound $e) {
            // Collection doesn't exist, create it
            return $this->createCollection($model);
        } catch (\Exception $e) {
            // Other error, try to create
            return $this->createCollection($model);
        }
    }

    protected function deleteDocument($model)
    {
        try {
            $collection = $this->ensureCollectionExists($model);
            $document = $collection->documents[(string)$model->getScoutKey()];
            $document->delete();
        } catch (ObjectNotFound $e) {
            // Document already deleted
        }
    }

    protected function performSearch(Builder $builder)
    {
        $collection = $this->ensureCollectionExists($builder->model);
        
        $searchParameters = [
            'q' => $builder->query,
            'query_by' => 'name,description,category',
            'page' => $builder->limit && isset($builder->offset) ? floor($builder->offset / $builder->limit) + 1 : 1,
            'per_page' => $builder->limit ?: 10,
        ];

        // Add filters
        if (!empty($builder->wheres)) {
            $filterBy = [];
            
            foreach ($builder->wheres as $key => $value) {
                // Handle Laravel Scout comparison operators
                if (is_array($value) && isset($value['operator'])) {
                    $field = $value['column'];
                    $operator = $value['operator'];
                    $filterValue = $value['value'];
                    
                    // Convert Laravel operators to Typesense operators
                    switch ($operator) {
                        case '>=':
                            $filterBy[] = "{$field}:>={$filterValue}";
                            break;
                        case '<=':
                            $filterBy[] = "{$field}:<={$filterValue}";
                            break;
                        case '>':
                            $filterBy[] = "{$field}:>{$filterValue}";
                            break;
                        case '<':
                            $filterBy[] = "{$field}:<{$filterValue}";
                            break;
                        case '=':
                        case '==':
                            $filterBy[] = "{$field}:={$filterValue}";
                            break;
                        default:
                            $filterBy[] = "{$field}:='{$filterValue}'";
                    }
                } elseif (is_string($value) && in_array($value, ['>=', '<=', '>', '<', '=', '=='])) {
                    // Handle case where operator is sent as value (Laravel Scout bug)
                    // We need to get the actual value from the next filter or from context
                    // For now, we'll skip this filter as it's malformed
                    continue;
                } else {
                    // Handle simple key-value filters
                    $field = $key;
                    $filterValue = $value;
                    
                    if (is_bool($filterValue)) {
                        $filterBy[] = "{$field}:" . ($filterValue ? 'true' : 'false');
                    } elseif (is_numeric($filterValue)) {
                        $filterBy[] = "{$field}:={$filterValue}";
                    } else {
                        $filterBy[] = "{$field}:'{$filterValue}'";
                    }
                }
            }
            
            $filterString = implode(' && ', $filterBy);
            $searchParameters['filter_by'] = $filterString;
        }

        return $collection->documents->search($searchParameters);
    }


    protected function getCollectionFromBuilder(Builder $builder)
    {
        $model = $builder->model;
        return $this->ensureCollectionExists($model);
    }

    protected function createCollection($model)
    {
        $schema = $this->getCollectionSchema($model);
        return $this->client->collections->create($schema);
    }

    protected function getCollectionSchema($model)
    {
        return [
            'name' => $model->searchableAs(),
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
    }

    public function mapIds($results)
    {
        return collect($results['hits'])->pluck('document.id')->values();
    }

    public function map(Builder $builder, $results, $model)
    {
        if (empty($results['hits'])) {
            return $model->newCollection();
        }

        $keys = collect($results['hits'])
            ->pluck('document.id')
            ->values()
            ->all();

        $models = $model->whereIn(
            $model->getQualifiedKeyName(), $keys
        )->get()->keyBy($model->getKeyName());

        return collect($results['hits'])
            ->map(function ($hit) use ($models) {
                $key = $hit['document']['id'];
                return $models->has($key) ? $models[$key] : null;
            })
            ->filter()
            ->values();
    }

    public function getTotalCount($results)
    {
        return $results['found'] ?? 0;
    }

    public function flushIndexes($models)
    {
        foreach ($models as $model) {
            try {
                $collection = $this->ensureCollectionExists($model);
                $this->client->collections[$model->searchableAs()]->delete();
            } catch (ObjectNotFound $e) {
                // Collection already deleted
            }
        }
    }

    public function flush($model)
    {
        try {
            $collection = $this->ensureCollectionExists($model);
            $this->client->collections[$model->searchableAs()]->delete();
        } catch (ObjectNotFound $e) {
            // Collection already deleted
        }
    }

    public function paginate(Builder $builder, $perPage, $page)
    {
        $builder->limit = $perPage;
        $builder->offset = ($page - 1) * $perPage;
        
        $results = $this->search($builder);
        
        return [
            'data' => $this->map($builder, $results, $builder->model),
            'total' => $this->getTotalCount($results),
            'per_page' => $perPage,
            'current_page' => $page,
            'last_page' => ceil($this->getTotalCount($results) / $perPage),
        ];
    }

    public function lazyMap(Builder $builder, $results, $model)
    {
        return $this->map($builder, $results, $model);
    }

    public function createIndex($name, array $options = [])
    {
        // Typesense creates collections automatically when needed
        // This method is kept for compatibility
        return true;
    }

    public function deleteIndex($name)
    {
        try {
            $this->client->collections[$name]->delete();
            return true;
        } catch (ObjectNotFound $e) {
            // Collection already deleted
            return true;
        }
    }
}
