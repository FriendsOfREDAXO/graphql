<?php

namespace RexGraphQL\Auth;
use TheCodingMachine\GraphQLite\Security\AuthenticationServiceInterface;

class AuthService implements AuthenticationServiceInterface
{

    public const AUTH_GET_PARAM_KEY = 'auth';
    private JwtService $jwtService;

    public function __construct()
    {
        $this->jwtService = new JwtService();
    }

    public const SHARED_SECRET_CONFIG_KEY = 'auth_shared_secret';

    public function isLogged(): bool
    {
        $secret = \rex_addon::get('graphql')->getConfig(self::SHARED_SECRET_CONFIG_KEY);
        $bearerToken = static::parseBearerToken();
        if(!$bearerToken && $secret) {
            return false;
        }
        if(($bearerToken && $this->jwtService->validateToken($bearerToken)) || !$secret) {
            return true;
        }
        return $bearerToken === $secret;
    }

    public function getUser(): ?object
    {
        return null;
    }

    private static function parseBearerToken(): ?string
    {
        $authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? null;
        if (null === $authHeader) {
            return null;
        }

        if (!str_starts_with($authHeader, 'Bearer ') && !str_starts_with($authHeader, 'bearer ')) {
            return null;
        }

        return trim(substr($authHeader, 7));
    }
}
