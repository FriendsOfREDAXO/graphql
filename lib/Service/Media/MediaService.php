<?php

namespace GraphQL\Service\Media;

use RexGraphQL\Type\Media\Media;

class MediaService
{
    public function getMediaByName(string $name, string $mediaType): Media
    {
        return Media::getByName($name, $mediaType);
    }
}

