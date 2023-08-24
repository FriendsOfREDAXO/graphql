<?php

namespace RexGraphQL;

use GraphQL\Service\Auth\AuthService;
use GraphQL\Service\Auth\JwtService;
use RexGraphQL\Connector\Connector;
use RexGraphQL\RexGraphQL;

class Extensions
{
    public static function init(): void
    {
        if (RexGraphQL::isHeadlessMode()) {
            \rex_extension::register('YREWRITE_CANONICAL_URL', [self::class, 'ext__rewriteArticleUrl'], \rex_extension::LATE);
            \rex_extension::register('URL_REWRITE', [self::class, 'ext__rewriteArticleUrl'], \rex_extension::LATE);
        }
        if (\rex::isBackend()) {
            if (RexGraphQL::isHeadlessMode()) {
                \rex_extension::register('OUTPUT_FILTER', [self::class, 'ext__interceptBackendArticleLink'], \rex_extension::LATE);
                \rex_extension::register('PACKAGES_INCLUDED', [self::class, 'ext__interceptBackendFrontendLink'], \rex_extension::LATE);
                \rex_extension::register('SLICE_SHOW', [self::class, 'ext__createModulePreview'], \rex_extension::EARLY);
            }
        }
        if (\rex::isFrontend()) {
            \rex_extension::register('PACKAGES_INCLUDED', [self::class, 'ext__initGraphQLEndpoint'], \rex_extension::LATE);
            Connector::init();

            if (RexGraphQL::isHeadlessMode()) {
                \rex_extension::register('GRAPHQL_SLICE_VALUES', [self::class, 'ext__replaceInterLinks']);
                \rex_extension::register('MEDIA_MANAGER_URL', [self::class, 'ext__rewriteMediaUrl'], \rex_extension::LATE);
                \rex_extension::register('MEDIA_URL_REWRITE', [self::class, 'ext__rewriteMediaUrl'], \rex_extension::LATE);
            }
        }
    }

    public static function ext__interceptBackendArticleLink(\rex_extension_point $ep)
    {
        $content = $ep->getSubject();

        $frontendUrl = \rex::getServer();
        if ($frontendUrl) {

            $jwtService = new JwtService();
            $token = $jwtService->generateToken();

            $articleId = $ep->getParam('article_id');
            $clang = $ep->getParam('clang');
            $newUrl = rtrim($frontendUrl, '/') . '/' . ltrim(rex_getUrl($articleId, $clang), '/');
            if ($token) {
                $newUrl .= '?' . AuthService::AUTH_GET_PARAM_KEY . '=' . $token;
            }
            $pattern = '/<li class="pull-right">.*?<a href=".*?" onclick=".*?">.+?<\/a><\/li>/';
            $content = preg_replace_callback($pattern, function ($matches) use ($newUrl) {
                $match = $matches[0];
                $match = preg_replace('/href=".*?"/', "href=\"$newUrl\"", $match);
                return $match;
            }, $content);
        }
        return $content;
    }

    /**
     * @throws \rex_exception
     */
    public static function ext__initGraphQLEndpoint(): void
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
        $basePath = \rex_yrewrite::getCurrentDomain()?->getPath();

        if (preg_match('@^http(s)?://@', $subject) && !($basePath && preg_match('@^https(s)?://' . preg_quote($basePath, '@') . '@', $subject))) {
            return $subject;
        }

        if ($basePath) {
            $subject = preg_replace('@^http(s)?://@', '', $subject);
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

    public static function ext__createModulePreview(\rex_extension_point $ep): string
    {
        $sliceId = $ep->getParam('slice_id');
        $clangId = $ep->getParam('clang');
        $fragment = new \rex_fragment();
        $fragment->setVar('slice_id', $sliceId);
        $fragment->setVar('clang_id', $clangId);
        $preview = $fragment->parse('graphql/headless_module_preview.php');
        return preg_replace('@</header>\s*</div>@', '</header>' . $preview . '</div>', $ep->getSubject());
    }

    public static function ext__interceptBackendFrontendLink(): void
    {
        $jwtService = new JwtService();
        $token = $jwtService->generateToken();

        $url = \rex::getServer();
        if ($token) {
            $url .= '?' . AuthService::AUTH_GET_PARAM_KEY . '=' . $token;
        }
        \rex_view::setJsProperty(
            'customizer_showlink',
            '<h1 class="be-style-customizer-title"><a href="' . $url . '" target="_blank" rel="noreferrer noopener"><span class="be-style-customizer-title-name">' . rex_escape(\rex::getServerName()) . '</span><i class="fa fa-external-link"></i></a></h1>',
        );
    }
}
