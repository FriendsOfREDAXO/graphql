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
     * @Query("All WildCards")
     * @return WildCard[]
     */
    public function getWildCards(): array
    {
        return $this->wildCardService->getAllWildCards();
    }



}
