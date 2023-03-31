<?php

namespace GraphQL\Service\Structure;

use GraphQL\Type\Structure\Article;

class ArticleService
{

    public function getArticleByPath(string $path): Article
    {
        if (substr($path, 0, 1) !== '/') {
            $path = '/'.$path;
        }
        if (substr($path, -1) !== '/') {
            $path = $path.'/';
        }
        $structureAddon = \rex_addon::get('structure');
        $resolver = new \rex_yrewrite_path_resolver(
            \rex_yrewrite::getDomains(),
            [],
            [],
            \rex_yrewrite::$paths['paths'] ?? [],
            \rex_yrewrite::$paths['redirections'] ?? []
        );
        $resolver->resolve($path);
        $id = $structureAddon->getProperty('article_id');
        return Article::getById($id);
    }

    public function getRootArticles(): array
    {
        $articles = \rex_article::getRootArticles();
        return array_map(function ($article) {
            return Article::getByObject($article);
        }, $articles);
    }

    public function getArticleById(int $id): Article
    {
        return Article::getById($id);
    }

    public function getSiteStartArticle(): Article
    {
        return Article::getByObject(\rex_article::getSiteStartArticle());
    }
}
