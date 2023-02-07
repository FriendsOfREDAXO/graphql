<?php

namespace Headless\GraphQL\Controller;
use Headless\Model\Structure\Article;
use lib\Services\Structure\ArticleService;
use TheCodingMachine\GraphQLite\Annotations\Query;
use TheCodingMachine\GraphQLite\Types\ID;

class ArticleController
{

    private ArticleService $service;

    public function __construct()
    {
        $this->service = new ArticleService();
    }

    /**
     * Get all articles in the root category
     *
     * @Query()
     * @return Article[]
     */
    public function getRootArticles(): array
    {
        return $this->service->getRootArticles();
    }

    /**
     * Get an article by its id
     * @Query()
     * @param ID $id id of the article
     * @return Article
     */
    public function getArticle(ID $id): Article
    {
        return $this->service->getArticleById($id->val());
    }

    /**
     * Get an article by its path
     * @Query()
     * @param string $path path of the article
     * @return Article
     */
    public function getArticleByPath(string $path): Article
    {

        return $this->service->getArticleByPath($path);
    }

    /**
     * Get the start article of the site
     * @Query()
     * @return Article
     */
    public function getSiteStartArticle(): Article
    {
        return $this->service->getSiteStartArticle();
    }

}
