<?php

namespace GraphQL\Service\Structure;

use GraphQL\Type\Structure\Clang;
use TheCodingMachine\GraphQLite\Exceptions\GraphQLException;

class ClangService
{

    /**
     * Get all available languages
     *
     * @return Clang[]
     * @throws GraphQLException
     */
    public function getClangs(?int $articleId): array
    {
        $articleId = $articleId ?? \rex_article::getCurrentId();
        if (!$articleId) {
            throw new GraphQLException('Could not determine current article');
        }
        $clangs = \rex_clang::getAll(1);
        $clangs = array_filter($clangs, function ($clang) use ($articleId) {
            $article = \rex_article::get($articleId, $clang->getId());
            return $article && $article->isOnline();
        });
        return array_map(function ($clang) use ($articleId) {
            $lang = Clang::getByObject($clang);
            $lang->isActive = $clang->getId() === \rex_clang::getCurrentId();
            $lang->url = rex_getUrl($articleId, $clang->getId());
            return $lang;
        }, $clangs);
    }

}
