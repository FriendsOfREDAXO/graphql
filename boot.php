<?php

use Headless\GraphQL\GraphQL;

rex_extension::register('PACKAGES_INCLUDED', function (\rex_extension_point $ep) {
    if (rex_request('headless-graphql', 'string', null) !== null) {
        GraphQL::registerEndpoint();
    }
});

rex_extension::register('HEADLESS_GRAPHQL_CONTROLLERS', function (\rex_extension_point $ep) {
    $controllers = $ep->getSubject();
    $controllers[] = \Headless\GraphQL\Controller\ArticleController::class;
    $controllers[] = \Headless\GraphQL\Controller\CategoryController::class;
    $controllers[] = \Headless\GraphQL\Controller\ClangController::class;
    $controllers[] = \Headless\GraphQL\Controller\ArticleSliceController::class;
    return $controllers;
});
