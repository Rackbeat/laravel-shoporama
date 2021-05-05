<?php

namespace KgBot\Shoporama\Builders;

use KgBot\Shoporama\Models\Category;

class CategoryBuilder extends Builder
{
    protected $entity = 'category';
    protected $model = Category::class;
}