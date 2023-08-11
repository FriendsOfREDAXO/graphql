<?php

namespace GraphQL\Service\Structure;

use RexGraphQL\Type\Structure\ContentType;
use rex;
use rex_addon;
use rex_clang;
use rex_yrewrite;
use rex_yrewrite_path_resolver;
use RexGraphQL\Type\Structure\Forward;
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

        $forwardType = $this->checkForForward($path);
        if($forwardType) {
            return $forwardType;
        }

        $articleType = $this->checkForArticle($path);
        if($articleType) {
            return $articleType;
        }

        $urlType = $this->checkForUrlObject($path);
        if($urlType) {
            return $urlType;
        }

        return $this->get404();
    }

    private function checkForArticle(string $path): ?ContentType
    {
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
        return null;
    }

    private function checkForUrlObject(string $path): ?ContentType
    {
        if (rex_addon::exists('url') && rex_addon::get('url')->isAvailable()) {
            try {
                $basePath = \rex_yrewrite::getCurrentDomain()->getPath();
                $resolvablePath = rtrim($basePath, '/') . '/' . ltrim($path, '/');
                $urlObject = UrlManager::resolveUrl(new Url($resolvablePath));
                rex::setProperty('url_object', $urlObject);
                if ($urlObject) {
                    return new ContentType($urlObject->getProfile()->getNamespace(), $urlObject->getClangId(), new ID($urlObject->getDatasetId()));
                }
            } catch (\Exception $e) {}
        }
        return null;
    }

    private function checkForForward(string $path): ?ContentType
    {
        $paths = \rex_yrewrite_forward::$paths;
        $path = trim($path, '/');
        foreach($paths as $_path) {
            if(trim($_path['url'], '/') == $path) {
                return new ContentType('forward', rex_clang::getCurrentId(), new ID($_path['id']));
            }
        }
        return null;
    }

    private function get404(): ContentType
    {
        return  new ContentType('article', rex_clang::getCurrentId(), new ID(rex_yrewrite::getCurrentDomain()->getNotfoundId()));
    }

    public function getForward(int $id): ?Forward
    {
        $paths = \rex_yrewrite_forward::$paths;
        foreach($paths as $_path) {
            if($_path['id'] == $id) {
                $forward = Forward::getForwardFromArray($_path);
                if($forward) {
                    return $forward;
                }
            }
        }
        return null;
    }
}
