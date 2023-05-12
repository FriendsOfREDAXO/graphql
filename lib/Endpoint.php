<?php

namespace GraphQL;

use GraphQL\Error\DebugFlag;
use GraphQL\Validator\DocumentValidator;
use GraphQL\Validator\Rules\QueryDepth;
use rex;
use rex_extension;
use rex_extension_point;
use rex_response;
use rex_var;
use RexGraphQL\Auth\SharedSecretAuthenticationService;
use Symfony\Component\DependencyInjection\Container;
use TheCodingMachine\GraphQLite\Context\Context;
use TheCodingMachine\GraphQLite\Exceptions\WebonyxErrorHandler;
use TheCodingMachine\GraphQLite\Schema;
use TheCodingMachine\GraphQLite\SchemaFactory;
use Yiisoft\Cache\ArrayCache;

use function is_array;

class Endpoint
{
    private SchemaFactory $schemaFactory;

    public function __construct()
    {
        $this->schemaFactory = new SchemaFactory(
            new ArrayCache(), new Container(),
        );
    }

    public function executeQuery(): array
    {
        $input = $this->readInput();
        $schema = $this->generateSchema();

        $queryDepthRule = new QueryDepth(5);
        DocumentValidator::addRule($queryDepthRule);

        $queryComplexityRule = new \GraphQL\Validator\Rules\QueryComplexity(75);
        DocumentValidator::addRule($queryComplexityRule);

        return \GraphQL\GraphQL::executeQuery(
            $schema,
            $input['query'],
            null,
            new Context(),
            $input['variables'] ?? null,
        )->setErrorFormatter([WebonyxErrorHandler::class, 'errorFormatter'])
            ->setErrorsHandler([WebonyxErrorHandler::class, 'errorHandler'])
            ->toArray(self::isDebug() ? DebugFlag::INCLUDE_DEBUG_MESSAGE | DebugFlag::INCLUDE_TRACE : DebugFlag::NONE);
    }

    private function generateSchema(): Schema
    {
        $this->schemaFactory->addQueryProviderFactory(new \GraphQL\RexQueryProviderFactory());
        $this->schemaFactory->addTypeMapperFactory(new \GraphQL\RexTypeMapperFactory());
        if (static::isDebug()) {
            $this->schemaFactory->devMode();
        } else {
            $this->schemaFactory->prodMode();
        }
        $this->schemaFactory->setAuthenticationService(new SharedSecretAuthenticationService());
        return $this->schemaFactory->createSchema();
    }

    private function readInput(): array
    {
        $rawInput = file_get_contents('php://input');
        $value = rex_var::toArray($rawInput);
        if (is_array($value)) {
            return $value;
        }
        return [];
    }

    public static function registerEndpoint(): void
    {
        $endpoint = new self();
        $result = $endpoint->executeQuery();
        static::sendResponse($result);
    }

    private static function sendResponse(array $output): void
    {
        rex_response::cleanOutputBuffers();
        rex_response::sendCacheControl();
        rex_response::setStatus(rex_response::HTTP_OK);
        rex_response::setHeader('Access-Control-Allow-Origin', '*');
        rex_response::setHeader('Access-Control-Allow-Headers', 'Content-Type');
        $output = json_encode($output);
        $output = rex_extension::registerPoint(new rex_extension_point('GRAPHQL_OUTPUT_FILTER', $output));
        rex_response::sendContent($output, 'application/json');
        exit;
    }

    private static function isDebug(): bool
    {
        return rex_extension::registerPoint(
            new rex_extension_point('GRAPHQL_DEBUG', rex::isDebugMode()),
        );
    }
}
