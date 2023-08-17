<?php

namespace RexGraphQL\Type\Structure;


use TheCodingMachine\GraphQLite\Annotations\Field;
use TheCodingMachine\GraphQLite\Annotations\Type;
use TheCodingMachine\GraphQLite\Types\ID;

#[Type]
class Forward
{
    private string $url;
    private int $status;

    public function __construct(string $url, int $status)
    {
        $this->url = $url;
        $this->status = $status;
    }

    #[Field]
    public function getUrl(): string
    {
        return $this->url;
    }
    #[Field]
    public function getStatus(): int
    {
        return $this->status;
    }

    public static function getForwardFromArray(array $forward): ?Forward
    {
        $type = $forward['type'];
        $url = null;
        if($type == 'article') {
            $article = \rex_article::get($forward['article_id'], $forward['clang_id']);
            $url = $article->getUrl();
        } else if($type == 'extern') {
            $url = $forward['extern'];
        }
        if($url) {
            return new Forward($url, $forward['movetype']);
        }
        return null;
    }
}
