<?php

namespace GraphQL;

use TheCodingMachine\GraphQLite\FactoryContext;
use TheCodingMachine\GraphQLite\Mappers\TypeMapperFactoryInterface;
use TheCodingMachine\GraphQLite\Mappers\TypeMapperInterface;

class RexTypeMapperFactory implements TypeMapperFactoryInterface
{

    public function create(FactoryContext $context): TypeMapperInterface
    {
        return new RexTypeMapper($context);
    }
}
