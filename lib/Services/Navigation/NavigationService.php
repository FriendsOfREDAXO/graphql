<?php

namespace Kreatif\Services\Navigation;

use Headless\Model\Navigation\NavigationItem;


class NavigationService
{

    /**
     * @return NavigationItem[]
     */
    public function getRootNavigation(int $depth, int $articleId): array
    {
        $navigation = [];

        $siteStartArticle = \rex_article::getSiteStartArticle();
        $navigation[]     = NavigationItem::getByArticle($siteStartArticle, $articleId);
        $rootCategories   = \rex_category::getRootCategories(true);
        foreach ($rootCategories as $rootCategory) {
            $navigation[] = NavigationItem::getByCategory($rootCategory, $articleId);
            if ($depth > 1) {
                $navigation = array_merge($navigation, $this->getChildren($rootCategory, $depth - 1, $articleId));
            }
        }
        return $navigation;
    }

    /**
     * @return NavigationItem[]
     */
    private function getChildren(\rex_category $category, int $depth, int $articleId): array
    {
        $children = [];
        if ($category) {
            $categories = $category->getChildren();
            foreach ($categories as $_category) {
                $children[] = NavigationItem::getByCategory($_category, $articleId);
                if ($depth > 1) {
                    $children = $this->getChildren($_category, $depth - 1, $articleId);
                }
            }
            $articles = $category->getArticles(true);
            foreach ($articles as $article) {
                $children[] = NavigationItem::getByArticle($article, $articleId);
            }
        }
        return $children;
    }

    public function getNavigationByName(string $name, int $articleId): array
    {
        $navigation = [];
        $navItems   = array_filter(\rex_navbuilder::getStructure($name));

        if ($navItems) {
            foreach ($navItems as $navItem) {
                $navigation = array_merge($navigation, $this->parseNavbuilderItems($navItem, null, $articleId));
            }
        }
        return $navigation;
    }

    public function parseNavbuilderItems(array $navItem, ?int $parentId, int $articleId): array
    {
        $navigation   = [];
        $parentItem   = NavigationItem::getByNavbuilderItem($navItem, $parentId, $articleId);
        $navigation[] = $parentItem;

        if (isset($navItem['children'])) {
            foreach ($navItem['children'] as $child) {
                $navigation = array_merge($navigation, $this->parseNavbuilderItems($child, $parentItem->getId()->val(), $articleId));
            }
        }
        return $navigation;
    }
}
