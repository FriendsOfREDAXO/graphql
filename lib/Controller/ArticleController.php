<?php

namespace RexGraphQL\Controller;

use RexGraphQL\Type\Structure\Article;
use GraphQL\Service\Structure\ArticleService;
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
     * @return Article[]
     */
    #[Query]
    public function getRootArticles(): array
    {
        return $this->service->getRootArticles();
    }

    /**
     * Get an article by its id
     *
     * @param ID $id id of the article
     * @return Article
     */
    #[Query]
    public function getArticle(ID $id): Article
    {
        return $this->service->getArticleById($id->val());
    }

    /**
     * Get an article by its path
     *
     * @param string $path path of the article
     * @return Article
     */
    #[Query]
    public function getArticleByPath(string $path): Article
    {
        return $this->service->getArticleByPath($path);
    }

    /**
     * Get the start article of the site
     *
     * @return Article
     */
    #[Query]
    public function getSiteStartArticle(): Article
    {
        return $this->service->getSiteStartArticle();
    }

}
