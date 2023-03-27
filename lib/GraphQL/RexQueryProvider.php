<?php

namespace Headless\GraphQL;

use Headless\GraphQL\Controller\SprogController;
use Headless\GraphQL\Controller\TestController;
use TheCodingMachine\GraphQLite\AggregateControllerQueryProvider;
use TheCodingMachine\GraphQLite\AggregateQueryProvider;
use TheCodingMachine\GraphQLite\FactoryContext;
use TheCodingMachine\GraphQLite\QueryProviderInterface;

class RexQueryProvider implements QueryProviderInterface
{

    private FactoryContext $context;

    public function __construct(FactoryContext $context)
    {
        $this->context = $context;
    }

    public function getQueries(): array
    {
        $fieldsBuilder = $this->context->getFieldsBuilder();
        $provider = new AggregateControllerQueryProvider(\rex_extension::registerPoint(
            new \rex_extension_point('HEADLESS_GRAPHQL_CONTROLLERS', [])
        ),
            $fieldsBuilder,
            $this->context->getContainer()
        );
        return $provider->getQueries();
    }

    public function getMutations(): array
    {
        return [];
    }
}
