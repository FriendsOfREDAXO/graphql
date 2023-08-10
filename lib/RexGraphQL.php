<?php

namespace RexGraphQL;

use GraphQL\Endpoint;

class RexGraphQL
{
    public const MODE_CONFIG_KEY = 'addon_mode';

    public const ENDPOINT_MODE_KEY = 'endpoint';
    public const HEADLESS_MODE_KEY = 'headless';
    public const DEFAULT_MODE = self::HEADLESS_MODE_KEY;


    public static function isEndpointMode(): bool
    {
        return self::getMode() == self::ENDPOINT_MODE_KEY || self::DEFAULT_MODE == self::ENDPOINT_MODE_KEY;
    }
    public static function isHeadlessMode(): bool
    {
        return self::getMode() == self::ENDPOINT_MODE_KEY || self::DEFAULT_MODE == self::ENDPOINT_MODE_KEY;
    }

    public static function getMode(): string {
        return \rex_addon::get('graphql')->getConfig(self::MODE_CONFIG_KEY);
    }
}
