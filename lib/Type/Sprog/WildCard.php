<?php

namespace RexGraphQL\Type\Sprog;

use RexGraphQL\Type\Structure\Clang;
use TheCodingMachine\GraphQLite\Annotations\Field;
use TheCodingMachine\GraphQLite\Annotations\Type;
use TheCodingMachine\GraphQLite\Types\ID;

#[Type]
class WildCard
{
    private ?int $id;
    private ?string $wildcard;
    private ?string $replace;
    private ?int $clangId;

    public function __construct(int $id = null, string $wildcard= null, string $replace= null, int $clangId= null)
    {
        $this->id = $id;
        $this->wildcard = $wildcard;
        $this->replace = $replace;
        $this->clangId = $clangId;
    }

    #[Field]
    public function getId(): ID
    {
        return new ID($this->id);
    }

    #[Field]
    public function getWildcard(): string
    {
        return $this->wildcard;
    }

    #[Field]
    public function getReplace(): string
    {
        return $this->replace;
    }

    #[Field]
    public function getClang(): Clang
    {
        return Clang::getById($this->clangId);
    }

}
