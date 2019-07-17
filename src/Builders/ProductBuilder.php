<?php
/**
 * Created by PhpStorm.
 * User: nts
 * Date: 1.4.18.
 * Time: 00.01
 */

namespace KgBot\Shoporama\Builders;


use KgBot\Shoporama\Models\Product;

class ProductBuilder extends Builder
{

    protected $entity = 'products';
    protected $model = Product::class;
}