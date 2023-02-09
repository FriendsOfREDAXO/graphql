<?php

namespace Headless\GraphQL\Controller;
use Headless\Model\Structure\Clang;
use Headless\Services\Structure\ClangService;
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
     * @Query()
     * @return Clang[]
     */
    public function getClangs(ID $articleId): array
    {
        return $this->service->getClangs($articleId->val());
    }
}
