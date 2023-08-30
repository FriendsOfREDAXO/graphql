<?php

namespace GraphQL\Service\Structure;

use RexGraphQL\Type\Structure\Article;

class ArticleService
{
    private ContentTypeService $contentTypeService;
    public function __construct()
    {
        $this->contentTypeService = new ContentTypeService();
    }

    public function getArticleByPath(string $path): Article
    {
        $contentType = $this->contentTypeService->getContentTypeByPath($path);
        if($contentType->getType() == 'article') {
            return Article::getById($contentType->getElementId()->val());
        }
        return Article::getById(\rex_article::getNotfoundArticleId());
    }

    public function getRootArticles(): array
    {
        $articles = \rex_article::getRootArticles();
        return array_map(function ($article) {
            return Article::getByObject($article);
        }, $articles);
    }

    public function getArticleById(int $id): ?Article
    {
        return Article::getById($id);
    }

    public function getSiteStartArticle(): Article
    {
        return Article::getByObject(\rex_article::getSiteStartArticle());
    }

    public function getSelectedArticles(array $ids): array
    {
        $articles =  array_map(fn ($id) => Article::getById($id), $ids);
        return array_filter($articles, fn ($article) => $article !== null);
    }
}
