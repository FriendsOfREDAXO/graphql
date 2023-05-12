<?php

namespace RexGraphQL\Auth;

class AuthHelpers
{

    public static function parseBearerToken(): ?string
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
