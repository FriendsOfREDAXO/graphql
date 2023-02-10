<?php

namespace Headless\Model\Media;

use TheCodingMachine\GraphQLite\Types\ID;
use TheCodingMachine\GraphQLite\Annotations\Field;
use TheCodingMachine\GraphQLite\Annotations\Type;


/**
 * @Type()
 */
class Media
{
    public \rex_media $media;
    public string $imageType;

    /**
     * @Field()
     */
    public function getId(): ID
    {
        return new ID($this->media->getId());
    }

    /**
     * @Field()
     */
    public function getFilename(): string
    {
        return $this->media->getFilename();
    }

    /**
     * @Field()
     * @return float[]
     */
    public function getFocusPoint(): ?array
    {
        $focusPoint = $this->media->getValue('med_focuspoint');
        if($focusPoint) {
            return explode(',', $focusPoint);
        }
        return null;
    }

    /**
     * @Field()
     */
    public function getTitle(): ?string
    {
        $title = null;
        if(\rex_clang::getCurrentId() === 1) {
            $title = $this->media->getValue('med_title_' . \rex_clang::getCurrentId());
        }
        if(!$title) {
            $title =  $this->media->getTitle() ?: $this->getFilename();
        }
        return $title;
    }

    /**
     * @Field()
     */
    public function getSrc(): string
    {
        $host = trim(\rex_article::getSiteStartArticle()->getUrl(), '/');
        $mediaType = urlencode($this->imageType);
        $name = urlencode($this->media->getFilename());
        return "$host/index.php?rex_media_type=$mediaType&rex_media_file=$name";
    }

    /**
     * @Field()
     */
    public function getWith(): int
    {
        return $this->media->getWidth();
    }

    /**
     * @Field()
     */
    public function getExtension(): string
    {
        return $this->media->getExtension();
    }

    /**
     * @param string $name Name of the media
     * @return Media proxy object
     */
    public static function getByName(string $name, string $imageType): Media
    {
        $a = new Media();
        $a->media = \rex_media::get($name);
        $a->imageType = $imageType;
        return $a;
    }


    /**
     * @param \rex_media $obj \rex_article object to encapsulate
     * @return Media proxy object
     */
    public static function getByObject(\rex_media $obj, string $imageType): Media
    {
        $a = new Media();
        $a->media = $obj;
        $a->imageType = $imageType;
        return $a;
    }

}
