<?php

namespace RexGraphQL\Type\Structure;

use Exception;
use rex_article_slice;
use rex_extension;
use rex_extension_point;
use rex_module;
use TheCodingMachine\GraphQLite\Annotations\Field;
use TheCodingMachine\GraphQLite\Annotations\Type;
use TheCodingMachine\GraphQLite\Exceptions\GraphQLException;
use TheCodingMachine\GraphQLite\Types\ID;

#[Type]
class ArticleSlice
{
    public rex_article_slice $slice;
    private ?string $media = null;
    private ?string $values = null;
    private ?string $mediaList = null;
    private ?string $link = null;
    private ?string $linkList = null;

    #[Field]
    public function getId(): ID
    {
        return new ID($this->slice->getId());
    }

    #[Field]
    public function getModuleCode(): ?string
    {
        $module = new rex_module($this->slice->getModuleId());
        return $module->getKey() ?: null;
    }

    #[Field]
    public function getCtypeId(): ?int
    {
        return $this->slice->getCtype();
    }

    /**
     * Values as JSON-Object.
     */
    #[Field]
    public function getValues(): ?string
    {
        if(!$this->values) {
            $this->values = $this->parseValueObjects(function ($i) {
                return $this->slice->getValueArray($i) ?: $this->slice->getValue($i);
            });
        }
        return rex_extension::registerPoint(
            new rex_extension_point('GRAPHQL_SLICE_VALUES', $this->values, [
                'slice' => $this->slice,
            ]),
        ) ?: null;
    }

    public function setValues(?string $values): void
    {
        $this->values = $values;
    }

    /**
     * Media as JSON-Object.
     */
    #[Field]
    public function getMedia(): ?string
    {
        if(!$this->media) {
            $this->media = $this->parseValueObjects(function ($i) {
                return $this->slice->getMedia($i);
            }, 10);
        }
        return $this->media;
    }

    public function setMedia(?string $media): void
    {
        $this->media = $media;
    }

    /**
     * Medialist as JSON-Object.
     */
    #[Field]
    public function getMediaList(): ?string
    {
        if(!$this->mediaList) {
            $this->mediaList = $this->parseValueObjects(function ($i) {
                return $this->slice->getMediaList($i);
            }, 10);
        }
        return $this->mediaList;
    }

    public function setMediaList(?string $mediaList): void
    {
        $this->mediaList = $mediaList;
    }

    /**
     * Link as JSON-Object.
     */
    #[Field]
    public function getLink(): ?string
    {
        if(!$this->link) {
            $this->link = $this->parseValueObjects(function ($i) {
                return $this->slice->getLink($i);
            });
        }
        return $this->link;
    }

    public function setLink(?string $link): void
    {
        $this->link = $link;
    }

    /**
     * Linklist as JSON-Object.
     */
    #[Field]
    public function getLinkList(): ?string
    {
        if(!$this->linkList) {
            $this->linkList = $this->parseValueObjects(function ($i) {
                return $this->slice->getLinkList($i);
            });
        }
        return $this->linkList;
    }

    public function setLinkList(?string $linkList): void
    {
        $this->linkList = $linkList;
    }

    #[Field]
    public function isOnline(): bool
    {
        return $this->slice->isOnline();
    }

    /**
     * @param callable $callback function to get value
     * @param int      $count    max number of values
     */
    private function parseValueObjects(callable $callback, int $count = 20): ?string
    {
        $values = [];
        $found = false;
        for ($i = 1; $i <= $count; ++$i) {
            $value = $callback($i);
            if ($value) {
                $values[$i] = $value;
                $found = true;
            }
        }
        if ($found) {
            return json_encode($values);
        }
        return null;
    }

    /**
     * @param int $id id of \rex_article_slice
     *
     * @return ArticleSlice proxy object
     */
    public static function getById(int $id): self
    {
        $s = new self();
        $s->slice = rex_article_slice::getArticleSliceById($id);
        if (!$s->slice->isOnline() && !\rex::getUser()) {
            throw new Exception("Slice with id {$s->slice->getId()} is not online");
        }
        return $s;
    }

    /**
     * @param rex_article_slice $obj \rex_article_slice object to encapsulate
     *
     * @return ArticleSlice proxy object
     * @throws GraphQLException if slice is not online
     */
    public static function getByObject(rex_article_slice $obj): self
    {
        $s = new self();
        $s->slice = $obj;
        if (!$s->slice->isOnline() && !\rex::getUser()) {
            throw new GraphQLException("Slice with id {$s->slice->getId()} is not online");
        }
        return $s;
    }
}
