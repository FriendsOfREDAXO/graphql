<?php

namespace Headless\GraphQL;

use DI\Container;
use JetBrains\PhpStorm\NoReturn;

class GraphQL
{
    public static function registerEndpoint(): void
    {
        $endpoint = new Endpoint();
        $result = $endpoint->executeQuery();
        $isDebug = static::isDebug();
        $output = $result->toArray($isDebug);
        static::sendResponse($output);
    }

    private static function sendResponse(array $output): void
    {
        \rex_response::cleanOutputBuffers();
        \rex_response::sendCacheControl();
        \rex_response::setStatus(\rex_response::HTTP_OK);
        \rex_response::setHeader('Access-Control-Allow-Origin', '*');
        \rex_response::setHeader('Access-Control-Allow-Headers', 'Content-Type');
        $output = json_encode($output);
        $output = \rex_extension::registerPoint(new \rex_extension_point('HEADLESS_OUTPUT_FILTER', $output));
        \rex_response::sendContent($output, 'application/json');
        exit;
    }

    private static function isDebug(): bool
    {
        return \rex_extension::registerPoint(
            new \rex_extension_point('HEADLESS_GRAPHQL_DEBUG', \rex::isDebugMode())
        );
    }
}
