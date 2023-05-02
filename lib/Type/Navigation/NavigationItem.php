<?php

namespace RexGraphQL\Type\Navigation;

use TheCodingMachine\GraphQLite\Annotations\Field;
use TheCodingMachine\GraphQLite\Annotations\Type;
use TheCodingMachine\GraphQLite\Types\ID;

#[Type]
class NavigationItem
{

    private $id;
    private ?string $label;
    private ?string $url;
    private ?int $parentId;
    private ?bool $isActive;
    private ?bool $internal;

    public function __construct(mixed $id = null, ?string $label = null, ?string $url = null, ?int $parentId = null, ?bool $isActive = null, ?bool $internal = null)
    {
        $this->id = $id;
        $this->label = $label;
        $this->url = $url;
        $this->parentId = $parentId ?: null;
        $this->isActive = $isActive;
        $this->internal = $internal;
    }

    public static function getByNavbuilderItem(array $item, ?int $parentId, int $articleId): self
    {
        $active = false;
        if ($item['type'] === 'intern') {
            $navItem = static::getByArticleId($item['id'], $articleId);
            $url = $navItem->getUrl();
            $active = $navItem->isActive();
            $id = $item['id'];
        } else {
            $url = $item['href'];
            $id = uniqid();
        }
        return new self($id, $item['name'], $url, $parentId, $active, $item['type'] !== 'extern');
    }

    #[Field]
    public function getId(): ID
    {
        return new ID($this->id);
    }

    #[Field]
    public function getLabel(): string
    {
        return $this->label;
    }

    #[Field]
    public function getUrl(): string
    {
        return $this->url;
    }

    #[Field]
    public function getParentId(): ?int
    {
        return $this->parentId;
    }

    #[Field]
    public function isActive(): bool
    {
        return $this->isActive;
    }

    #[Field]
    public function isInternal(): bool
    {
        return $this->internal;
    }

    public static function getByArticleId(int $id, int $currentId): ?self
    {
        $article = \rex_article::get($id);

        if ($article instanceof \rex_article) {
            return static::getByArticle($article, $currentId);
        }
        throw new \Exception("Article with id $id not found");
    }

    public static function getByArticle(\rex_article $article, ?int $currentId): ?self
    {
        $id = $article->getId();
        $label = $article->getName();
        $url = $article->getUrl();
        if ($article->isStartArticle()) {
            $parentId = $article->getId();
        } else {
            $parentId = $article->getParentId() ?: null;
        }
        $active = $currentId && $id === $currentId;
        $self = new self($id, $label, $url, $parentId, $active, true);
        return \rex_extension::registerPoint(
            new \rex_extension_point('GRAPHQL_PARSE_ARTICLE_NAVIGATION_ITEM', $self, [
                'article' => $article,
                'currentId' => $currentId,
            ])
        );
    }

    public static function getByCategory(\rex_category $category, ?int $currentId): ?self
    {
        $id = $category->getId();
        $label = $category->getName();
        $url = $category->getUrl();
        $parentId = $category->getParentId() ?: null;
        $active = false;
        if($currentId) {
            $currentArticle = \rex_article::get($currentId);
            $path = explode('|', $currentArticle->getPath());
            $active = in_array($id, $path);
        }
        $self = new self($id, $label, $url, $parentId, $active, true);
        return \rex_extension::registerPoint(
            new \rex_extension_point('GRAPHQL_PARSE_CATEGORY_NAVIGATION_ITEM', $self, [
                'category' => $category,
                'currentId' => $currentId,
            ])
        );
    }

}
