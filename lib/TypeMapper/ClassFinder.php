<?php

namespace GraphQL;

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
                    $classList[] = $class;
                }
            }
        }
        return $classList;
    }
}
