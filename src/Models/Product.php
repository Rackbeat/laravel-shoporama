<?php

namespace KgBot\Shoporama\Models;


use KgBot\Shoporama\Utils\Model;

class Product extends Model
{
    public $number;
    protected $entity = 'product';
    protected $primaryKey = 'product_id';
}