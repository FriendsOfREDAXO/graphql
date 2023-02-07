<?php

use Headless\GraphQL\GraphQL;

rex_extension::register('PACKAGES_INCLUDED', function (\rex_extension_point $ep) {
    if (rex_request('headless-graphql', 'string', null) !== null) {
        GraphQL::registerEndpoint();
    }
});

rex_extension::register('HEADLESS_GRAPHQL_CONTROLLERS', function (\rex_extension_point $ep) {
    $controllers = $ep->getSubject();
    $controllers[] = \Headless\GraphQL\Controller\ArticlesController::class;
    return $controllers;
});
