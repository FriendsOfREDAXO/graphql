<?php

namespace Headless\GraphQL;

use GraphQL\Type\Definition\FieldDefinition;
use Headless\GraphQL\Controller\SprogController;
use TheCodingMachine\GraphQLite\AggregateControllerQueryProvider;
use TheCodingMachine\GraphQLite\AggregateQueryProvider;
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
        $classes = \rex_extension::registerPoint(
            new \rex_extension_point('HEADLESS_GRAPHQL_CONTROLLERS', [])
        );
        $directories = \rex_extension::registerPoint(
            new \rex_extension_point('HEADLESS_GRAPHQL_CONTROLLER_DIRECTORIES', [])
        );

        foreach ($directories as $directory) {
            $classes = array_merge($classes, ClassParser::getClassesFromDirectory($directory));
        }

        $this->aggregateQueryProvider = new AggregateControllerQueryProvider(
            $classes,
            $fieldsBuilder,
            $this->context->getContainer()
        );
    }

    /**
     * @return FieldDefinition[]
     */
    public function getQueries(): array
    {

        return $this->aggregateQueryProvider->getQueries();
    }

    /**
     * @return FieldDefinition[]
     */
    public function getMutations(): array
    {
        return $this->aggregateQueryProvider->getMutations();
    }
}
