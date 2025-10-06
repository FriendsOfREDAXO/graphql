<?php

namespace RexGraphQL;

use GraphQL\Error\DebugFlag;
use GraphQL\GraphQL;
use GraphQL\Service\Auth\AuthService;
use GraphQL\Validator\DocumentValidator;
use GraphQL\Validator\Rules\QueryComplexity;
use GraphQL\Validator\Rules\QueryDepth;
use JetBrains\PhpStorm\NoReturn;
use rex;
use rex_extension;
use rex_extension_point;
use rex_response;
use rex_var;
use RexGraphQL\Middleware\ValidateFieldMiddleware;
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
    private Container $container;

    public function __construct()
    {
        $this->container = new Container();
        $this->schemaFactory = new SchemaFactory(
            new ArrayCache(), $this->container,
        );
    }

    public function executeQuery(): array
    {
        $input = $this->readInput();
        $schema = $this->generateSchema();

        $queryDepthRule = new QueryDepth(11);
        DocumentValidator::addRule($queryDepthRule);

        $queryComplexityRule = new QueryComplexity(200);
        DocumentValidator::addRule($queryComplexityRule);
        if (!$input['query']) {
            return [
                'errors' => [
                    [
                        'message' => 'No query provided',
                    ],
                ],
            ];
        }

        $result = GraphQL::executeQuery(
            $schema,
            $input['query'],
            null,
            new Context(),
            $input['variables'],
        )->setErrorFormatter([WebonyxErrorHandler::class, 'errorFormatter'])
            ->setErrorsHandler([WebonyxErrorHandler::class, 'errorHandler'])
            ->toArray(self::isDebug() ? DebugFlag::INCLUDE_DEBUG_MESSAGE | DebugFlag::INCLUDE_TRACE : DebugFlag::NONE);

        if (isset($result['errors'])) {
            foreach ($result['errors'] as $error) {
                $msg = $error['extensions']['debugMessage'] ?? $error['message'];
                $file = $error['extensions']['file'] ?? '';
                $line = $error['extensions']['line'] ?? 0;

                if (isset($error['path'])) {
                    $msg .= "\n (path: " . implode('->', $error['path']) . ')';
                }
                foreach ($error['extensions']['trace'] ?? [] as $trace) {
                    $msg .= "\n" . ($trace['file'] ?? '') . ':' . ($trace['line'] ?? '') . ' ' . ($trace['call'] ?? '');
                }
                \rex_logger::logError(1, $msg, $file, $line);
            }
        }

        return $result;
    }

    private function generateSchema(): Schema
    {
        $this->schemaFactory->addQueryProviderFactory(new RexQueryProviderFactory());
        $this->schemaFactory->addTypeMapperFactory(new RexTypeMapperFactory());
        if (static::isDebug()) {
            $this->schemaFactory->devMode();
        } else {
            $this->schemaFactory->prodMode();
        }
        $this->schemaFactory->setAuthenticationService(new AuthService());

        // field validation
        $this->schemaFactory->addParameterMiddleware(new ValidateFieldMiddleware($this->container));

        return $this->schemaFactory->createSchema();
    }

    private function readInput(): array
    {
        $rawInput = file_get_contents('php://input');
        $value = rex_var::toArray($rawInput);
        $operations = rex_request('operations', 'string');
        if ($operations) {
            return json_decode($operations, true);
        }
        if (is_array($value)) {
            return $value;
        }
        return [];
    }

    #[NoReturn] public static function registerEndpoint(): void
    {
        $endpoint = new self();
        rex_response::setHeader('Access-Control-Allow-Origin', '*');
        rex_response::setHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization');
        $result = $endpoint->executeQuery();
        static::sendResponse($result);
    }

    #[NoReturn] private static function sendResponse(array $output): void
    {
        rex_response::cleanOutputBuffers();
        rex_response::sendCacheControl();
        rex_response::setStatus(rex_response::HTTP_OK);
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
