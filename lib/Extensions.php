<?php

namespace GraphQL;

class Extensions
{
    public static function init()
    {
        \rex_extension::register('PACKAGES_INCLUDED', [self::class, 'ext__initGraphQLEndpoint'], \rex_extension::LATE);
        \rex_extension::register('STRUCTURE_CONTENT_NAV_RIGHT', [self::class, 'ext__interceptBackendArticleLink']);
    }

    public static function ext__interceptBackendArticleLink(\rex_extension_point $ep)
    {
        $content     = $ep->getSubject();
        $frontendUrl = trim(\rex_addon::get('headless')->getConfig('frontend_base_url'), '/');
        if($frontendUrl) {
            $articleId   = $ep->getParam('article_id');
            $clang       = $ep->getParam('clang');
            $newUrl      = $frontendUrl . '/' . ltrim(rex_getUrl($articleId, $clang), '/');
            $content[1]  = [
                'title' => '<a href="' . $newUrl . '" onclick="window.open(this.href); return false;">' . \rex_i18n::msg('article') . ' ' . \rex_i18n::msg('show') . ' <i class="rex-icon rex-icon-external-link"></i></a>',
            ];
        }
        return $content;
    }

    public static function ext__initGraphQLEndpoint()
    {
        if (rex_request('headless-graphql', 'string', null) !== null) {
            $clangId = rex_request('clang-id', 'int', null);
            if ($clangId) {
                \rex_clang::setCurrentId($clangId);
            }
            Endpoint::registerEndpoint();
        }
    }
}
