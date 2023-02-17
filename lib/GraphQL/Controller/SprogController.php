<?php

namespace Headless\GraphQL\Controller;

use Headless\Model\Sprog\WildCard;
use Headless\Services\Sprog\WildCardService;
use TheCodingMachine\GraphQLite\Annotations\Query;


class SprogController
{

    private WildCardService $wildCardService;

    public function __construct()
    {
        $this->wildCardService = new WildCardService();
    }

    /**
     * Get all wildcards
     *
     * @Query()
     * @return WildCard[]
     */
    public function getWildCards(): array
    {
        return $this->wildCardService->getAllWildCards();
    }



}
