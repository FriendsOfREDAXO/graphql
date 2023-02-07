<?php

namespace Headless\GraphQL\Controller;
use Headless\Model\Structure\Category;
use lib\Services\Structure\CategoryService;
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
     * @Query()
     * @return Category[]
     */
    public function getRootCategories(): array
    {
        return $this->service->getRootCategories();
    }

}
