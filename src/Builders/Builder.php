<?php

namespace KgBot\Shoporama\Builders;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use KgBot\Shoporama\Utils\Model;
use KgBot\Shoporama\Utils\Request;

abstract class Builder
{
    protected $entity;
    /** @var Model */
    protected $model;
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @param array $filters
     *
     * @return Collection|Model[]
     * @throws \KgBot\Shoporama\Exceptions\ShoporamaClientException
     * @throws \KgBot\Shoporama\Exceptions\ShoporamaRequestException
     */
    public function get(array $filters = [])
    {
        $entity = Str::plural($this->entity);
        $filters['limit'] = 100;

        $urlFilters = $this->parseFilters($filters);

        return $this->request->handleWithExceptions(function () use ($urlFilters, $entity) {
            $response = $this->request->client->get("{$this->entity}{$urlFilters}");
            $responseData = json_decode((string)$response->getBody());
            $fetchedItems = collect($responseData->{$entity});
            $items = collect([]);

            foreach ($fetchedItems as $index => $item) {
                /** @var Model $model */
                $model = new $this->model($item);
                $items->push($model);
            }

            return $items;
        });
    }

    /**
     * @param array $filters
     *
     * @return string
     */
    protected function parseFilters(array $filters = []) : string
    {
        if (empty($filters)) {
            return '';
        }

        $args = [];
        foreach ($filters as $filter => $value) {
            if (is_array($value)) {
                $args[] = $value[0] . $value[1] . $value[2];
            } else {
                $args[] = $filter . '=' .$value;
            }
        }

        return '?' . implode("&", $args);
    }

    /**
     * Fetch items from integration by chunks
     * This will return generator to save memory for proper job handle
     *
     * @param array $filters
     * @param int $chunkSize
     *
     * @return \Generator
     * @throws \KgBot\Shoporama\Exceptions\ShoporamaClientException
     * @throws \KgBot\Shoporama\Exceptions\ShoporamaRequestException
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
                    $model = new $this->model($item);
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

            return new $this->model($responseData);
        });
    }

    public function create($data)
    {
        return $this->request->handleWithExceptions(function () use ($data) {

            $response = $this->request->client->post("{$this->entity}", [
                'json' => $data,
            ]);

            $responseData = json_decode((string)$response->getBody());

            return new $this->model($responseData);
        });
    }

    public function delete($id)
    {
        return $this->request->handleWithExceptions(function () use ($id) {
            return $this->request->client->delete("{$this->entity}/{$id}");
        });
    }

    public function update($id, $data = [])
    {
        return $this->request->handleWithExceptions(function () use ($id, $data) {
            $response = $this->request->client->put("{$this->entity}/{$id}", [
                'json' => $data,
            ]);

            $responseData = json_decode((string)$response->getBody());

            return new $this->model($responseData);
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