<?php

namespace Headless\Services\Structure;

use Headless\Model\Structure\Clang;


class ClangService
{

    /**
     * Get all available languages
     *
     * @return Clang[]
     */
    public function getClangs(): array
    {
        $clangs = \rex_clang::getAll(1);
        return array_map(function($clang) {
            return Clang::getByObject($clang);
        }, $clangs);
    }

}
