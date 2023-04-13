<?php

namespace GraphQL\Service\Structure;

use GraphQL\Type\Structure\Clang;

class ClangService
{

    /**
     * Get all available languages
     *
     * @return Clang[]
     */
    public function getClangs(int $article): array
    {
        $article = \rex_article::get($article);
        $clangs = \rex_clang::getAll(1);
        $clangs = array_filter($clangs, function ($clang) use ($article) {
            $article = \rex_article::get($article->getId(), $clang->getId());
            return $article && $article->isOnline();
        });
        return array_map(function ($clang) use ($article) {
            $lang = Clang::getByObject($clang);
            $lang->isActive = $clang->getId() === $article->getClangId();
            $lang->url = rex_getUrl($article->getId(), $clang->getId());
            return $lang;
        }, $clangs);
    }

}
