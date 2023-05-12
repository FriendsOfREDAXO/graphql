<?php

namespace RexGraphQL\Controller;

use RexGraphQL\Type\Structure\Clang;
use GraphQL\Service\Structure\ClangService;
use TheCodingMachine\GraphQLite\Annotations\Logged;
use TheCodingMachine\GraphQLite\Annotations\Query;
use TheCodingMachine\GraphQLite\Types\ID;

class ClangController
{

    private ClangService $service;

    public function __construct()
    {
        $this->service = new ClangService();
    }

    /**
     * Get all available languages for an article
     *
     * @return Clang[]
     */
    #[Query]
    #[Logged]
    public function getClangs(?ID $articleId): array
    {
        return $this->service->getClangs($articleId?->val());
    }
}
