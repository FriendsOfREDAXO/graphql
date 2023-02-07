<?php

namespace Headless\Model\Article;

use TheCodingMachine\GraphQLite\Types\ID;
use TheCodingMachine\GraphQLite\Annotations\Type;
use TheCodingMachine\GraphQLite\Annotations\Field;

/**
 * @Type()
 */
class Category
{

    public \rex_category $category;

    /**
     * @Field()
     * @return ID
     */
    public function getId(): ID
    {
        return new ID($this->category->getId());
    }


    /**
     * @Field()
     * @return Category[]
     */
    public function getChildren(): array
    {
        $children = $this->category->getChildren();
        return array_map(function($child) {
            return self::getByObject($child);
        }, $children);
    }





    /**
     * @param $id id of \rex_category
     * @return Category proxy object
     */
    public static function getById($id): Category
    {
        $c = new Category();
        $c->category = \rex_category::get($id);
        return $c;
    }


    /**
     * @param \rex_category $obj \rex_category object to encapsulate
     * @return Category proxy object
     */
    public static function getByObject(\rex_category $obj): Category
    {
        $c = new Category();
        $c->category = $obj;
        return $c;
    }

}
