<?php

namespace GraphQL\Service\Structure;

use GraphQL\Type\Structure\ArticleSlice;

class ArticleSliceService
{
    /**
     * Get slice by id
     *
     * @param int $id
     *
     * @return ArticleSlice
     */
    public function getSliceById(int $id): ArticleSlice
    {
        return ArticleSlice::getById($id);
    }

    /**
     * Get all slices of an article
     *
     * @param int $articleId
     *
     * @return array
     */
    public function getSlicesByArticleId(int $articleId): array
    {
        $slices = \rex_article_slice::getSlicesForArticle($articleId);
        return array_map(function ($slice) {
            return ArticleSlice::getByObject($slice);
        }, $slices);
    }

}
