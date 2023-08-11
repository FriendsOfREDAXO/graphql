<?php

namespace RexGraphQL\Type\Structure;

use rex_category;
use TheCodingMachine\GraphQLite\Annotations\Field;
use TheCodingMachine\GraphQLite\Annotations\Type;
use TheCodingMachine\GraphQLite\Exceptions\GraphQLException;
use TheCodingMachine\GraphQLite\Types\ID;

#[Type]
class Category
{
    public rex_category $category;

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
        return array_map(static function ($child) {
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
        return array_map(static function ($article) {
            return Article::getByObject($article);
        }, $articles);
    }

    #[Field]
    public function isOnline(): bool
    {
        return $this->category->isOnline();
    }

    /**
     * @param int $id id of \rex_category
     *
     * @throws GraphQLException if category is not online or not found
     * @return Category proxy object
     */
    public static function getById(int $id): self
    {
        $c = new self();
        $category = rex_category::get($id);
        if (!$category) {
            throw new GraphQLException("Category with id $id not found");
        }
        $c->category = $category;
        if (!$c->category->isOnline() && !\rex::getUser()) {
            throw new GraphQLException("Category with id {$c->category->getId()} is not online");
        }
        return $c;
    }

    /**
     * @param rex_category $obj \rex_category object to encapsulate
     *
     * @throws GraphQLException if category is not online
     * @return Category proxy object
     */
    public static function getByObject(rex_category $obj): self
    {
        $c = new self();
        $c->category = $obj;
        if (!$c->category->isOnline() && !\rex::getUser()) {
            throw new GraphQLException("Category with id {$c->category->getId()} is not online");
        }
        return $c;
    }
}
