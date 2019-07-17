<?php
/**
 * Created by PhpStorm.
 * User: nts
 * Date: 1.4.18.
 * Time: 00.02
 */

namespace KgBot\Shoporama\Models;


use KgBot\Shoporama\Utils\Model;

class Product extends Model
{
    public $number;
    protected $entity = 'product';
    protected $primaryKey = 'product_id';
}