<?php

namespace KgBot\Shoporama\Builders;


use KgBot\Shoporama\Models\Order;

class OrderBuilder extends Builder
{
    protected $entity = 'order';
    protected $model = Order::class;
}