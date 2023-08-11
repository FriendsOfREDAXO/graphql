<?php

namespace RexGraphQL\Controller;

use GraphQL\Service\Auth\AuthService;
use TheCodingMachine\GraphQLite\Annotations\Query;

class AuthController
{
    private AuthService $service;
    public function __construct()
    {
        $this->service = new AuthService();
    }

    #[Query]
    public function isRedaxoLoggedIn(): bool
    {
        return $this->service->isRedaxoLoggedIn();
    }

}
