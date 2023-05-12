<?php

namespace RexGraphQL\Controller;

use RexGraphQL\Type\Structure\ArticleSlice;
use GraphQL\Service\Structure\ArticleSliceService;
use TheCodingMachine\GraphQLite\Annotations\Logged;
use TheCodingMachine\GraphQLite\Annotations\Query;
use TheCodingMachine\GraphQLite\Types\ID;

class ArticleSliceController
{
    private ArticleSliceService $service;

    public function __construct()
    {
        $this->service = new ArticleSliceService();
    }

    /**
     * Get article slices by article id
     *
     * @return ArticleSlice[]
     */
    #[Query]
    #[Logged]
    public function getArticleSlices(ID $articleId): array
    {
        return $this->service->getSlicesByArticleId($articleId->val());
    }

    /**
     * Get article slice by id
     *
     */
    #[Query]
    #[Logged]
    public function getArticleSlice(ID $id): ArticleSlice
    {
        return $this->service->getSliceById($id->val());
    }

}
