<?php

namespace Headless\GraphQL;

use DI\Container;
use http\Exception\InvalidArgumentException;
use TheCodingMachine\GraphQLite\Context\Context;
use TheCodingMachine\GraphQLite\Exceptions\GraphQLException;
use TheCodingMachine\GraphQLite\Mappers\GlobTypeMapper;
use TheCodingMachine\GraphQLite\Schema;
use TheCodingMachine\GraphQLite\SchemaFactory;
use TheCodingMachine\GraphQLite\TypeGenerator;
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
        // throw new \InvalidArgumentException('Error parsing GraphQL input: "' . $rawInput . '"');
    }

    private function setLanguage(): void
    {
        $clangId = rex_get('clangId', 'int', \rex_clang::getCurrentId());
        \rex_clang::setCurrentId($clangId);
    }
}
