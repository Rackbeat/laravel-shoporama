<?php

namespace KgBot\Shoporama\Builders;


use KgBot\Shoporama\Models\Supplier;

class SupplierBuilder extends Builder
{
    protected $entity = 'suppliers';
    protected $model = Supplier::class;
}