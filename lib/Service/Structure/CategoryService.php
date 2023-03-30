<?php

namespace GraphQL\Service\Structure;

use GraphQL\Type\Structure\Category;


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
