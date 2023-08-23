<?php

namespace RexGraphQL\Type\Structure;

use rex_article;
use TheCodingMachine\GraphQLite\Annotations\Field;
use TheCodingMachine\GraphQLite\Annotations\Type;
use TheCodingMachine\GraphQLite\Types\ID;

#[Type]
class Link
{

    private ID $id;
    private string $label;
    private string $url;
    private string $target;

    public function __construct(ID $id, string $label, string $url, string $target)
    {
        $this->id = $id;
        $this->label = $label;
        $this->url = $url;
        $this->target = $target;
    }

    #[Field]
    public function getId(): ID
    {
        return $this->id;
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
    public function getTarget(): string
    {
        return $this->target;
    }

    public static function getByArticle(rex_article $article): Link
    {
        $id = new ID($article->getId());
        $label = $article->getName();
        $url = $article->getUrl();
        $target = preg_match('@^http(s)?://@', $article->getUrl()) ? '_blank' : '_self';
        return new Link($id, $label, $url, $target);
    }


}
