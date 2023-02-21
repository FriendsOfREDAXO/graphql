<?php

namespace Headless\GraphQL;

use DI\Container;
use http\Exception\InvalidArgumentException;
use TheCodingMachine\GraphQLite\Context\Context;
use TheCodingMachine\GraphQLite\Exceptions\GraphQLException;
use TheCodingMachine\GraphQLite\Schema;
use TheCodingMachine\GraphQLite\SchemaFactory;
use Yiisoft\Cache\ArrayCache;

class Endpoint
{
    private Container $container;
    private SchemaFactory $schemaFactory;

    public function __construct()
    {
        $this->container = new Container();
        $this->schemaFactory = new SchemaFactory(
            new ArrayCache(), $this->container
        );
    }

    public function addControllerNamespace(string $namespace): void
    {
        $this->schemaFactory->addControllerNamespace($namespace);
    }

    public function addTypeNamespace(string $namespace): void
    {
        $this->schemaFactory->addTypeNamespace($namespace);
    }

    public function addController(string $controller): void
    {
        $this->container->set($controller, new $controller());
    }


    public function executeQuery(): \GraphQL\Executor\ExecutionResult
    {
        $this->setLanguage();
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
        // throw new \InvalidArgumentException('Error parsing GraphQL input: "' . $rawInput . '"');
    }

    private function setLanguage(): void
    {
        $clangId = rex_get('clangId', 'int', \rex_clang::getCurrentId());
        \rex_clang::setCurrentId($clangId);
    }
}
