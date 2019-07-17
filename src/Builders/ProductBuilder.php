<?php

namespace KgBot\Shoporama\Builders;


use KgBot\Shoporama\Models\Product;

class ProductBuilder extends Builder
{

    protected $entity = 'products';
    protected $model = Product::class;
}