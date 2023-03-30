<?php

namespace GraphQL\Controller;
use GraphQL\Type\Structure\Category;
use GraphQL\Service\Structure\CategoryService;
use TheCodingMachine\GraphQLite\Annotations\Query;


class CategoryController
{
    private CategoryService $service;
    public function __construct()
    {
        $this->service = new CategoryService();
    }

    /**
     * Get all categories in the root category
     *
     * @return Category[]
     */
    #[Query]
    public function getRootCategories(): array
    {
        return $this->service->getRootCategories();
    }

}
