<?php

namespace GraphQL;

class ClassFinder
{
    // private static array $cache = [];
    // private static array $cacheTTl = [];

    // private static function getCacheKey(array $namespaces): string
    // {
    //     return md5(implode(',', $namespaces));
    // }
    //
    // private static function getCache(array $namespaces): array
    // {
    //     $cacheKey = self::getCacheKey($namespaces);
    //     if (isset(self::$cache[$cacheKey]) && isset(self::$cacheTTl[$cacheKey]) && self::$cacheTTl[$cacheKey] > time(
    //         )) {
    //         return self::$cache[$cacheKey];
    //     }
    //     return [];
    // }
    //
    // private static function setCache(array $namespaces, array $classes): void
    // {
    //     $cacheKey = self::getCacheKey($namespaces);
    //     self::$cache[$cacheKey] = $classes;
    //     self::$cacheTTl[$cacheKey] = time() + 10;
    // }

    public static function findByNamespaces(array $namespaces): array
    {
        $classes = \rex_autoload::getClasses();
        // $cache = self::getCache($namespaces);
        // if (!empty($cache)) {
        //     return $cache;
        // }
        $classList = [];
        foreach ($namespaces as $namespace) {
            $namespace = strtolower($namespace);
            foreach ($classes as $class) {
                if (str_starts_with($class, $namespace)) {
                    $classList[] = $class;
                }
            }
        }
        // self::setCache($namespaces, $classList);
        return $classList;
    }
}
