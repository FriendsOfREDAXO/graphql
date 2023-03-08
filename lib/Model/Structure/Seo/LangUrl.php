<?php

namespace Headless\Model\Structure\SEO;

use TheCodingMachine\GraphQLite\Annotations\Type;
use TheCodingMachine\GraphQLite\Annotations\Field;

/**
 * @Type()
 */
class LangUrl
{

    private string $langCode;
    private string $url;

    public function __construct(string $langCode, string $url)
    {
        $this->langCode = $langCode;
        $this->url = $url;
    }

    /**
     * @Field()
     */
    public function getLangCode(): string
    {
        return $this->langCode;
    }

    /**
     * @Field()
     */
    public function getUrl(): string
    {
        return $this->url;
    }

}
