<?php

use Headless\GraphQL\GraphQL;

rex_extension::register('PACKAGES_INCLUDED', function (\rex_extension_point $ep) {

    if (rex_request('headless-graphql', 'string', null) !== null) {
        $clangId = rex_request('clang-id', 'int', null);
        if($clangId) {
            rex_clang::setCurrentId($clangId);
        }
        GraphQL::registerEndpoint();
    }
}, rex_extension::LATE);

rex_extension::register('HEADLESS_GRAPHQL_CONTROLLERS', function (\rex_extension_point $ep) {
    $controllers = $ep->getSubject();
    $controllers[] = \Headless\GraphQL\Controller\ArticleController::class;
    $controllers[] = \Headless\GraphQL\Controller\CategoryController::class;
    $controllers[] = \Headless\GraphQL\Controller\ClangController::class;
    $controllers[] = \Headless\GraphQL\Controller\ArticleSliceController::class;
    $controllers[] = \Headless\GraphQL\Controller\MediaController::class;
    $controllers[] = \Headless\GraphQL\Controller\SprogController::class;
    $controllers[] = \Headless\GraphQL\Controller\NavigationController::class;
    return $controllers;
});
