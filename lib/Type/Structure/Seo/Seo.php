<?php

namespace GraphQL\Type\Structure\Seo;

use GraphQL\Type\Media\Media;
use TheCodingMachine\GraphQLite\Annotations\Type;
use TheCodingMachine\GraphQLite\Annotations\Field;

#[Type]
class Seo
{
    public ?\rex_article $article = null;
    public ?\rex_yrewrite_seo $seo = null;

    #[Field]
    public function getTitle(): ?string
    {
        if ($this->seo) {
            return $this->seo->getTitle();
        }
        return null;
    }

    #[Field]
    public function getDescription(): ?string
    {
        if ($this->seo) {
            return $this->seo->getDescription();
        }
        return null;
    }

    #[Field]
    public function getCanonical(): ?string
    {
        if ($this->seo) {
            return $this->seo->getCanonicalUrl();
        }
        return null;
    }

    #[Field]
    public function getRobots(): ?string
    {
        if ($this->seo) {
            $index = $this->article->getValue(
                    \rex_yrewrite_seo::$meta_index_field
                ) ?? \rex_yrewrite_seo::$index_setting_default;
            if (1 == $index || (0 == $index && $this->article->isOnline())) {
                return 'index, follow';
            } elseif (2 == $index) {
                return 'noindex, follow';
            }
            return 'noindex, nofollow';
        }
        return null;
    }

    /**
     * @return LangUrl[]
     */
    #[Field]
    public function getAlternateLanguages(): array
    {
        $lang_domains = [];
        foreach (\rex_clang::getAll(true) as $clang) {
            $article = \rex_article::get($this->article->getId(), $clang->getId());
            if ($article->isOnline()) {
                $lang_domains[] = new LangUrl($clang->getCode(), $article->getUrl());
            }
        }
        $defaultLang = \rex_clang::get(\rex_clang::getStartId());
        if ($defaultLang) {
            $defaultLangArticle = \rex_article::get($this->article->getId(), $defaultLang->getId());
            if ($defaultLangArticle && $defaultLangArticle->isOnline()) {
                $lang_domains[] = new LangUrl('x-default', $defaultLangArticle->getUrl());
            }
        }
        return $lang_domains;
    }

    #[Field]
    public function getImage(): ?Media
    {
        if ($this->seo) {
            $image = $this->seo->getImage();
            if ($image) {
                return Media::getByName($image, 'yrewrite_seo_image');
            }
        }
        return null;
    }

    public static function getByArticle(\rex_article $article): ?Seo
    {
        $seo = new Seo();
        $seoData = new \rex_yrewrite_seo($article->getId());
        $seo->article = $article;
        $seo->seo = $seoData;
        return $seo;
    }

    public static function getByArticleId(int $articleId): ?Seo
    {
        $article = \rex_article::get($articleId);
        if ($article instanceof \rex_article) {
            return self::getByArticle($article);
        }
        return null;
    }
}
