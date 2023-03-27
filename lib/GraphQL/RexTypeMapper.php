<?php

namespace Headless\GraphQL;

use GraphQL\Type\Definition\OutputType;
use GraphQL\Type\Definition\Type;
use Headless\Model\Sprog\WildCard;
use Headless\Model\Structure\Article;
use Headless\Model\Structure\Clang;
use TheCodingMachine\GraphQLite\FactoryContext;
use TheCodingMachine\GraphQLite\InputTypeUtils;
use TheCodingMachine\GraphQLite\Mappers\AbstractTypeMapper;
use TheCodingMachine\GraphQLite\Mappers\TypeMapperInterface;
use TheCodingMachine\GraphQLite\Types\MutableInterface;
use TheCodingMachine\GraphQLite\Types\ResolvableMutableInputInterface;

class RexTypeMapper extends AbstractTypeMapper
{
    private FactoryContext $context;
    public function __construct(FactoryContext $context)
    {
        parent::__construct(
            'Headless_Model',
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
        $classes = \rex_extension::registerPoint(
            new \rex_extension_point('HEADLESS_GRAPHQL_MODELS', [])
        );
        $directories = \rex_extension::registerPoint(
            new \rex_extension_point('HEADLESS_GRAPHQL_MODEL_DIRECTORIES', [])
        );
        foreach ($directories as $directory) {
            $classes = array_merge($classes, ClassParser::getClassesFromDirectory($directory));
        }
        $classList = [];
        foreach ($classes as $class) {
            $reflection = new \ReflectionClass($class);
            $classList[$class] = $reflection;
        }
        return $classList;
    }



}
