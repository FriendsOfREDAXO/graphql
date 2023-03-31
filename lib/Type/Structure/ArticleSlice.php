<?php

namespace GraphQL\Type\Structure;

use TheCodingMachine\GraphQLite\Types\ID;
use TheCodingMachine\GraphQLite\Annotations\Field;
use TheCodingMachine\GraphQLite\Annotations\Type;

#[Type]
class ArticleSlice
{

    public \rex_article_slice $slice;

    #[Field]
    public function getId(): ID
    {
        return new ID($this->slice->getId());
    }

    #[Field]
    public function getModuleCode(): ?string
    {
        $module = new \rex_module($this->slice->getModuleId());
        return $module->getKey() ?: null;
    }

    /**
     * Values as JSON-Object
     */
    #[Field]
    public function getValues(): ?string
    {
        $values = $this->parseValueObjects(function ($i) {
            return $this->slice->getValueArray($i) ?: $this->slice->getValue($i);
        });
        return \rex_extension::registerPoint(
            new \rex_extension_point('GRAPHQL_SLICE_VALUES', $values, [
                'slice' => $this->slice,
            ])
        ) ?: null;
    }

    /**
     * Media as JSON-Object
     */
    #[Field]
    public function getMedia(): ?string
    {
        return $this->parseValueObjects(function ($i) {
            return $this->slice->getMedia($i);
        }, 10);
    }

    /**
     * Medialist as JSON-Object
     *
     */
    #[Field]
    public function getMediaList(): ?string
    {
        return $this->parseValueObjects(function ($i) {
            return $this->slice->getMediaList($i);
        }, 10);
    }

    /**
     * Link as JSON-Object
     */
    #[Field]
    public function getLink(): ?string
    {
        return $this->parseValueObjects(function ($i) {
            return $this->slice->getLink($i);
        });
    }

    /**
     * Linklist as JSON-Object
     */
    #[Field]
    public function getLinkList(): ?string
    {
        return $this->parseValueObjects(function ($i) {
            return $this->slice->getLinkList($i);
        });
    }

    /**
     * @param callable $callback function to get value
     * @param int      $count    max number of values
     */
    private function parseValueObjects(callable $callback, int $count = 20): ?string
    {
        $values = [];
        $found = false;
        for ($i = 1; $i <= $count; $i++) {
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
    public static function getById(int $id): ArticleSlice
    {
        $s = new ArticleSlice();
        $s->slice = \rex_article_slice::getArticleSliceById($id);
        return $s;
    }

    /**
     * @param \rex_article_slice $obj \rex_article_slice object to encapsulate
     *
     * @return ArticleSlice proxy object
     */
    public static function getByObject(\rex_article_slice $obj): ArticleSlice
    {
        $s = new ArticleSlice();
        $s->slice = $obj;
        return $s;
    }
}
