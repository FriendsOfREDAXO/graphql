<?php

namespace GraphQL;

class Extensions
{
    public static function init()
    {
        \rex_extension::register('PACKAGES_INCLUDED', [self::class, 'ext__initGraphQLEndpoint'], \rex_extension::LATE);
        if (\rex::isBackend()) {
            \rex_extension::register('OUTPUT_FILTER', [self::class, 'ext__interceptBackendArticleLink']);
        }
        \rex_extension::register('MEDIA_MANAGER_URL', [self::class, 'ext__rewriteMediaUrl'], \rex_extension::LATE);
        \rex_extension::register('MEDIA_URL_REWRITE', [self::class, 'ext__rewriteMediaUrl'], \rex_extension::LATE);
        \rex_extension::register('URL_REWRITE', [self::class, 'ext__rewriteArticleUrl'], \rex_extension::LATE);
    }

    public static function ext__interceptBackendArticleLink(\rex_extension_point $ep)
    {
        $content = $ep->getSubject();
        if (!\rex::isBackend()) {
            return $content;
        }

        $frontendUrl = \rex::getServer();
        if ($frontendUrl) {
            $articleId = $ep->getParam('article_id');
            $clang = $ep->getParam('clang');
            $newUrl = rtrim($frontendUrl, '/') . '/' . ltrim(rex_getUrl($articleId, $clang), '/');
            $pattern = '/<li class="pull-right">.*?<a href=".*?" onclick=".*?">.+?<\/a><\/li>/';
            $content = preg_replace_callback($pattern, function ($matches) use ($newUrl) {
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

    public static function ext__rewriteMediaUrl(\rex_extension_point $ep)
    {
        $subject = $ep->getSubject();
        if (\rex_yrewrite::getCurrentDomain()) {
            $basePath = \rex_yrewrite::getCurrentDomain()->getPath();
            $matches = [];
            preg_match('_^(' . $basePath . ')(.*)_', $subject, $matches);
            $path = ltrim($matches[2], '/');
            $baseUrl = rtrim(\rex_yrewrite::getCurrentDomain()->getUrl(), '/');
            return $baseUrl . '/' . $path;
        }
        return $subject;
    }

    public static function ext__rewriteArticleUrl(\rex_extension_point $ep)
    {
        $subject = $ep->getSubject();
        if (\rex_yrewrite::getCurrentDomain()) {
            $baseUrl = \rex_yrewrite::getCurrentDomain()->getPath();
            $matches = [];
            preg_match('_^(' . $baseUrl . ')(.*)_', $subject, $matches);
            return '/' . $matches[2];
        }
        return $subject;
    }
}
