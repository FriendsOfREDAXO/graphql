<?php

namespace GraphQL\Controller;

use GraphQL\Service\Structure\ContentTypeService;
use GraphQL\Type\Structure\ContentType;
use TheCodingMachine\GraphQLite\Annotations\Query;

class ContentTypeController
{
    private ContentTypeService $service;

    public function __construct()
    {
        $this->service = new ContentTypeService();
    }

    #[Query]
    public function getContentTypeByPath(string $path): ContentType
    {
        return $this->service->getContentTypeByPath($path);
    }
}
