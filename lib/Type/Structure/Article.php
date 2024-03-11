<?php

namespace RexGraphQL\Type\Structure;

use DateTimeInterface;
use GraphQL\Service\Media\MediaService;
use MFormHelpers\MForm;
use rex_article;
use rex_article_slice;
use rex_yrewrite_seo;
use RexGraphQL\Type\Media\Media;
use TheCodingMachine\GraphQLite\Annotations\Field;
use TheCodingMachine\GraphQLite\Annotations\Type;
use TheCodingMachine\GraphQLite\Exceptions\GraphQLException;
use TheCodingMachine\GraphQLite\Types\ID;

#[Type]
class Article
{
    public rex_article $article;

    #[Field]
    public function getId(): ID
    {
        return new ID($this->article->getId());
    }

    #[Field]
    public function getName(): string
    {
        return $this->article->getName();
    }

    #[Field]
    public function getUrl(): string
    {
        return $this->article->getUrl();
    }

    #[Field]
    public function getLink(): Link
    {
        return Link::getByArticle($this->article);
    }

    #[Field]
    public function isStartArticle(): bool
    {
        return $this->article->isStartArticle();
    }

    #[Field]
    public function isSiteStartArticle(): bool
    {
        return $this->article->isSiteStartArticle();
    }

    #[Field]
    public function isOnline(): bool
    {
        return $this->article->isOnline();
    }

    /**
     * @throws GraphQLException if clang is not online or not found
     */
    #[Field]
    public function getClang(): Clang
    {
        return Clang::getById($this->article->getClangId());
    }

    /**
     * @throws GraphQLException
     */
    #[Field]
    public function getMetadata(): Metadata
    {
        return Metadata::getByArticleId($this->getId()->val(), $this->getClang()->getId()->val());
    }

    /**
     * @return ArticleAnchors[]|null
     */
    #[Field]
    public function getModulesAnchor(): ?array
    {
        $slices = $this->getSlices();
        if ($slices) {
            $module_anchors = array_map(function($slice) {
                $articleId = $this->article->getId();
                $sliceId = $slice->getId();
                $customLinkValue = "{$articleId}.{$sliceId}";
                $anchorTitle = '';
                $value10 = $slice->slice->getValue(10);
                if ($value10 && str_starts_with(trim($value10), '{')) {
                    $value10 = json_decode($value10);
                    if (isset($value10->anchor_title) && strlen($value10->anchor_title) > 0) {
                        $anchorTitle = $value10->anchor_title;
                    } else {
                        return null;
                    }
                }
                $linkItem =  \Kreatif\Link::parseCustomLinkValue($customLinkValue, $anchorTitle);
                $linkItem->setAttribute('linkCode', $customLinkValue);
                return $linkItem;
            }, $slices);
            $module_anchors = array_filter($module_anchors, fn($value) => !is_null($value));
        } else {
            $module_anchors = [];
        }
        $links = [];
        foreach ($module_anchors as $module_anchor) {
            $aa = new ArticleAnchors(
                $module_anchor->getHref(),
                $module_anchor->getLabel(),
                $module_anchor->getTarget(),
               $module_anchor->getType(),
                [
                    'linkCode' => $module_anchor->getAttribute('linkCode')
                ]
            );
            $links[] = $aa;
        }
        return $links;
    }

    /**
     * @return ArticleSlice[]
     * @throws GraphQLException if some slices are not online
     */
    #[Field]
    public function getSlices(): array
    {
        $slices = rex_article_slice::getSlicesForArticle($this->article->getId(), false, 0, \rex::getUser() == null);
        return array_map(static function ($slice) {
            return ArticleSlice::getByObject($slice);
        }, $slices);
    }

    /**
     * @param string $mediaType
     * @return ?Media
     */
    #[Field]
    public function getSeoImage(string $mediaType): ?Media
    {
        $seo = new rex_yrewrite_seo($this->article->getId(), null);
        $image = $seo->getImage();
        if (!$image) {
            return null;
        }
        $service = new MediaService();
        return $service->getMediaByName($image, $mediaType);
    }

    #[Field]
    public function getCreatedAt(): string
    {
        return date(DateTimeInterface::ISO8601, $this->article->getCreateDate());
    }

    #[Field]
    public function getUpdatedAt(): string
    {
        return date(DateTimeInterface::ISO8601, $this->article->getUpdateDate());
    }

    /**
     * @param int $id id of \rex_article
     *
     * @return ?Article proxy object
     */
    public static function getById(int $id): ?self
    {
        $a = new self();
        $article = rex_article::get($id);
        if (!$article || (!$article->isOnline() && !\rex::getUser())) {
            return null;
        }
        $a->article = $article;
        return $a;
    }

    /**
     * @param rex_article $obj \rex_article object to encapsulate
     *
     * @return ?Article proxy object
     */
    public static function getByObject(rex_article $obj): ?self
    {
        $a = new self();
        if (!$obj->isOnline() && !\rex::getUser()) {
            return null;
        }
        $a->article = $obj;
        return $a;
    }

    #[Field]
    public function getTemplate(): ?Template
    {
        $templateId = $this->article->getTemplateId();
        if ($templateId) {
            return Template::getById($templateId);
        }
        return null;
    }

    #[Field]
    public function getTemplateKey(): ?string
    {
        $templateId = $this->article->getTemplateId();
        if ($templateId) {
            return Template::getById($templateId)?->getKey();
        }
        return null;
    }
}
