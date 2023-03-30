<?php

namespace GraphQL\Type\Structure;

use GraphQL\Type\Structure\SEO\Seo;
use TheCodingMachine\GraphQLite\Types\ID;
use TheCodingMachine\GraphQLite\Annotations\Type;
use TheCodingMachine\GraphQLite\Annotations\Field;

#[Type]
class Article
{

    public \rex_article $article;

    #[Field]
    public function getId(): ID
    {
        return new ID($this->article->getId());
    }

    #[Field]
    public function getName(): string
    {
        return $this->article->getName();
    }

    #[Field]
    public function getUrl(): string
    {
        return $this->article->getUrl();
    }

    #[Field]
    public function isStartArticle(): bool
    {
        return $this->article->isStartArticle();
    }

    #[Field]
    public function isOnline(): bool
    {
        return $this->article->isOnline();
    }

    #[Field]
    public function getClang(): Clang
    {
        return Clang::getById($this->article->getClangId());
    }

    #[Field]
    public function getSeo(): Seo
    {
        return Seo::getByArticle($this->article);
    }

    /**
     * @return Breadcrumb[]
     */
    #[Field]
    public function getBreadcrumbs(): array
    {
        return Breadcrumb::getAllForArticle($this->article);
    }

    /**
     * @return ArticleSlice[]
     */
    #[Field]
    public function getSlices(): array
    {
        $slices = \rex_article_slice::getSlicesForArticle($this->article->getId());
        return array_map(function ($slice) {
            return ArticleSlice::getByObject($slice);
        }, $slices);
    }

    #[Field]
    public function getCreateDate(): string
    {
        return date(\DateTimeInterface::ISO8601, $this->article->getCreateDate());
    }

    #[Field]
    public function getUpdateDate(): string
    {
        return date(\DateTimeInterface::ISO8601, $this->article->getUpdateDate());
    }

    /**
     * @param int $id id of \rex_article
     *
     * @return Article proxy object
     */
    public static function getById(int $id): Article
    {
        $a = new Article();
        $article = \rex_article::get($id);
        if (!$article) {
            throw new \Exception("Article with id $id not found");
        }
        $a->article = $article;
        return $a;
    }

    /**
     * @param \rex_article $obj \rex_article object to encapsulate
     *
     * @return Article proxy object
     */
    public static function getByObject(\rex_article $obj): Article
    {
        $a = new Article();
        $a->article = $obj;
        return $a;
    }

}
