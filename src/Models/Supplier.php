<?php

namespace KgBot\Shoporama\Models;


use KgBot\Shoporama\Utils\Model;

class Supplier extends Model
{
    public $number;
    public $company_name;

    protected $entity = 'supplier';
    protected $primaryKey = 'supplier_id';
}