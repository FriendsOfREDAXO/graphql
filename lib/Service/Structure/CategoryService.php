<?php

namespace GraphQL\Service\Structure;

use RexGraphQL\Type\Structure\Category;
use TheCodingMachine\GraphQLite\Exceptions\GraphQLException;

class CategoryService
{

    /**
     * Get all categories in the root directory
     *
     * @return Category[]
     * @throws GraphQLException
     */
    public function getRootCategories(): array
    {
        $categories = \rex_category::getRootCategories(1);
        return array_map(function ($category) {
            return Category::getByObject($category);
        }, $categories);
    }

    public function getSelectedCategories(array $ids): array
    {
        $articles = array_map(fn($id) => Category::getById($id->val()), $ids);
        return array_filter($articles, fn($article) => $article !== null);
    }
}
