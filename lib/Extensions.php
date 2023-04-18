<?php

namespace GraphQL;

class Extensions
{
    public static function init()
    {
        \rex_extension::register('PACKAGES_INCLUDED', [self::class, 'ext__initGraphQLEndpoint'], \rex_extension::LATE);
        if(\rex::isBackend()) {
            \rex_extension::register('OUTPUT_FILTER', [self::class, 'ext__interceptBackendArticleLink']);
        }
    }

    public static function ext__interceptBackendArticleLink(\rex_extension_point $ep)
    {
        $content     = $ep->getSubject();
        if(!\rex::isBackend()) {
            return $content;
        }

        $frontendUrl = \rex::getServer();
        if($frontendUrl) {
            $articleId   = $ep->getParam('article_id');
            $clang       = $ep->getParam('clang');
            $newUrl      = rtrim($frontendUrl, '/') . '/' . ltrim(rex_getUrl($articleId, $clang), '/');
            $pattern = '/<li class="pull-right">.*?<a href=".*?" onclick=".*?">.+?<\/a><\/li>/';
            $content = preg_replace_callback($pattern, function($matches) use ($newUrl) {
                $match = $matches[0];
                $match = preg_replace('/href=".*?"/', "href=\"$newUrl\"", $match);
                return $match;
            }, $content);
        }
        return $content;
    }

    public static function ext__initGraphQLEndpoint()
    {
        if (rex_request('graphql-api', 'string', null) !== null) {
            $clangId = rex_request('clang-id', 'int', null);
            if ($clangId) {
                \rex_clang::setCurrentId($clangId);
            }
            Endpoint::registerEndpoint();
        }
    }
}
