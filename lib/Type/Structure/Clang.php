<?php

namespace GraphQL\Type\Structure;

use TheCodingMachine\GraphQLite\Types\ID;
use TheCodingMachine\GraphQLite\Annotations\Type;
use TheCodingMachine\GraphQLite\Annotations\Field;

#[Type]
class Clang
{
    public bool $isActive = false;
    public ?string $url = null;

    public \rex_clang $clang;

    #[Field]
    public function getId(): ID
    {
        return new ID($this->clang->getId());
    }

    #[Field]
    public function getName(): string
    {
        return $this->clang->getName();
    }

    #[Field]
    public function getCode(): string
    {
        return $this->clang->getCode();
    }

    #[Field]
    public function getPriority(): int
    {
        return $this->clang->getPriority();
    }

    #[Field]
    public function isActive(): bool
    {
        return $this->isActive;
    }

    #[Field]
    public function getUrl(): ?string
    {
        return $this->url;
    }

    /**
     * @param $id int of \rex_clang
     *
     * @return Clang proxy object
     */
    public static function getById(int $id): self
    {
        $clang = new self();
        $lang = \rex_clang::get($id);
        if (!$lang) {
            throw new \Exception("CLang with id $id not found");
        }
        if(!$lang->isOnline()) {
            throw new \Exception("Clang with id {$lang->getId()} is not online");
        }
        $clang->clang = $lang;
        return $clang;
    }

    /**
     * @param \rex_clang $obj \rex_clang object to encapsulate
     *
     * @return Clang proxy object
     */
    public static function getByObject(\rex_clang $obj): self
    {
        $clang = new self();
        $clang->clang = $obj;
        if(!$obj->isOnline()) {
            throw new \Exception("Clang with id {$obj->getId()} is not online");
        }
        return $clang;
    }
}
