<?php

namespace GraphQL\TypeMapper;

use TheCodingMachine\GraphQLite\AggregateControllerQueryProvider;
use TheCodingMachine\GraphQLite\FactoryContext;
use TheCodingMachine\GraphQLite\QueryProviderInterface;

class RexQueryProvider implements QueryProviderInterface
{

    private FactoryContext $context;
    private AggregateControllerQueryProvider $aggregateQueryProvider;

    public function __construct(FactoryContext $context)
    {
        $this->context = $context;
        $fieldsBuilder = $this->context->getFieldsBuilder();

        $namespaces = \rex_extension::registerPoint(
            new \rex_extension_point('GRAPHQL_CONTROLLER_NAMESPACES', ['GraphQL\\Controller'])
        );
        $classes = ClassFinder::findByNamespaces($namespaces);

        $this->aggregateQueryProvider = new AggregateControllerQueryProvider(
            $classes,
            $fieldsBuilder,
            $this->context->getContainer()
        );
    }

    public function getQueries(): array
    {
        return $this->aggregateQueryProvider->getQueries();
    }

    public function getMutations(): array
    {
        return $this->aggregateQueryProvider->getMutations();
    }
}
