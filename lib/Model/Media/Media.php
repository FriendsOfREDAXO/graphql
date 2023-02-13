<?php

namespace Headless\Model\Media;

use Kreatif\MediaManager;
use TheCodingMachine\GraphQLite\Types\ID;
use TheCodingMachine\GraphQLite\Annotations\Field;
use TheCodingMachine\GraphQLite\Annotations\Type;


/**
 * @Type()
 */
class Media
{
    public \rex_media $media;
    public string     $imageType;
    private array     $dimensions = [];

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
        if ($focusPoint) {
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
        if (\rex_clang::getCurrentId() !== 1) {
            $title = $this->media->getValue('med_title_' . \rex_clang::getCurrentId());
        }
        if (!$title) {
            $title = $this->media->getTitle() ?: $this->getFilename();
        }
        return $title;
    }

    /**
     * @Field()
     */
    public function getSrcset(): ?string
    {
        if (!\rex_addon::exists('media_srcset') || !\rex_addon::get('media_srcset')->isAvailable()) {
            return null;
        }

        $srcset = \rex_media_srcset::getSrcSet($this->media->getFilename(), $this->imageType);
        if(!$srcset) {
            return null;
        }
        return $this->rewriteUrl($srcset);
    }

    private function rewriteUrl(?string $url): string {
        return str_replace('./', \rex_yrewrite::getCurrentDomain()->getUrl(), $url);
    }

    private function getBaseUrl(): string {
        return \rex_yrewrite::getCurrentDomain()->getUrl();
    }

    /**
     * @Field()
     */
    public function getAlt(): ?string
    {
        $alt = null;
        if (\rex_clang::getCurrentId() !== 1) {
            $alt = $this->media->getValue('med_alt_' . \rex_clang::getCurrentId());
        }
        if (!$alt) {
            $alt = $this->media->getValue('med_alt') ?: $this->getFilename();
        }
        return $alt;
    }

    /**
     * @Field()
     */
    public function getSrc(): string
    {
        $mediaType = urlencode($this->imageType);
        $baseUrl   = $this->getBaseUrl();
        $name      = urlencode($this->media->getFilename());

        return "{$baseUrl}media/$mediaType/$name";
    }

    /**
     * @Field()
     */
    public function getWidth(): int
    {
        return $this->getDimensions()['width'];
    }

    /**
     * @Field()
     */
    public function getHeight(): int
    {
        return $this->getDimensions()['height'];
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
     *
     * @return Media proxy object
     */
    public static function getByName(string $name, string $imageType): Media
    {
        $a            = new Media();
        $a->media     = \rex_media::get($name);
        $a->imageType = $imageType;
        return $a;
    }


    /**
     * @param \rex_media $obj \rex_article object to encapsulate
     *
     * @return Media proxy object
     */
    public static function getByObject(\rex_media $obj, string $imageType): Media
    {
        $a            = new Media();
        $a->media     = $obj;
        $a->imageType = $imageType;
        return $a;
    }


    private function getDimensions(): array
    {
        if ($this->dimensions) {
            return $this->dimensions;
        }
        $imgSize  = [];
        $filePath = \rex_path::media($this->media->getFileName());
        if ($this->getExtension() == 'svg') {
            $content = file_get_contents($filePath);
            if (preg_match('!\bviewBox="\b[\d\.]+\s[\d\.]+\s([\d\.]+)\s([\d\.]+)"!', $content, $matches)) {
                $imgSize = [$matches[1], $matches[2]];
            } else if (preg_match('!\bwidth="\b([\d\.]+)"!', $content, $widthMatches)) {
                if (preg_match('!\bheight="\b([\d\.]+)"!', $content, $heightMatches)) {
                    $imgSize = [$widthMatches[1], $heightMatches[1]];
                }
            }
        } else if ($this->imageType) {
            $mediaManager = \rex_media_manager::create($this->imageType, $this->media->getFileName());

            if (version_compare(\rex_addon::get('media_manager')->getVersion(), '2.11.0', '>=')) {
                $cachePath = \rex_path::addonCache('media_manager');
            } else {
                $cachePath = \rex_path::addonCache('media_manager', $this->imageType);
            }
            $mediaManager->setCachePath($cachePath);

            if ($mediaManager->isCached()) {
                $imgSize = @getimagesize($mediaManager->getCacheFilename());
            } else {
                $imgSize = @getimagesize($filePath);
            }
        }
        $this->dimensions = ['width' => round($imgSize[0]), 'height' => round($imgSize[1]),];
        return $this->dimensions;
    }
}
