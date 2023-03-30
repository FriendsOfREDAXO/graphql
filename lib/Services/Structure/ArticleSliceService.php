<?php

namespace Headless\Services\Structure;

use Headless\Model\Structure\ArticleSlice;


class ArticleSliceService
{
    /**
     * Get slice by id
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
     * @return ArticleSlice[]
     */
    public function getSlicesByArticleId(int $articleId): array
    {
        $slices = \rex_article_slice::getSlicesForArticle($articleId);
        return array_map(function($slice) {
            return ArticleSlice::getByObject($slice);
        }, $slices);
    }

}
