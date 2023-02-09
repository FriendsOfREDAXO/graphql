<?php

namespace Headless\Model\Structure;

use TheCodingMachine\GraphQLite\Types\ID;
use TheCodingMachine\GraphQLite\Annotations\Type;
use TheCodingMachine\GraphQLite\Annotations\Field;


/**
 * @Type()
 */
class Clang
{

    public \rex_clang $clang;

    /**
     * @Field()
     * @return ID
     */
    public function getId(): ID
    {
        return new ID($this->clang->getId());
    }

    /**
     * @Field()
     */
    public function getName(): string
    {
        return $this->clang->getName();
    }

    /**
     * @Field()
     */
    public function getCode(): string
    {
        return $this->clang->getCode();
    }

    /**
     * @Field()
     */
    public function getPriority(): int
    {
        return $this->clang->getPriority();
    }

    /**
     * @param $id int of \rex_clang
     *
     * @return Clang proxy object
     */
    public static function getById(int $id): self
    {
        $clang        = new self();
        $clang->clang = \rex_clang::get($id);
        return $clang;
    }


    /**
     * @param \rex_clang $obj \rex_clang object to encapsulate
     *
     * @return Clang proxy object
     */
    public static function getByObject(\rex_clang $obj): self
    {
        $clang        = new self();
        $clang->clang = $obj;
        return $clang;
    }
}
