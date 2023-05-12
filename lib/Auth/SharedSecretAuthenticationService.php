<?php

namespace RexGraphQL\Auth;
use TheCodingMachine\GraphQLite\Security\AuthenticationServiceInterface;

class SharedSecretAuthenticationService implements AuthenticationServiceInterface
{
    public const SHARED_SECRET_CONFIG_KEY = 'auth_shared_secret';
    public function isLogged(): bool
    {
        $secret = \rex_addon::get('graphql')->getConfig(self::SHARED_SECRET_CONFIG_KEY);
        if(!$secret) {
            return true;
        }
        $bearerToken = AuthHelpers::parseBearerToken();
        if(!$bearerToken) {
            return false;
        }

        return $bearerToken === $secret;
    }

    public function getUser(): ?object
    {
        return null;
    }
}
