<?php

namespace lib\Services\Structure;

use Headless\Model\Structure\Category;


class CategoryService
{

    /**
     * Get all categories in the root directory
     *
     * @return Category[]
     */
    public function getRootCategories(): array
    {
        $categories = \rex_category::getRootCategories(1);
        return array_map(function($category) {
            return Category::getByObject($category);
        }, $categories);
    }
}
