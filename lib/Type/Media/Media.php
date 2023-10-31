<?php

namespace RexGraphQL\Type\Media;

use TheCodingMachine\GraphQLite\Types\ID;
use TheCodingMachine\GraphQLite\Annotations\Field;
use TheCodingMachine\GraphQLite\Annotations\Type;

#[Type]
class Media
{
    public \rex_media $media;
    public string $mediaType;
    private array $dimensions = [];

    #[Field]
    public function getId(): ID
    {
        return new ID($this->media->getId());
    }

    #[Field]
    public function getFilename(): string
    {
        return $this->media->getFilename();
    }

    /**
     * @return float[]|null
     */
    #[Field]
    public function getFocusPoint(): ?array
    {
        $focusPoint = $this->media->getValue('med_focuspoint');
        if ($focusPoint) {
            return explode(',', $focusPoint);
        }
        return null;
    }

    #[Field]
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

    #[Field]
    public function getSrcset(): ?string
    {
        if (!\rex_addon::exists('media_srcset') || !\rex_addon::get('media_srcset')->isAvailable()) {
            return null;
        }

        if ($this->isSVG()) {
            return null;
        }

        $srcset = \rex_media_srcset::getSrcSet($this->media->getFilename(), $this->mediaType);
        if (!$srcset) {
            return null;
        }
        return $srcset;
    }


    private function getBaseUrl(): string
    {
        return \rex_yrewrite::getCurrentDomain()->getUrl();
    }

    #[Field]
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

    #[Field]
    public function getSrc(): string
    {
        return \rex_media_manager::getUrl($this->isSVG() ? '' : $this->mediaType, $this->media->getFileName());
    }

    private function isSVG(): bool
    {
        return $this->media->getExtension() === 'svg';
    }

    #[Field]
    public function getWidth(): int
    {
        return $this->getDimensions()['width'];
    }

    #[Field]
    public function getHeight(): int
    {
        return $this->getDimensions()['height'];
    }

    #[Field]
    public function getExtension(): string
    {
        return $this->media->getExtension();
    }

    #[Field]
    public function getSize(): string
    {
        return $this->media->getSize();
    }

    #[Field]
    public function getFormattedSize(): string
    {
        return $this->media->getFormattedSize();
    }


    /**
     * @param string $name Name of the media
     *
     * @return Media proxy object
     */
    public static function getByName(string $name, string $mediaType): Media
    {
        $a = new Media();
        $media = \rex_media::get($name);
        if (!$media) {
            throw new \Exception("Media for name $name not found");
        }
        $a->media = $media;
        $a->mediaType = $mediaType;
        return $a;
    }

    /**
     * @param \rex_media $obj \rex_article object to encapsulate
     *
     * @return Media proxy object
     */
    public static function getByObject(\rex_media $obj, string $mediaType): Media
    {
        $a = new Media();
        $a->media = $obj;
        $a->mediaType = $mediaType;
        return $a;
    }

    private function getDimensions(): array
    {
        if ($this->dimensions) {
            return $this->dimensions;
        }
        $imgSize = [];
        $filePath = \rex_path::media($this->media->getFileName());
        if ($this->isSVG()) {
            $content = file_get_contents($filePath);
            if (preg_match('!\bviewBox="\b[\d\.]+\s[\d\.]+\s([\d\.]+)\s([\d\.]+)"!', $content, $matches)) {
                $imgSize = [$matches[1], $matches[2]];
            } elseif (preg_match('!\bwidth="\b([\d\.]+)"!', $content, $widthMatches)) {
                if (preg_match('!\bheight="\b([\d\.]+)"!', $content, $heightMatches)) {
                    $imgSize = [$widthMatches[1], $heightMatches[1]];
                }
            }
        } elseif ($this->mediaType) {
            $mediaManager = \rex_media_manager::create($this->mediaType, $this->media->getFileName());

            if (version_compare(\rex_addon::get('media_manager')->getVersion(), '2.11.0', '>=')) {
                $cachePath = \rex_path::addonCache('media_manager');
            } else {
                $cachePath = \rex_path::addonCache('media_manager', $this->mediaType);
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
