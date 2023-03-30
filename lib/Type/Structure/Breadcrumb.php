<?php

namespace GraphQL\Type\Structure;

use _PHPStan_4dd92cd93\Nette\Neon\Exception;
use TheCodingMachine\GraphQLite\Annotations\Type;
use TheCodingMachine\GraphQLite\Annotations\Field;
use TheCodingMachine\GraphQLite\Types\ID;

#[Type]
class Breadcrumb
{
    private int $id;
    private string $label;
    private string $url;

    public function __construct(int $id, string $label, string $url)
    {
        $this->id = $id;
        $this->label = $label;
        $this->url = $url;
    }

    #[Field]
    public function getId(): ID
    {
        return new ID($this->id);
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

    public static function getAllForArticle(\rex_article $article): array
    {
        $breadcrumb = [];
        if ($article->isSiteStartArticle()) {
            return $breadcrumb;
        }
        $siteStartArticle = \rex_article::getSiteStartArticle();
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

    public static function getByArticleId(int $articleId): ?self
    {
        $article = \rex_article::get($articleId);
        if ($article instanceof \rex_article) {
            return self::getByArticle($article);
        }
        throw new \Exception("Article with id $articleId not found");
    }

    public static function getByCategoryId(int $categoryId): ?self
    {
        $category = \rex_category::get($categoryId);
        if ($category instanceof \rex_category) {
            return self::getByCategory($category);
        }
        throw new \Exception("Category with id $categoryId not found");
    }

    public static function getByCategory(\rex_category $category): self
    {
        return new self($category->getId(), $category->getName(), $category->getUrl());
    }

    public static function getByArticle(\rex_article $article): self
    {
        return new self($article->getId(), $article->getName(), $article->getUrl());
    }

}
