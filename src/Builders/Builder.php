<?php

namespace KgBot\Shoporama\Builders;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use KgBot\Shoporama\Utils\Model;
use KgBot\Shoporama\Utils\Request;


class Builder
{
    protected $entity;
    /** @var Model */
    protected $model;
    private $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @param array $filters
     *
     * @return Collection|Model[]
     */
    public function get($filters = [])
    {
        $entity = Str::plural($this->entity);
        $filters['limit'] = 100;

        $urlFilters = $this->parseFilters($filters);

        return $this->request->handleWithExceptions(function () use ($urlFilters, $entity) {
            $response = $this->request->client->get("{$this->entity}{$urlFilters}");
            $responseData = json_decode((string)$response->getBody());
            $fetchedItems = collect($responseData->{$entity});
            $items = collect([]);
            $count = (isset($responseData->paging)) ? $responseData->paging->count : 0;

            foreach ($fetchedItems as $index => $item) {
                /** @var Model $model */
                $model = new $this->model($this->request, $item);

                $items->push($model);
            }

            return $items;
        });
    }

    protected function parseFilters(array $filters = []) : string
    {
        if (empty($filters)) {
            return '';
        }

        $args = [];
        foreach ($filters as $filter => $value) {
            $args[] = $filter . '=' .$value;
        }

        return '?' . implode("&", $args);
    }

    /**
     * Fetch items from integration by chunks
     * This will return generator to save memory for proper job handle
     * 
     * @param  array       $filters  
     * @param  int|integer $chunkSize
     * @return 
     */
    public function all(array $filters = [], int $chunkSize = 100)
    {
        $entity = Str::plural($this->entity);
        $offset = 0;

        $response = function ($filters, $offset, $entity) use ($chunkSize) {
            $filters['limit'] = $chunkSize;
            $filters['offset'] = $offset;

            $urlFilters = $this->parseFilters($filters);

            return $this->request->handleWithExceptions(function () use ($urlFilters, $entity) {
                $response = $this->request->client->get("{$this->entity}{$urlFilters}");
                $responseData = json_decode((string)$response->getBody());
                $fetchedItems = collect($responseData->{$entity});
                $items = collect([]);
                $count = (isset($responseData->paging)) ? $responseData->paging->count : 0;

                foreach ($fetchedItems as $index => $item) {
                    /** @var Model $model */
                    $model = new $this->model($this->request, $item);

                    $items->push($model);
                }

                return (object)[
                    'items' => $items,
                    'count' => $count,
                ];
            });
        };

        do {
            $resp = $response($filters, $offset, $entity);

            $countResults = count($resp->items);
            if ( $countResults === 0 ) {
                break;
            }
            // make a generator of the results and return them
            // so the logic will handle them before the next iteration
            // in order to avoid memory leaks
            foreach ($resp->items as $result ) {
                yield $result;
            }

            unset( $resp );

            $offset += $chunkSize;
        } while ($countResults === $chunkSize);
    }

    public function find($id)
    {
        return $this->request->handleWithExceptions(function () use ($id) {

            $response = $this->request->client->get("{$this->entity}/{$id}");
            $responseData = json_decode((string)$response->getBody());

            return new $this->model($this->request, $responseData);
        });
    }

    public function create($data)
    {
        return $this->request->handleWithExceptions(function () use ($data) {

            $response = $this->request->client->post("{$this->entity}", [
                'json' => $data,
            ]);

            $responseData = json_decode((string)$response->getBody());

            return new $this->model($this->request, $responseData);
        });
    }

    public function getEntity()
    {
        return $this->entity;
    }

    public function setEntity($new_entity)
    {
        $this->entity = $new_entity;

        return $this->entity;
    }
}