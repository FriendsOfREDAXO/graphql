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
    private string $label;
    private string $url;
    private ?int    $parentId;
    private bool   $isActive;

    public function __construct(int $id, string $label, string $url, ?int $parentId, bool $isActive)
    {
        $this->id       = $id;
        $this->label    = $label;
        $this->url      = $url;
        $this->parentId = $parentId ?: null;
        $this->isActive = $isActive;
    }

    public static function getByNavbuilderItem(array $item, ?int $parentId, int $articleId): self
    {
        $active = false;
        if ($item['type'] === 'intern') {
            $navItem = static::getByArticleId($item['id'], $articleId);
            $url     = $navItem->getUrl();
            $active  = $navItem->isActive();
        } else {
            $url = $item['url'];
        }
        return new self($item['id'], $item['name'], $url, $parentId, $active);
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
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * @Field()
     */
    public function getUrl(): string
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

    /**
     * @Field()
     */
    public function isActive(): bool
    {
        return $this->isActive;
    }

    public static function getByArticleId(int $id, int $currentId): ?self
    {
        $article = \rex_article::get($id);
        if ($article instanceof \rex_article) {
            return static::getByArticle($article, $currentId);
        }
        return null;
    }

    public static function getByArticle(\rex_article $article, int $currentId): self
    {
        $id    = $article->getId();
        $label = $article->getName();
        $url   = $article->getUrl();
        if ($article->isStartArticle()) {
            $parentId = $article->getId();
        } else {
            $parentId = $article->getParentId() ?: null;
        }
        $active = $id === $currentId;
        return new self($id, $label, $url, $parentId, $active);
    }

    public static function getByCategory(\rex_category $rootCategory, int $currentId): self
    {
        $id       = $rootCategory->getId();
        $label    = $rootCategory->getName();
        $url      = $rootCategory->getUrl();
        $parentId = $rootCategory->getParentId() ?: null;
        $currentArticle = \rex_article::get($currentId);
        $path = explode('|', $currentArticle->getPath());
        $active = in_array($id, $path);
        return new self($id, $label, $url, $parentId, $active);
    }

}
