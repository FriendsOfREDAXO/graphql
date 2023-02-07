<?php

namespace Headless\GraphQL\Controller;
use Headless\Model\Article\Article;
use lib\Services\Article\ArticlesService;
use TheCodingMachine\GraphQLite\Annotations\Query;
use TheCodingMachine\GraphQLite\Types\ID;

class ArticlesController
{

    private ArticlesService $service;

    public function __construct()
    {
        $this->service = new ArticlesService();
    }

    /**
     * @Query("Get all articles in the root category")
     * @return Article[]
     */
    public function getRootArticles(): array
    {
        return $this->service->getRootArticles();
    }

    /**
     * @Query("Get an article by its id")
     * @param ID $id id of the article
     * @return Article
     */
    public function getArticle(ID $id): Article
    {
        return $this->service->getArticleById($id->val());
    }

    /**
     * @Query("Get an article by its path")
     * @param string $path path of the article
     * @return Article
     */
    public function getArticleByPath(string $path): Article
    {
        if (substr($path, 0, 1) !== '/')
            $path = '/' . $path;
        if (substr($path, -1) !== '/')
            $path = $path . '/';
        return $this->service->getArticleByPath($path);
    }

}
