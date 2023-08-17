<?php

namespace RexGraphQL\Controller;

use GraphQL\Service\Structure\ContentTypeService;
use RexGraphQL\Type\Structure\ContentType;
use RexGraphQL\Type\Structure\Forward;
use TheCodingMachine\GraphQLite\Annotations\Logged;
use TheCodingMachine\GraphQLite\Annotations\Query;
use TheCodingMachine\GraphQLite\Types\ID;

class ContentTypeController
{
    private ContentTypeService $service;

    public function __construct()
    {
        $this->service = new ContentTypeService();
    }

    #[Query]
    #[Logged]
    public function getContentTypeByPath(string $path): ContentType
    {
        return $this->service->getContentTypeByPath($path);
    }

    #[Query]
    #[Logged]
    public function getForward(ID $id): ?Forward
    {
        return $this->service->getForward($id->val());
    }

    #[Query]
    #[Logged]
    public function getArticleRedirect(ID $id): ?Forward
    {
        return $this->service->getArticleRedirect($id->val());
    }
}
