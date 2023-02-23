<?php

namespace Headless\GraphQL\Controller;
use Headless\Model\Navigation\NavigationItem;
use Kreatif\Services\Navigation\NavigationService;
use TheCodingMachine\GraphQLite\Annotations\Query;


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
    public function getRootNavigation(int $depth): array
    {
        return $this->service->getRootNavigation($depth);
    }



}
