<?php

namespace RexGraphQL\Type\Structure;

use Exception;
use rex;
use rex_clang;
use RexGraphQL\Extensions;
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
    private ?Article $relatedArticle;

    public function __construct(string $type = null, int $clangId = null, ID $elementId = null, Article $relatedArticle = null)
    {
        $this->type = $type;
        $this->clangId = $clangId;
        $this->elementId = $elementId;
        $this->relatedArticle = $relatedArticle;
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
        if (in_array($this->type, ['forward', 'article_redirect'])) {
            return null;
        }
        if ('article' === $this->type) {
            return Metadata::getByArticleId($this->elementId->val(), $this->clangId);
        }
        return Metadata::getByUrlObject();
    }

    /**
     * @return Clang[]
     * @throws Exception
     */
    #[Field]
    public function getClangs(): array
    {
        $clangs = [];
        if (in_array($this->type, ['forward', 'article_redirect'])) {
            $clang = Clang::getById($this->clangId);
            $clang->isActive = true;
            return [$clang];
        }
        if ('article' === $this->type) {
            foreach (rex_clang::getAll(\rex::getUser() == null) as $_clang) {
                $article = \rex_article::get($this->elementId->val(), $_clang->getId());
                if ($article && ($article->isOnline() || rex::getUser())) {
                    $clang = Clang::getById($_clang->getId());
                    $clang->url = rex_getUrl($this->elementId->val(), $_clang->getId());
                    $clang->isActive = rex_clang::getCurrentId() === $_clang->getId();
                    $clangs[] = $clang;
                }
            }
        } else {
            /** @var UrlManager $urlObject */
            $urlObject = rex::getProperty('url_object');
            $urlObjects = $urlObject->getHreflang(rex_clang::getAllIds(\rex::getUser() == null));
            foreach ($urlObjects as $_urlObject) {
                $clang = Clang::getById($_urlObject->getClangId());
                $url = $_urlObject->getUrl()->getPath();
                if($url) {
                    $url = Extensions::sanitizeArticleUrl($url);
                }
                $clang->url = $url;
                $clang->isActive = rex_clang::getCurrentId() === $_urlObject->getClangId();
                $clangs[] = $clang;
            }
        }
        return $clangs;
    }

    #[Field]
    public function getRelatedArticle(): ?Article
    {
        return $this->relatedArticle;
    }
}
