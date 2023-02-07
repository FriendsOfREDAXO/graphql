<?php

namespace lib\Services\Article;

use Headless\Model\Article\Article;

class ArticlesService
{

    public function getArticleByPath(string $path): Article
    {
        $structureAddon = \rex_addon::get('structure');
        $resolver = new \rex_yrewrite_path_resolver(\rex_yrewrite::getDomains(), [], [], \rex_yrewrite::$paths['paths'] ?? [], \rex_yrewrite::$paths['redirections'] ?? []);
        $resolver->resolve($path);
        $id = $structureAddon->getProperty('article_id');
        return Article::getById($id);
    }

    public function getRootArticles(): array
    {
        $articles = \rex_article::getRootArticles();
        return array_map(function($article) {
            return Article::getByObject($article);
        }, $articles);
    }

    public function getArticleById(int $id): Article
    {
        return Article::getById($id);
    }
}
