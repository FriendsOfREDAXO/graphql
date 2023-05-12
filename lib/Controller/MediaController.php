<?php

namespace RexGraphQL\Controller;

use RexGraphQL\Type\Media\Media;
use GraphQL\Service\Media\MediaService;
use TheCodingMachine\GraphQLite\Annotations\Logged;
use TheCodingMachine\GraphQLite\Annotations\Query;

class MediaController
{

    private MediaService $service;

    public function __construct()
    {
        $this->service = new MediaService();
    }

    /**
     * Get a media by name and image type
     */
    #[Query]
    #[Logged]
    public function getMedia(string $name, string $mediaType): Media
    {
        return $this->service->getMediaByName($name, $mediaType);
    }

}
