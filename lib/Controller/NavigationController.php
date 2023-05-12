<?php

namespace RexGraphQL\Controller;

use GraphQL\Service\Navigation\NavigationService;
use RexGraphQL\Type\Navigation\NavigationItem;
use TheCodingMachine\GraphQLite\Annotations\Logged;
use TheCodingMachine\GraphQLite\Annotations\Query;
use TheCodingMachine\GraphQLite\Exceptions\GraphQLException;
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
     * @return NavigationItem[]
     */
    #[Query]
    #[Logged]
    public function getRootNavigation(int $depth, ?ID $articleId): array
    {
        return $this->service->getRootNavigation($depth, $articleId?->val());
    }

    /**
     * Get navigation by name
     *
     * @return NavigationItem[]
     * @throws GraphQLException
     */
    #[Query]
    #[Logged]
    public function getNavigation(string $name, ?ID $articleId): array
    {
        return $this->service->getNavigationByName($name, $articleId?->val());
    }

}
