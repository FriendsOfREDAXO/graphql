<?php

namespace GraphQL\Service\Navigation;

use RexGraphQL\Type\Navigation\NavigationItem;
use TheCodingMachine\GraphQLite\Exceptions\GraphQLException;

class NavigationService
{

    /**
     * @return NavigationItem[]
     */
    public function getRootNavigation(int $depth, bool $includeHome, ?int $articleId): array
    {
        $navigation = [];
        $articleId = $articleId ?? \rex_article::getCurrentId();
        if($includeHome) {
            $siteStartArticle = \rex_article::getSiteStartArticle();
            $startNavItem = NavigationItem::getByArticle($siteStartArticle, $articleId);
            if ($startNavItem) {
                $navigation[] = $startNavItem;
            }
        }
        $rootCategories = \rex_category::getRootCategories(true);
        foreach ($rootCategories as $rootCategory) {
            $navItem = NavigationItem::getByCategory($rootCategory, $articleId);
            if ($navItem) {
                $navigation[] = $navItem;
                if ($depth > 1) {
                    $navigation = array_merge($navigation, $this->getChildren($rootCategory, $depth - 1, $articleId));
                }
            }
        }
        return \rex_extension::registerPoint(
            new \rex_extension_point('HEADLESS_ROOT_NAVIGATION', $navigation, [
                'depth' => $depth,
            ])
        );
    }

    /**
     * @return NavigationItem[]
     */
    private function getChildren(?\rex_category $category, int $depth, int $articleId): array
    {
        $children = [];
        if ($category) {
            $categories = $category->getChildren();
            foreach ($categories as $_category) {
                $navItem = NavigationItem::getByCategory($_category, $articleId);
                if ($navItem) {
                    $children[] = $navItem;
                    if ($depth > 1) {
                        $children = array_merge($children, $this->getChildren($_category, $depth - 1, $articleId));
                    }
                }
            }
            $articles = $category->getArticles(true);
            foreach ($articles as $article) {
                $navItem = NavigationItem::getByArticle($article, $articleId);
                if ($navItem) {
                    $children[] = $navItem;
                }
            }
        }
        return $children;
    }
}
