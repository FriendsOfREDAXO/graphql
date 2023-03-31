<?php

namespace GraphQL\TypeMapper;

use TheCodingMachine\GraphQLite\FactoryContext;
use TheCodingMachine\GraphQLite\QueryProviderFactoryInterface;
use TheCodingMachine\GraphQLite\QueryProviderInterface;

class RexQueryProviderFactory implements QueryProviderFactoryInterface
{
    public function create(FactoryContext $context): QueryProviderInterface
    {
        return new RexQueryProvider($context);
    }
}
