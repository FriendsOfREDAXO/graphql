<?php

namespace RexGraphQL\Middleware;

use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\Type;
use ReflectionParameter;
use RexGraphQL\Annotations\Validate;
use Symfony\Component\DependencyInjection\ContainerInterface;
use TheCodingMachine\GraphQLite\Annotations\ParameterAnnotations;
use TheCodingMachine\GraphQLite\Exceptions\GraphQLException;
use TheCodingMachine\GraphQLite\Mappers\Parameters\ParameterHandlerInterface;
use TheCodingMachine\GraphQLite\Mappers\Parameters\ParameterMiddlewareInterface;
use TheCodingMachine\GraphQLite\Parameters\InputTypeParameterInterface;
use TheCodingMachine\GraphQLite\Parameters\ParameterInterface;

/**
 * A parameter filled from the container.
 */
class ValidateFieldMiddleware implements ParameterMiddlewareInterface
{
    private ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function mapParameter(ReflectionParameter $parameter, DocBlock $docBlock, ?Type $paramTagType, ParameterAnnotations $parameterAnnotations, ParameterHandlerInterface $next): ParameterInterface
    {
        $validateAnnotations = $parameterAnnotations->getAnnotationsByType(Validate::class);

        $realParameter = $next->mapParameter($parameter, $docBlock, $paramTagType, $parameterAnnotations);

        if (empty($validateAnnotations)) {
            return $realParameter;
        }
        if (!$realParameter instanceof InputTypeParameterInterface) {
            throw new GraphQLException(sprintf('The @Validate annotation can only be used on input types. Parameter $%s is not an input type.', $parameter->getName()));
        }
        // We found a Validate annotation, let's return a parameter resolver.
        return new ParameterValidator($realParameter, $parameter->getName());
    }
}
