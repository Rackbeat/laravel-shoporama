<?php
/**
 * Created by PhpStorm.
 * User: nts
 * Date: 19.4.18.
 * Time: 01.30
 */

namespace KgBot\Shoporama\Models;


use KgBot\Shoporama\Utils\Model;

class Order extends Model
{
    protected $entity = 'order';
    protected $primaryKey = 'order_id';
}
