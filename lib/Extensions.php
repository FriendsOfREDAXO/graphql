<?php

namespace GraphQL;

use RexGraphQL\Connector\Connector;

class Extensions
{
    public static function init()
    {
        if (\rex::isBackend()) {
            \rex_extension::register('OUTPUT_FILTER', [self::class, 'ext__interceptBackendArticleLink']);
        }
        if (\rex::isFrontend()) {
            \rex_extension::register('PACKAGES_INCLUDED', [self::class, 'ext__initGraphQLEndpoint'], \rex_extension::LATE);
            \rex_extension::register('GRAPHQL_SLICE_VALUES', [self::class, 'ext__replaceInterLinks']);
            \rex_extension::register('MEDIA_MANAGER_URL', [self::class, 'ext__rewriteMediaUrl'], \rex_extension::LATE);
            \rex_extension::register('MEDIA_URL_REWRITE', [self::class, 'ext__rewriteMediaUrl'], \rex_extension::LATE);
            \rex_extension::register('YREWRITE_CANONICAL_URL', [self::class, 'ext__rewriteArticleUrl'], \rex_extension::LATE);
            \rex_extension::register('URL_REWRITE', [self::class, 'ext__rewriteArticleUrl'], \rex_extension::LATE);
            Connector::init();
        }
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

    public static function ext__addTypeNamespaces(\rex_extension_point $ep) {
        $subject = $ep->getSubject();
        if(\rex_addon::exists('sprog') && \rex_addon::get('sprog')->isAvailable()) {
            $subject[] = '\\RexGraphQL\\Sprog\\Type';
        }
        return $subject;
    }

    public static function ext__addControllerNamespaces(\rex_extension_point $ep) {
        $subject = $ep->getSubject();
        if(\rex_addon::exists('sprog') && \rex_addon::get('sprog')->isAvailable()) {
            $subject[] = '\\RexGraphQL\\Sprog\\Controller';
        }
        return $subject;
    }

    public static function ext__rewriteMediaUrl(\rex_extension_point $ep)
    {
        $subject = $ep->getSubject();
        if (\rex_yrewrite::getCurrentDomain()) {
            $basePath = \rex_yrewrite::getCurrentDomain()->getPath();
            $baseUrl = \rex_yrewrite::getCurrentDomain()->getUrl();
            if ($subject) {
                return $baseUrl . preg_replace('@^' . preg_quote($basePath, '@') . '@', '', $subject);
            }
            $media = $ep->getParam('media');
            if ($media) {
                return $baseUrl . 'media/' . $media->getFilename();
            }
        }
        return $subject;
    }

    public static function ext__rewriteArticleUrl(\rex_extension_point $ep)
    {
        $subject = $ep->getSubject();
        $subject = preg_replace('@^http(s)?://@', '', $subject);
        if (\rex_yrewrite::getCurrentDomain()) {
            $baseUrl = \rex_yrewrite::getCurrentDomain()->getUrl();
            $basePath = \rex_yrewrite::getCurrentDomain()->getPath();
            $subject = preg_replace('@^' . preg_quote($baseUrl, '@') . '@', '', $subject);
            return '/' . preg_replace('@^' . preg_quote($basePath, '@') . '@', '', $subject);
        }
        return $subject;
    }

    public static function ext__replaceInterLinks(\rex_extension_point $ep)
    {
        $content = $ep->getSubject();
        return preg_replace_callback(
            '@redaxo:\\\/\\\/(\d+)(?:-(\d+))?/?@i',
            function (array $matches) {
                return rex_getUrl((int)$matches[1], (int)($matches[2] ?? \rex_clang::getCurrentId()));
            },
            $content,
        );
    }
}
