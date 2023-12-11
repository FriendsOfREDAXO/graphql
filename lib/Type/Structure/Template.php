<?php

namespace RexGraphQL\Type\Structure;

use rex_template;
use TheCodingMachine\GraphQLite\Annotations\Field;
use TheCodingMachine\GraphQLite\Annotations\Type;
use TheCodingMachine\GraphQLite\Types\ID;

#[Type]
class Template
{
    private rex_template $template;

    public function __construct(int $id)
    {
        $this->template = new rex_template($id);
    }

    #[Field]
    public function getId(): ID
    {
        return new ID($this->template->getId());
    }

    #[Field]
    public function getKey(): ?string
    {
        return $this->template->getKey();
    }

    /**
     * @param int $id
     * @return self
     */
    public static function getById(int $id): self
    {
        return new self($id);
    }
}
