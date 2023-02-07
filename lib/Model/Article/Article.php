<?php

namespace Headless\Model\Article;

use TheCodingMachine\GraphQLite\Annotations\Field;
use TheCodingMachine\GraphQLite\Types\ID;
use TheCodingMachine\GraphQLite\Annotations\Type;

/**
 * @Type()
 */
class Article
{

    public \rex_article $article;

    /**
     * @Field()
     * @return ID
     */
    public function getId(): ID
    {
        return new ID($this->article->getId());
    }

    /**
     * @Field()
     * @return string
     */
    public function getName(): string
    {
        return $this->article->getName();
    }

    /**
     * @Field()
     * @return string
     */
    public function getUrl(): string
    {
        return $this->article->getUrl();
    }

    /**
     * @param int $id id of \rex_article
     * @return Article proxy object
     */
    public static function getById(int $id): Article
    {
        $a = new Article();
        $a->article = \rex_article::get($id);
        return $a;
    }


    /**
     * @param \rex_article $obj \rex_article object to encapsulate
     * @return Article proxy object
     */
    public static function getByObject(\rex_article $obj): Article
    {
        $a = new Article();
        $a->article = $obj;
        return $a;
    }


}