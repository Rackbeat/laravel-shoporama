<?php
/**
 * Created by PhpStorm.
 * User: nts
 * Date: 31.3.18.
 * Time: 15.37
 */

namespace KgBot\Shoporama\Builders;


use KgBot\Shoporama\Models\Supplier;

class SupplierBuilder extends Builder
{
    protected $entity = 'suppliers';
    protected $model = Supplier::class;
}