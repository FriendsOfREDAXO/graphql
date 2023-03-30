<?php

namespace GraphQL\Controller;

use GraphQL\Type\Media\Media;
use GraphQL\Service\Media\MediaService;
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
     *
     * @Query()
     * @return Media
     */
    public function getMedia(string $name, string $imageType): Media
    {
        return $this->service->getMediaByName($name, $imageType);
    }


}
