<?php

namespace RexGraphQL\Connector;

class Connector
{
    public static function init(): void
    {
        \rex_extension::register('GRAPHQL_TYPE_NAMESPACES', [self::class, 'ext__addTypeNamespaces'], \rex_extension::LATE);
        \rex_extension::register('GRAPHQL_CONTROLLER_NAMESPACES', [self::class, 'ext__addControllerNamespaces'], \rex_extension::LATE);
    }

    public static function ext__addTypeNamespaces(\rex_extension_point $ep)
    {
        $subject = $ep->getSubject();
        if(static::checkIfAddonIsAvailable('sprog')) {
            $subject[] = 'RexGraphQL\\Connector\\Sprog\\Type';
        }
        return $subject;
    }

    public static function ext__addControllerNamespaces(\rex_extension_point $ep)
    {
        $subject = $ep->getSubject();
        if(static::checkIfAddonIsAvailable('sprog')) {
            $subject[] = 'RexGraphQL\\Connector\\Sprog\\Controller';
        }
        if(static::checkIfAddonIsAvailable('navbuilder')) {
            $subject[] = 'RexGraphQL\\Connector\\Navbuilder\\Controller';
        }
        return $subject;
    }


    private static function checkIfAddonIsAvailable(string $addon): bool
    {
        return \rex_addon::exists($addon) && \rex_addon::get($addon)->isAvailable();
    }
}
