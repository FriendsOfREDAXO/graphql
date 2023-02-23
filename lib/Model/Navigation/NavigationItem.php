<?php

namespace Headless\Model\Navigation;

use TheCodingMachine\GraphQLite\Annotations\Field;
use TheCodingMachine\GraphQLite\Annotations\Type;
use TheCodingMachine\GraphQLite\Types\ID;


/**
 * @Type
 */
class NavigationItem
{

    private int     $id;
    private ?string $label;
    private ?string $url;
    private ?int    $parentId;

    public function __construct(int $id, ?string $label = null, ?string $url = null, ?int $parentId = null)
    {
        $this->id       = $id;
        $this->label    = $label;
        $this->url      = $url;
        $this->parentId = $parentId;
    }


    /**
     * @Field()
     * @return ID
     */
    public function getId(): ID
    {
        return new ID($this->id);
    }


    /**
     * @Field()
     */
    public function getLabel(): ?string
    {
        return $this->label;
    }

    /**
     * @Field()
     */
    public function getUrl(): ?string
    {
        return $this->url;
    }

    /**
     * @Field()
     */
    public function getParentId(): ?int
    {
        return $this->parentId;
    }

    public static function getByArticle(\rex_article $article): self
    {
        $id       = $article->getId();
        $label    = $article->getName();
        $url      = $article->getUrl();
        if($article->isStartArticle()) {
            $parentId = $article->getId();
        } else {
            $parentId = $article->getParentId() ?: null;
        }
        return new self($id, $label, $url, $parentId);
    }

    public static function getByCategory(\rex_category $rootCategory): self
    {
        $id       = $rootCategory->getId();
        $label    = $rootCategory->getName();
        $url      = $rootCategory->getUrl();
        $parentId = $rootCategory->getParentId() ?: null;
        return new self($id, $label, $url, $parentId);
    }

}
