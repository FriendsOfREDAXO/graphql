<?php

namespace RexGraphQL\Type\Structure;

use DateTimeInterface;
use rex_article;
use rex_article_slice;
use TheCodingMachine\GraphQLite\Annotations\Field;
use TheCodingMachine\GraphQLite\Annotations\Type;
use TheCodingMachine\GraphQLite\Exceptions\GraphQLException;
use TheCodingMachine\GraphQLite\Types\ID;

#[Type]
class Article
{
    public rex_article $article;

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
    public function isSiteStartArticle(): bool
    {
        return $this->article->isSiteStartArticle();
    }

    #[Field]
    public function isOnline(): bool
    {
        return $this->article->isOnline();
    }

    /**
     * @throws GraphQLException if clang is not online or not found
     */
    #[Field]
    public function getClang(): Clang
    {
        return Clang::getById($this->article->getClangId());
    }

    /**
     * @throws GraphQLException
     */
    #[Field]
    public function getMetadata(): Metadata
    {
        return Metadata::getByArticleId($this->getId()->val(), $this->getClang()->getId()->val());
    }

    /**
     * @throws GraphQLException if some slices are not online
     * @return ArticleSlice[]
     */
    #[Field]
    public function getSlices(): array
    {
        $slices = rex_article_slice::getSlicesForArticle($this->article->getId());
        $slices = array_filter($slices, static function ($slice) {
            return $slice->isOnline();
        });
        return array_map(static function ($slice) {
            return ArticleSlice::getByObject($slice);
        }, $slices);
    }

    #[Field]
    public function getCreatedAt(): string
    {
        return date(DateTimeInterface::ISO8601, $this->article->getCreateDate());
    }

    #[Field]
    public function getUpdatedAt(): string
    {
        return date(DateTimeInterface::ISO8601, $this->article->getUpdateDate());
    }

    /**
     * @param int $id id of \rex_article
     *
     * @return Article proxy object
     */
    public static function getById(int $id): self
    {
        $a = new self();
        $article = rex_article::get($id);
        if (!$article || !$article->isOnline()) {
            $article = rex_article::getNotfoundArticle();
        }
        $a->article = $article;
        return $a;
    }

    /**
     * @param rex_article $obj \rex_article object to encapsulate
     *
     * @return Article proxy object
     */
    public static function getByObject(rex_article $obj): self
    {
        $a = new self();
        if (!$obj->isOnline()) {
            $obj = rex_article::getNotfoundArticle();
        }
        $a->article = $obj;
        return $a;
    }
}
