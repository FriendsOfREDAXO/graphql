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
    private string $type;

    public function __construct(ID $id, string $label, string $url, string $target, string $type)
    {
        $this->id = $id;
        $this->label = $label;
        $this->url = $url;
        $this->target = $target;
        $this->type = $type;
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

    #[Field]
    public function getType(): string
    {
        return $this->type;
    }

    public static function getByArticle(rex_article $article): Link
    {
        $id = new ID($article->getId());
        $label = $article->getName();
        $url = $article->getUrl();
        $external = preg_match('@^http(s)?://@', $article->getUrl());
        $target = $external ? '_blank' : '_self';
        $type = $external ? 'url' : 'article';
        return new Link($id, $label, $url, $target, $type);
    }


}
