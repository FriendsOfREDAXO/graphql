<?php

namespace RexGraphQL\Connector\Sprog\Controller;

use RexGraphQL\Connector\Sprog\Service\WildCardService;
use RexGraphQL\Connector\Sprog\Type\WildCard;
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

    /**
     * @param string[] $keys
     * @return WildCard[]
     */
    #[Query]
    public function getSelectedWildCards(array $keys): array
    {
        return $this->wildCardService->getSelectedWildCards($keys);
    }

    /**
     * Get wildcard by key
     */
    #[Query]
    public function getWildCard(string $key): ?WildCard
    {
        return $this->wildCardService->getWildCard($key);
    }

}
