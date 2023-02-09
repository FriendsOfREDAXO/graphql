<?php

namespace Headless\GraphQL\Controller;

use Headless\Model\Structure\ArticleSlice;
use Headless\Services\Structure\ArticleSliceService;
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
     * @Query()
     * @return ArticleSlice[]
     */
    public function getArticleSlices(ID $articleId): array
    {
        return $this->service->getSlicesByArticleId($articleId->val());
    }

    /**
     * Get article slice by id
     *
     * @Query()
     */
    public function getArticleSlice(ID $articleSliceId): ArticleSlice
    {
        return $this->service->getSliceById($articleSliceId->val());
    }



}
