<?php

namespace RexGraphQL\Type\Structure;

use Exception;
use rex;
use rex_clang;
use TheCodingMachine\GraphQLite\Annotations\Field;
use TheCodingMachine\GraphQLite\Annotations\Type;
use TheCodingMachine\GraphQLite\Exceptions\GraphQLException;
use TheCodingMachine\GraphQLite\Types\ID;
use Url\UrlManager;

#[Type]
class ContentType
{
    private ?string $type;
    private ?int $clangId;
    private ?ID $elementId;

    public function __construct(string $type = null, int $clangId = null, ID $elementId = null)
    {
        $this->type = $type;
        $this->clangId = $clangId;
        $this->elementId = $elementId;
    }

    #[Field]
    public function getType(): string
    {
        return $this->type;
    }

    #[Field]
    public function getElementId(): ID
    {
        return $this->elementId;
    }

    /**
     * @throws GraphQLException
     */
    #[Field]
    public function getMetadata(): ?Metadata
    {
        if ('article' === $this->type) {
            return Metadata::getByArticleId($this->elementId->val(), $this->clangId);
        }
        if('forward' === $this->type) {
            return null;
        }
        return Metadata::getByUrlObject();
    }

    /**
     * @throws Exception
     * @return Clang[]
     */
    #[Field]
    public function getClangs(): array
    {
        $clangs = [];
        if('forward' === $this->type) {
            $clang = Clang::getById($this->clangId);
            $clang->isActive = true;
            return [$clang];
        }
        if ('article' === $this->type) {
            foreach (rex_clang::getAll(1) as $_clang) {
                $article = \rex_article::get($this->elementId->val(), $_clang->getId());
                if($article && $article->isOnline()) {
                    $clang = Clang::getById($_clang->getId());
                    $clang->url = rex_getUrl($this->elementId->val(), $_clang->getId());
                    $clang->isActive = rex_clang::getCurrentId() === $_clang->getId();
                    $clangs[] = $clang;
                }
            }
        } else {
            /** @var UrlManager $urlObject */
            $urlObject = rex::getProperty('url_object');
            $urlObjects = $urlObject->getHreflang(rex_clang::getAllIds(1));
            foreach ($urlObjects as $_urlObject) {
                $clang = Clang::getById($_urlObject->getClangId());
                $clang->url = $_urlObject->getUrl()->getPath();
                $clang->isActive = rex_clang::getCurrentId() === $_urlObject->getClangId();
                $clangs[] = $clang;
            }
        }
        return $clangs;
    }
}
