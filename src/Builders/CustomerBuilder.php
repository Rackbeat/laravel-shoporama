<?php

namespace KgBot\Shoporama\Builders;


use KgBot\Shoporama\Models\Customer;

class CustomerBuilder extends Builder
{
    protected $entity = 'customers';
    protected $model = Customer::class;
}