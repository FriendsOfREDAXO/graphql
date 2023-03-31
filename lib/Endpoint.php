<?php

namespace GraphQL;

use DI\Container;
use GraphQL\TypeMapper\RexQueryProviderFactory;
use GraphQL\TypeMapper\RexTypeMapperFactory;
use TheCodingMachine\GraphQLite\Context\Context;
use TheCodingMachine\GraphQLite\Schema;
use TheCodingMachine\GraphQLite\SchemaFactory;
use Yiisoft\Cache\ArrayCache;

class Endpoint
{
    private SchemaFactory $schemaFactory;

    public function __construct()
    {
        $this->schemaFactory = new SchemaFactory(
            new ArrayCache(), new Container()
        );
    }

    public function executeQuery(): \GraphQL\Executor\ExecutionResult
    {
        $input = $this->readInput();
        $schema = $this->generateSchema();

        return \GraphQL\GraphQL::executeQuery(
            $schema,
            $input['query'],
            null,
            new Context(),
            $input['variables'] ?? null
        );
    }

    private function generateSchema(): Schema
    {
        $this->schemaFactory->addTypeMapperFactory(new RexTypeMapperFactory());
        $this->schemaFactory->addQueryProviderFactory(new RexQueryProviderFactory());
        return $this->schemaFactory->createSchema();
    }

    private function readInput(): array
    {
        $rawInput = file_get_contents('php://input');
        $value = \rex_var::toArray($rawInput);
        if (is_array($value)) {
            return $value;
        }
        return [];
    }

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
        $output = \rex_extension::registerPoint(new \rex_extension_point('GRAPHQL_OUTPUT_FILTER', $output));
        \rex_response::sendContent($output, 'application/json');
        exit;
    }

    private static function isDebug(): bool
    {
        return \rex_extension::registerPoint(
            new \rex_extension_point('GRAPHQL_DEBUG', \rex::isDebugMode())
        );
    }
}
