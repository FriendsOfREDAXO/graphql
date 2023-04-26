<?php

namespace GraphQL\Type\Structure;

use rex;
use rex_article;
use rex_category;
use rex_exception;
use TheCodingMachine\GraphQLite\Annotations\Field;
use TheCodingMachine\GraphQLite\Annotations\Type;
use TheCodingMachine\GraphQLite\Exceptions\GraphQLException;
use Url\UrlManager;

#[Type]
class Breadcrumb
{
    private string $label;
    private string $url;

    public function __construct(string $label, string $url)
    {
        $this->label = $label;
        $this->url = $url;
    }

    #[Field]
    public function getLabel(): string
    {
        return $this->label;
    }

    #[Field]
    public function getUrl(): string
    {
        return $this->url;
    }

    public static function getAllForArticle(rex_article $article): array
    {
        $breadcrumb = [];
        if ($article->isSiteStartArticle()) {
            return $breadcrumb;
        }
        $siteStartArticle = rex_article::getSiteStartArticle();
        $breadcrumb[] = static::getByArticle($siteStartArticle);
        $path = $article->getPathAsArray();
        foreach ($path as $id) {
            $breadcrumb[] = static::getByCategoryId($id);
        }
        $lastCategory = $article->getCategory();
        if ($article->isStartArticle() && $lastCategory->getName() === $article->getName()) {
            return $breadcrumb;
        }
        $breadcrumb[] = static::getByArticle($article);
        return $breadcrumb;
    }

    /**
     * @throws rex_exception if url object not found
     * @throws GraphQLException if no url object found
     */
    public static function getAllForUrlObject(): array
    {
        /** @var UrlManager $urlObject */
        $urlObject = rex::getProperty('url_object');
        if (null === $urlObject) {
            throw new GraphQLException('No url object found');
        }
        $articleId = $urlObject->getProfile()->getArticleId();
        if (0 === $articleId) {
            throw new GraphQLException('No article found for url object');
        }
        $article = rex_article::get($articleId);
        if (null === $article) {
            throw new GraphQLException('No article found for url object');
        }
        $breadcrumbs = static::getAllForArticle($article);
        $breadcrumbs[] = new self(
            $urlObject->getSeoTitle(),
            $urlObject->getUrl()->getPath(),
        );
        return $breadcrumbs;
    }

    /**
     * @throws GraphQLException
     */
    private static function getByArticleId(int $articleId): ?self
    {
        $article = rex_article::get($articleId);
        if ($article instanceof rex_article) {
            return self::getByArticle($article);
        }
        throw new GraphQLException("Article with id $articleId not found");
    }

    /**
     * @throws GraphQLException
     */
    private static function getByCategoryId(int $categoryId): ?self
    {
        $category = rex_category::get($categoryId);
        if ($category instanceof rex_category) {
            return self::getByCategory($category);
        }
        throw new GraphQLException("Category with id $categoryId not found");
    }

    private static function getByCategory(rex_category $category): self
    {
        return new self($category->getName(), $category->getUrl());
    }

    private static function getByArticle(rex_article $article): self
    {
        return new self($article->getName(), $article->getUrl());
    }
}
