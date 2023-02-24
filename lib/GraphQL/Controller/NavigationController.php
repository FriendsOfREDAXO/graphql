<?php

namespace Headless\GraphQL\Controller;
use Headless\Model\Navigation\NavigationItem;
use Kreatif\Services\Navigation\NavigationService;
use TheCodingMachine\GraphQLite\Annotations\Query;
use TheCodingMachine\GraphQLite\Types\ID;


class NavigationController
{
    private NavigationService $service;

    public function __construct()
    {
        $this->service = new NavigationService();
    }

    /**
     * Get page navigation
     *
     * @Query()
     * @return NavigationItem[]
     */
    public function getRootNavigation(int $depth, ID $articleId): array
    {
        return $this->service->getRootNavigation($depth, $articleId->val());
    }

    /**
     * Get navigation by name
     *
     * @Query()
     * @return NavigationItem[]
     */
    public function getNavigation(string $name, ID $articleId): array
    {
        return $this->service->getNavigationByName($name, $articleId->val());
    }



}
