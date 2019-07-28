<?php

namespace KgBot\Shoporama\Builders;

use Illuminate\Support\Collection;
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
    public function get($filters = [], $entity = null)
    {
        $entity = $entity ?? $this->entity;
        $filters[] = ['limit', '=', 100];

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

    protected function parseFilters($filters = [])
    {

        if (array_search('limit', array_column($filters, 0)) !== (count($filters) - 1)) {

            unset($filters[count($filters) - 1]);
        }

        $urlFilters = '';

        if (count($filters) > 0) {

            $filters = array_unique($filters, SORT_REGULAR);

            $i = 1;

            $urlFilters .= '?';

            foreach ($filters as $filter) {

                $urlFilters .= $filter[0] . $filter[1] . $filter[2] ?? '=';

                if (count($filters) > $i) {

                    $urlFilters .= '&';
                }

                $i++;
            }
        }

        return $urlFilters;
    }

    public function all($filters = [], $entity = null)
    {
        $entity = $entity ?? $this->entity;
        $offset = 0;

        $items = collect();

        $response = function ($filters, $offset, $entity) {

            $filters[] = ['limit', '=', 100];
            $filters[] = ['offset', '=', $offset];

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

            $items = $items->merge($resp->items);
            $offset += 100;
            sleep(2);

        } while ($resp->count > 0);


        return $items;

    }

    public function find($id)
    {
        return $this->request->handleWithExceptions(function () use ($id) {

            $response = $this->request->client->get("{$this->entity}/{$id}");
            $responseData = collect(json_decode((string)$response->getBody()));

            return new $this->model($this->request, $responseData->first());
        });
    }

    public function create($data)
    {
        return $this->request->handleWithExceptions(function () use ($data) {

            $response = $this->request->client->post("{$this->entity}", [
                'json' => $data,
            ]);

            $responseData = collect(json_decode((string)$response->getBody()));

            return new $this->model($this->request, $responseData->first());
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