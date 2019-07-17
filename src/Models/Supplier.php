<?php
/**
 * Created by PhpStorm.
 * User: nts
 * Date: 31.3.18.
 * Time: 16.48
 */

namespace KgBot\Shoporama\Models;


use KgBot\Shoporama\Utils\Model;

class Supplier extends Model
{
    public $number;
    public $company_name;

    protected $entity = 'supplier';
    protected $primaryKey = 'supplier_id';
}