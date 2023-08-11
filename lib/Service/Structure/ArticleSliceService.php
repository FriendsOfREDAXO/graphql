<?php

namespace GraphQL\Service\Structure;

use RexGraphQL\Type\Structure\ArticleSlice;

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
}
