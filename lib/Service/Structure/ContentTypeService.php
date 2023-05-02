<?php

namespace GraphQL\Service\Structure;

use RexGraphQL\Type\Structure\ContentType;
use rex;
use rex_addon;
use rex_clang;
use rex_yrewrite;
use rex_yrewrite_path_resolver;
use TheCodingMachine\GraphQLite\Types\ID;
use Url\Url;
use Url\UrlManager;

class ContentTypeService
{
    public function getContentTypeByPath(string $path): ContentType
    {
        if (!str_starts_with($path, '/')) {
            $path = '/' . $path;
        }
        if (!str_ends_with($path, '/')) {
            $path = $path . '/';
        }
        $structureAddon = rex_addon::get('structure');
        $resolver = new rex_yrewrite_path_resolver(
            rex_yrewrite::getDomains(),
            [],
            [],
            rex_yrewrite::$paths['paths'] ?? [],
            rex_yrewrite::$paths['redirections'] ?? [],
        );
        $resolver->resolve($path);
        $id = $structureAddon->getProperty('article_id');
        if ($id !== rex_yrewrite::getCurrentDomain()->getNotfoundId()) {
            return new ContentType('article', rex_clang::getCurrentId(), new ID($id));
        }
        if (rex_addon::exists('url') && rex_addon::get('url')->isAvailable()) {
            try {
                $urlObject = UrlManager::resolveUrl(new Url($path));
                rex::setProperty('url_object', $urlObject);
                if ($urlObject) {
                    return new ContentType($urlObject->getProfile()->getNamespace(), $urlObject->getClangId(), new ID($urlObject->getArticleId()));
                }
            } catch (\Exception $e) {}
        }
        return new ContentType('article', rex_clang::getCurrentId(), new ID(rex_yrewrite::getCurrentDomain()->getNotfoundId()));
    }
}
