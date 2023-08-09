<?php

namespace RexGraphQL\Connector\Sprog\Service;


use RexGraphQL\Connector\Sprog\Type\WildCard;

class WildCardService
{

    public function getAllWildCards(): array
    {
        $data = \rex_sql::factory()->getArray(
            'SELECT * FROM ' . \rex::getTablePrefix() . 'sprog_wildcard WHERE clang_id = :clangId',
            [
                'clangId' => \rex_clang::getCurrentId(),
            ]
        );

        return array_map(function ($item) {
            return WildCard::getFromArray($item);
        }, $data);
    }

    public function getSelectedWildCards(array $wildcards): array
    {
        $sql = \rex_sql::factory();
        $sql->setTable(\rex::getTablePrefix() . 'sprog_wildcard');
        $in = $sql->in($wildcards);
        $sql->setWhere("clang_id = :clangId AND wildcard IN ($in)", [
            'clangId' => \rex_clang::getCurrentId(),
        ]);
        $sql->select();
        return array_map(function ($item) {
            return WildCard::getFromArray($item);
        }, $sql->getArray());
    }

    public function getWildCard(string $key): ?WildCard
    {
        $data = \rex_sql::factory()->getArray(
            'SELECT * FROM ' . \rex::getTablePrefix() . 'sprog_wildcard WHERE clang_id = :clangId AND wildcard = :wildcard',
            [
                'clangId' => \rex_clang::getCurrentId(),
                'wildcard' => $key,

            ]
        );
        $item = $data[0] ?? null;
        if (!$item) {
            return null;
        }
        return WildCard::getFromArray($item);
    }

}
