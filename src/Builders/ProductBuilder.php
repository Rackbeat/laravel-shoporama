<?php

namespace KgBot\Shoporama\Builders;


use KgBot\Shoporama\Models\Product;

class ProductBuilder extends Builder
{
    protected $entity = 'product';
    protected $model = Product::class;
}