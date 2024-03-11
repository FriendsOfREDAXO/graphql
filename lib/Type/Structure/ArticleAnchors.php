<?php

namespace RexGraphQL\Type\Structure;


use TheCodingMachine\GraphQLite\Annotations\Field;
use TheCodingMachine\GraphQLite\Annotations\Type;
use TheCodingMachine\GraphQLite\Types\ID;

#[Type]
class ArticleAnchors
{
    private string $href = '';
    private string $type = '';
    private string $target = '';
    private string $label = '';

    /**
     * @var string[]
     */
    private array $attributes = [];


    private string $attributes_json_str = '';

    public function __construct( string $href, string $label, string $target,
        string $type, array $attributes = [], string $attributes_json_str = ''
    )
    {
        $this->label = $label;
        $this->target = $target;
        $this->type = $type;
        $this->href  = $href;
        $this->attributes = $attributes;
        $this->attributes_json_str = $attributes_json_str;
    }

    #[Field]
    public function getHref(): ?string
    {
        return $this->href;
    }

    #[Field]
    public function getTarget(): ?string
    {
        return $this->target;
    }

    #[Field]
    public function getType(): ?string
    {
        return $this->type;
    }

    #[Field]
    public function getLabel(): ?string
    {
        return $this->label;
    }

    #[Field]
    /**
     * @return string[]
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    #[Field]
    /**
     * @return string
     */
    public function getAttributesJsonStr(): string
    {
        return json_encode($this->attributes);
    }

    public function setAttribute(string $k, string $v): void
    {
         $this->attributes[$k] = $v;
    }


}
