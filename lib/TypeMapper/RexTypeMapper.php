<?php

namespace GraphQL\TypeMapper;

use TheCodingMachine\GraphQLite\FactoryContext;
use TheCodingMachine\GraphQLite\InputTypeUtils;
use TheCodingMachine\GraphQLite\Mappers\AbstractTypeMapper;

class RexTypeMapper extends AbstractTypeMapper
{
    private FactoryContext $context;
    public function __construct(FactoryContext $context)
    {
        parent::__construct(
            'GraphQL_Type',
            $context->getTypeGenerator(),
            $context->getInputTypeGenerator(),
            new InputTypeUtils($context->getAnnotationReader(), $context->getNamingStrategy()),
            $context->getContainer(),
            $context->getAnnotationReader(),
            $context->getNamingStrategy(),
            $context->getRecursiveTypeMapper(),
            $context->getCache());
        $this->context = $context;
    }
    protected function getClassList(): array
    {
        $namespaces = \rex_extension::registerPoint(
            new \rex_extension_point('GRAPHQL_TYPE_NAMESPACES', ['GraphQL\\Type'])
        );
        $classes = ClassFinder::findByNamespaces($namespaces);
        $classList = [];
        foreach ($classes as $class) {
            $reflection = new \ReflectionClass($class);
            $classList[$class] = $reflection;
        }
        return $classList;
    }



}
