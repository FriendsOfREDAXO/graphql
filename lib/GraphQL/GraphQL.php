<?php

namespace Headless\GraphQL;

use DI\Container;
use JetBrains\PhpStorm\NoReturn;

class GraphQL
{
    public static function registerEndpoint(): void
    {
        $endpoint = new Endpoint();
        foreach (static::getControllerNamespaces() as $namespace) {
            $endpoint->addControllerNamespace($namespace);
        }
        foreach (static::getTypeNamespaces() as $namespace) {
            $endpoint->addTypeNamespace($namespace);
        }
        foreach (static::getControllers() as $controller) {
            $endpoint->addController($controller);
        }
        $result = $endpoint->executeQuery();
        $isDebug = static::isDebug();
        $output = $result->toArray($isDebug);
        static::sendResponse($output);
    }

    private static function sendResponse(array $output): void
    {
        static::addCorsHeaders();
        \rex_response::cleanOutputBuffers();
        \rex_response::sendCacheControl();
        \rex_response::setStatus(\rex_response::HTTP_OK);
        $output = json_encode($output);
        $output = \rex_extension::registerPoint(new \rex_extension_point('HEADLESS_OUTPUT_FILTER', $output));
        \rex_response::sendContent($output, 'application/json');
        exit;
    }

    private static function addCorsHeaders(): void
    {
        if (isset($_SERVER['HTTP_ORIGIN'])) {
            // Decide if the origin in $_SERVER['HTTP_ORIGIN'] is one
            // you want to allow, and if so:
            header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
            header('Access-Control-Allow-Credentials: true');
            header('Access-Control-Max-Age: 86400');    // cache for 1 day
        }

        // Access-Control headers are received during OPTIONS requests
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'])) // may also be using PUT, PATCH, HEAD etc
            {
                header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
            }

            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS'])) {
                header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
            }
            exit;
        }
    }

    private static function getControllerNamespaces(): array
    {
        return \rex_extension::registerPoint(
            new \rex_extension_point('HEADLESS_GRAPHQL_CONTROLLER_NAMESPACES', ['Headless\\GraphQL\\Controller\\'])
        );
    }

    private static function getTypeNamespaces(): array
    {
        return \rex_extension::registerPoint(
            new \rex_extension_point('HEADLESS_GRAPHQL_TYPE_NAMESPACES', ['Headless\\Model\\'])
        );
    }

    private static function getControllers(): array
    {
        return \rex_extension::registerPoint(
            new \rex_extension_point('HEADLESS_GRAPHQL_CONTROLLERS', [])
        );
    }

    private static function isDebug(): bool
    {
        return \rex_extension::registerPoint(
            new \rex_extension_point('HEADLESS_GRAPHQL_DEBUG', \rex::isDebugMode())
        );
    }
}
