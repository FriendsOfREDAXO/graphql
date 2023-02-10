<?php
namespace Headless\Services\Media;
use Headless\Model\Media\Media;

class MediaService
{
    public function getMediaByName(string $name, string $imageType): Media
    {
        return Media::getByName($name, $imageType);
    }
}

