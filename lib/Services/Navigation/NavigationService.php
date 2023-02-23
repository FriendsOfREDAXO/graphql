<?php

namespace Kreatif\Services\Navigation;

use Headless\Model\Navigation\NavigationItem;


class NavigationService
{

    /**
     * @return NavigationItem[]
     */
    public function getRootNavigation(int $depth): array
    {
        $navigation = [];

        $siteStartArticle = \rex_article::getSiteStartArticle();
        $navigation[]     = NavigationItem::getByArticle($siteStartArticle);
        $rootCategories   = \rex_category::getRootCategories(true);
        foreach ($rootCategories as $rootCategory) {
            $navigation[] = NavigationItem::getByCategory($rootCategory);
            if ($depth > 1) {
                $navigation = array_merge($navigation, $this->getChildren($rootCategory, $depth - 1));
            }
        }
        return $navigation;
    }

    /**
     * @return NavigationItem[]
     */
    private function getChildren(\rex_category $category = null, int $depth = null): array
    {
        $children   = [];
        if($category) {
            $categories = $category->getChildren();
            foreach ($categories as $_category) {
                $children[] = NavigationItem::getByCategory($_category);
                if ($depth > 1) {
                    $children = $this->getChildren($_category, $depth - 1);
                }
            }
            $articles = $category->getArticles(true);
            foreach ($articles as $article) {
                $children[] = NavigationItem::getByArticle($article);
            }
        }
        return $children;
    }
}
