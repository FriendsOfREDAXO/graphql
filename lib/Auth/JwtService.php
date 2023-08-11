<?php

namespace RexGraphQL\Auth;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JwtService
{
    const JWT_SETTINGS_KEY = 'jwt_secret';
    const KEY_ALGORITHM = 'HS256';
    public function validateToken(string $token): bool
    {
        $key = $this->getKey();
        if(!$key) {
            return false;
        }
        $key = new Key($key, self::KEY_ALGORITHM);
        $token = JWT::decode($token, $key);
        if($token->userId > 0) {
            $user = \rex_user::get($token->userId);
            \rex::setProperty('user', $user);
            return true;
        }
        return false;
    }

    public function getKey(): ?string
    {
        return \rex_addon::get('graphql')->getConfig(self::JWT_SETTINGS_KEY);
    }

    public function generateToken(): ?string {
        $user = \rex::getUser();
        $key = $this->getKey();
        if($user && $key) {
            $payload = [
                'userId' => $user->getId()
            ];
            return JWT::encode($payload, $key, self::KEY_ALGORITHM);
        }
        return null;
    }
}
