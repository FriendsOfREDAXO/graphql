<?php

namespace Headless\GraphQL;

class ClassParser
{

    /**
     * @return string[]
     */
    public static function getClassesFromDirectory(string $directory): array
    {
        $classes = [];
        $files = \rex_finder::factory($directory);
        $files->recursive(true);
        foreach ($files as $file) {
            $contents = file_get_contents($file->getPathname());
            \preg_match('/namespace\s+(.+?);/', $contents, $matches);
            $namespace = $matches[1];
            \preg_match('/class\s+(.+?)\s+/', $contents, $matches);
            $class = $matches[1];
            if($class) {
                $classes[] = $namespace . '\\' . $class;
            }
        }
        return $classes;
    }
}
