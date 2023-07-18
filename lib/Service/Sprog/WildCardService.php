<?php

namespace GraphQL\Service\Sprog;

use RexGraphQL\Type\Sprog\WildCard;

class WildCardService
{

    public function getAllWildCards(): array
    {
        $data = \rex_sql::factory()->getArray(
            'SELECT * FROM '.\rex::getTablePrefix().'sprog_wildcard WHERE clang_id = :clangId',
            [
                'clangId' => \rex_clang::getCurrentId(),
            ]
        );

        return array_map(function ($item) {
            return new WildCard($item['id'], $item['wildcard'], $item['replace'], \rex_clang::getCurrentId());
        }, $data);
    }

    public function getWildCard(string $key): ?WildCard
    {
        $data = \rex_sql::factory()->getArray(
            'SELECT * FROM '.\rex::getTablePrefix().'sprog_wildcard WHERE clang_id = :clangId AND wildcard = :wildcard',
            [
                'clangId' => \rex_clang::getCurrentId(),
                'wildcard' => $key,

            ]
        );
        $item = $data[0] ?? null;
        if(!$item) {
            return null;
        }
        return new WildCard($item['id'], $item['wildcard'], $item['replace'], \rex_clang::getCurrentId());
    }

}
