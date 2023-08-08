<?php

namespace RexGraphQL\Connector\Navbuilder\Controller;

use RexGraphQL\Connector\Navbuilder\Service\NavbuilderService;
use RexGraphQL\Type\Navigation\NavigationItem;
use TheCodingMachine\GraphQLite\Annotations\Logged;
use TheCodingMachine\GraphQLite\Annotations\Query;
use TheCodingMachine\GraphQLite\Exceptions\GraphQLException;
use TheCodingMachine\GraphQLite\Types\ID;

class NavbuilderController
{

    private NavbuilderService $service;

    public function __construct()
    {
        $this->service = new NavbuilderService();
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
