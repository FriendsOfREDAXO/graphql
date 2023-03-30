<?php

namespace GraphQL\Type\Structure;

use TheCodingMachine\GraphQLite\Types\ID;
use TheCodingMachine\GraphQLite\Annotations\Type;
use TheCodingMachine\GraphQLite\Annotations\Field;

#[Type]
class Category
{

    public \rex_category $category;

    #[Field]
    public function getId(): ID
    {
        return new ID($this->category->getId());
    }

    /**
     * @return Category[]
     */
    #[Field]
    public function getChildren(): array
    {
        $children = $this->category->getChildren();
        return array_map(function ($child) {
            return self::getByObject($child);
        }, $children);
    }

    #[Field]
    public function getName(): string
    {
        return $this->category->getName();
    }

    #[Field]
    public function getUrl(): string
    {
        return $this->category->getUrl();
    }

    #[Field]
    public function getStartArticle(): Article
    {
        return Article::getByObject($this->category->getStartArticle());
    }

    /**
     * @return Article[]
     */
    #[Field]
    public function getArticles(): array
    {
        $articles = $this->category->getArticles(1);
        return array_map(function ($article) {
            return Article::getByObject($article);
        }, $articles);
    }

    /**
     * @param $id id of \rex_category
     *
     * @return Category proxy object
     */
    public static function getById(int $id): Category
    {
        $c = new Category();
        $category = \rex_category::get($id);
        if (!$category) {
            throw new \Exception("Category with id $id not found");
        }
        $c->category = $category;
        return $c;
    }

    /**
     * @param \rex_category $obj \rex_category object to encapsulate
     *
     * @return Category proxy object
     */
    public static function getByObject(\rex_category $obj): Category
    {
        $c = new Category();
        $c->category = $obj;
        return $c;
    }

}
