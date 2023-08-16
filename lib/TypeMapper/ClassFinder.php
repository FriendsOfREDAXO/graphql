<?php

namespace RexGraphQL;

class ClassFinder
{
    public static function findByNamespaces(array $namespaces): array
    {
        $classes = \rex_autoload::getClasses();
        $classList = [];
        foreach ($namespaces as $namespace) {
            $namespace = strtolower($namespace);
            foreach ($classes as $class) {
                if (str_starts_with($class, $namespace)) {
                    $clazz = new \ReflectionClass($class);
                    $classList[] = $clazz->getName();
                }
            }
        }
        return $classList;
    }
}
