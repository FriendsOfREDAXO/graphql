<?php

namespace RexGraphQL\Middleware;


use GraphQL\Type\Definition\InputType;
use GraphQL\Type\Definition\ResolveInfo;
use TheCodingMachine\GraphQLite\Exceptions\GraphQLException;
use TheCodingMachine\GraphQLite\Parameters\InputTypeParameterInterface;

/**
 * A parameter filled from the container.
 */
class ParameterValidator implements InputTypeParameterInterface
{
    private $parameter;
    private $parameterName;

    public function __construct(InputTypeParameterInterface $parameter, string $parameterName)
    {
        $this->parameter = $parameter;
        $this->parameterName = $parameterName;
    }

    /**
     * The "resolver" returns the actual value that will be fed to the function.
     */
    public function resolve(?object $source, array $args, $context, ResolveInfo $info)
    {
        $data = $this->parameter->resolve($source, $args, $context, $info);
        $validatorBuilder = new \Symfony\Component\Validator\ValidatorBuilder();
        $validatorBuilder->enableAnnotationMapping();
        $validator = $validatorBuilder->getValidator();

        $errors = $validator->validate($data);
        if (count($errors) > 0) {
            $errorsArray = [];
            foreach ($errors as $error) {
                $errorsArray[] = [
                    'wildcard' => $error->getMessage(),
                    'field' => $error->getPropertyPath()
                ];
            }
            throw new GraphQLException(json_encode($errorsArray));
        }
        return $data;
    }

    public function getType(): InputType
    {
        return $this->parameter->getType();
    }

    public function hasDefaultValue(): bool
    {
        return $this->parameter->hasDefaultValue();
    }

    public function getDefaultValue(): mixed
    {
        return $this->parameter->getDefaultValue();
    }
}
