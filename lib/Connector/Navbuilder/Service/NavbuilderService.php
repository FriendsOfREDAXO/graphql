<?php

namespace RexGraphQL\Connector\Navbuilder\Service;

use RexGraphQL\Type\Navigation\NavigationItem;
use TheCodingMachine\GraphQLite\Exceptions\GraphQLException;

class NavbuilderService
{
    public function getNavigationByName(string $name, ?int $articleId): array
    {
        $navigation = [];
        $articleId = $articleId ?? \rex_article::getCurrentId();
        if(!$articleId) {
            throw new GraphQLException('Could not determine current article');
        }
        $navItems = array_filter(\rex_navbuilder::getStructure($name));

        if ($navItems) {
            foreach ($navItems as $navItem) {
                $navigation = array_merge($navigation, $this->parseNavbuilderItems($navItem, null, $articleId));
            }
        }
        return $navigation;
    }

    public function parseNavbuilderItems(array $navItem, ?int $parentId, int $articleId): array
    {
        $navigation = [];
        $parentItem = NavigationItem::getByArray($navItem, $parentId, $articleId);
        $navigation[] = $parentItem;

        if (isset($navItem['children'])) {
            foreach ($navItem['children'] as $child) {
                $navigation = array_merge(
                    $navigation,
                    $this->parseNavbuilderItems($child, $parentItem->getId()->val(), $articleId)
                );
            }
        }
        return $navigation;
    }
}
