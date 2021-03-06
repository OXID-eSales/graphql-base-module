<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Framework;

use Exception;
use GraphQL\Upload\UploadMiddleware;
use Laminas\Diactoros\ServerRequestFactory;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Token;
use OxidEsales\GraphQL\Base\Exception\InvalidToken;
use OxidEsales\GraphQL\Base\Infrastructure\Legacy as LegacyService;
use OxidEsales\GraphQL\Base\Service\Authentication;

use function apache_request_headers;
use function array_change_key_case;
use function file_get_contents;
use function json_decode;
use function sscanf;
use function strpos;
use function trim;

class RequestReader
{
    /** @var LegacyService */
    private $legacyService;

    public function __construct(
        LegacyService $legacyService
    ) {
        $this->legacyService = $legacyService;
    }

    /**
     * Returns the encoded token from the authorization header
     *
     * @throws InvalidToken
     */
    public function getAuthToken(): ?Token
    {
        $token      = new NullToken();
        $authHeader = $this->getAuthorizationHeader();

        if ($authHeader === null) {
            return $token;
        }
        [$jwt] = sscanf($authHeader, 'Bearer %s');

        if (!$jwt) {
            return $token;
        }

        try {
            $token = (new Parser())->parse($jwt);
        } catch (Exception $e) {
            throw InvalidToken::unableToParse();
        }

        $userId = $token->claims()
                        ->get(Authentication::CLAIM_USERID);

        $groups = $this->legacyService
                       ->getUserGroupIds(
                           $userId
                       );

        if (in_array('oxidblocked', $groups)) {
            throw InvalidToken::userBlocked();
        }

        return $token;
    }

    /**
     * Get the Request data
     *
     * @return array{query: string, variables: string[], operationName: string}
     */
    public function getGraphQLRequestData(string $inputFile = 'php://input'): array
    {
        if (isset($_SERVER['CONTENT_TYPE']) && strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false) {
            $raw  = file_get_contents($inputFile) ?: '';
            $data = json_decode($raw, true) ?: [];
        } elseif (isset($_SERVER['CONTENT_TYPE']) && strpos($_SERVER['CONTENT_TYPE'], 'multipart/form-data') !== false) {
            $request          = ServerRequestFactory::fromGlobals();
            $uploadMiddleware = new UploadMiddleware();
            $request          = $uploadMiddleware->processRequest($request);
            $data             = $request->getParsedBody();
        } else {
            $data = $_REQUEST;

            if (isset($data['variables'])) {
                $data['variables'] = json_decode($data['variables'], true);
            }
        }

        return [
            'query'         => $data['query'] ?? null,
            'variables'     => $data['variables'] ?? null,
            'operationName' => $data['operationName'] ?? null,
        ];
    }

    /**
     * Get HTTP-Authorization header
     *
     * php-cgi under Apache does not pass HTTP Basic user/pass to PHP by default
     * For this workaround to work, add these lines to your .htaccess file:
     * RewriteCond %{HTTP:Authorization} ^(.+)$
     * RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]
     */
    private function getAuthorizationHeader(): ?string
    {
        if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
            return trim($_SERVER['HTTP_AUTHORIZATION']);
        }

        if (isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
            return trim($_SERVER['REDIRECT_HTTP_AUTHORIZATION']);
        }

        if (function_exists('apache_request_headers')) {
            $headers = apache_request_headers();

            if (is_array($headers)) {
                $headers = array_change_key_case($headers, CASE_LOWER);

                if (isset($headers['authorization'])) {
                    return trim($headers['authorization']);
                }
            }
        }

        return null;
    }
}
