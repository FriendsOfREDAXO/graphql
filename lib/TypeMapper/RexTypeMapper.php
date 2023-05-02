<?php

namespace GraphQL;

use ReflectionClass;
use rex_extension;
use rex_extension_point;
use TheCodingMachine\GraphQLite\FactoryContext;
use TheCodingMachine\GraphQLite\InputTypeUtils;
use TheCodingMachine\GraphQLite\Mappers\AbstractTypeMapper;

use function count;

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
        $namespaces = rex_extension::registerPoint(
            new rex_extension_point('GRAPHQL_TYPE_NAMESPACES', ['RexGraphQL\\Type']),
        );
        $classes = ClassFinder::findByNamespaces($namespaces);
        $classList = [];
        foreach ($classes as $class) {
            $refClass = new ReflectionClass($class);
            $classList[$class] = $refClass;
            if ($this->isAddableToContainer($refClass)) {
                $this->context->getContainer()->set($class, new $class());
            }
        }
        return $classList;
    }

    private function isAddableToContainer(ReflectionClass $refClass): bool
    {
        if ($refClass->isInstantiable() && !$refClass->isAbstract() && !$refClass->isInterface()) {
            $constructor = $refClass->getConstructor();
            if (!$constructor) {
                return true;
            }
            $parameters = $constructor->getParameters();
            $requiredParameters = array_filter($parameters, static function ($parameter) {
                return !$parameter->isOptional();
            });
            if (0 === count($requiredParameters)) {
                return true;
            }
        }
        return false;
    }
}
