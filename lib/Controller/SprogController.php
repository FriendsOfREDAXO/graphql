<?php

namespace RexGraphQL\Controller;

use RexGraphQL\Type\Sprog\WildCard;
use GraphQL\Service\Sprog\WildCardService;
use TheCodingMachine\GraphQLite\Annotations\Logged;
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
     * @return WildCard[]
     */
    #[Query]
    #[Logged]
    public function getWildCards(): array
    {
        return $this->wildCardService->getAllWildCards();
    }

}
