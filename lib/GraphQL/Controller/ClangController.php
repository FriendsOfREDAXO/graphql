<?php

namespace Headless\GraphQL\Controller;
use Headless\Model\Structure\Clang;
use Headless\Services\Structure\ClangService;
use TheCodingMachine\GraphQLite\Annotations\Query;

class ClangController
{

    private ClangService $service;

    public function __construct()
    {
        $this->service = new ClangService();
    }

    /**
     * Get all available languages
     * @Query()
     * @return Clang[]
     */
    public function getClangs(): array
    {
        return $this->service->getClangs();
    }
}
