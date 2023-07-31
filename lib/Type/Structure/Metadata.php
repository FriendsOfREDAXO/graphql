<?php

namespace RexGraphQL\Type\Structure;

use DateTimeInterface;
use Exception;
use RexGraphQL\Type\Media\Media;
use rex;
use rex_article;
use rex_exception;
use rex_yrewrite;
use rex_yrewrite_seo;
use TheCodingMachine\GraphQLite\Annotations\Field;
use TheCodingMachine\GraphQLite\Annotations\Type;
use TheCodingMachine\GraphQLite\Exceptions\GraphQLException;
use Url\UrlManager;

#[Type]
class Metadata
{
    private ?string $title;
    private ?string $description;
    private ?string $robots;
    private ?string $canonical;
    private ?string $image;

    private ?int $createdAt;
    private ?int $updatedAt;
    public ?string $type = 'article';

    public function __construct(string $title = null, string $description = null, string $robots = null, string $canonical = null, string $image = null, ?int $createdAt = null, ?int $updatedAt = null)
    {
        $this->title = $title;
        $this->description = $description;
        $this->robots = $robots;
        $this->canonical = $canonical;
        $this->image = $image;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    #[Field]
    public function getTitle(): string
    {
        return $this->title;
    }

    #[Field]
    public function getDescription(): string
    {
        return $this->description;
    }

    #[Field]
    public function getRobots(): string
    {
        return $this->robots;
    }

    #[Field]
    public function getCanonical(): string
    {
        return $this->canonical;
    }

    /**
     * @throws Exception if the image is not found
     */
    #[Field]
    public function getImage(): ?Media
    {
        if (!$this->image) {
            return null;
        }
        return Media::getByName($this->image, 'yrewrite_seo_image');
    }

    /**
     * @return Breadcrumb[]
     * @throws GraphQLException if the article is not found
     */
    #[Field]
    public function getBreadcrumbs(): array
    {
        if ('article' === $this->type) {
            return Breadcrumb::getAllForArticle(rex_article::getCurrent());
        }
        return Breadcrumb::getAllForUrlObject();
    }

    #[Field]
    public function getCreatedAt(): string
    {
        return date(DateTimeInterface::ISO8601, $this->createdAt);
    }

    #[Field]
    public function getUpdatedAt(): string
    {
        return date(DateTimeInterface::ISO8601, $this->updatedAt);
    }

    /**
     * @throws GraphQLException if the article is not found
     */
    public static function getByArticleId(int $elementId, int $clangId): self
    {
        $article = rex_article::get($elementId, $clangId);
        if (!$article) {
            throw new GraphQLException('Article not found');
        }
        $seo = new rex_yrewrite_seo($article->getId(), $clangId);
        $robots = 'noindex, nofollow';
        $index = $article->getValue(
            rex_yrewrite_seo::$meta_index_field,
        ) ?? rex_yrewrite_seo::$index_setting_default;
        if (1 == $index || (0 == $index && $article->isOnline())) {
            $robots = 'index, follow';
        } elseif (2 == $index) {
            $robots = 'noindex, follow';
        }
        $item = new self(
            $seo->getTitle(),
            $seo->getDescription(),
            $robots,
            $seo->getCanonicalUrl(),
            $seo->getImage(),
            $article->getCreateDate(),
            $article->getUpdateDate(),
        );
        $item->type = 'article';
        return $item;
    }

    /**
     * @throws GraphQLException|rex_exception if the url object is not found
     */
    public static function getByUrlObject(): self
    {
        /** @var UrlManager $urlObject */
        $urlObject = rex::getProperty('url_object');
        if (!$urlObject) {
            throw new GraphQLException('Url object not found');
        }
        $createdAt = strtotime($urlObject->getValue('createdate'));
        $updatedAt = strtotime($urlObject->getValue('updatedate'));
        $title = htmlspecialchars_decode(trim(rex_yrewrite::getCurrentDomain()->getTitle()));
        if ('' == $title) {
            $title = rex_yrewrite_seo::$title_scheme_default;
        }
        $title = str_replace('%T', $urlObject->getSeoTitle(), $title);
        $title = str_replace('%SN', rex::getServerName(), $title);

        $image = $urlObject->getSeoImage();
        if($image) {
            $image = explode(',', $image)[0];
        }
        $item = new self(
            $title,
            $urlObject->getSeoDescription(),
            'index, follow',
            '',
            $image,
            $createdAt,
            $updatedAt,
        );
        $item->type = $urlObject->getProfile()->getNamespace();
        return $item;
    }
}
