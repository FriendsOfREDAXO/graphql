<?php

declare(strict_types=1);

namespace RexGraphQL\Annotations;

use Attribute;
use TheCodingMachine\GraphQLite\Annotations\ParameterAnnotationInterface;


/**
 * Use this annotation to validate a parameter for a query or mutation.
 *
 * @Annotation
 * @Target({"METHOD"})
 * @Attributes({
 * })
 */
#[Attribute(Attribute::TARGET_PARAMETER)]
class Validate implements ParameterAnnotationInterface
{
    public function __construct()
    {

    }

    public function getTarget(): string
    {
        return '';
    }
}
