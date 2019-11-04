<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Framework;

use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Token;
use OxidEsales\GraphQL\Base\Exception\InvalidTokenException;

class RequestReader implements RequestReaderInterface
{
    /**
     * Returns the encoded token from the authorization header
     *
     * @throws InvalidTokenException
     */
    public function getAuthToken(): ?Token
    {
        $token = null;
        $authHeader = $this->getAuthorizationHeader();
        if ($authHeader === null) {
            return null;
        }
        list($jwt) = sscanf($authHeader, 'Bearer %s');
        if (!$jwt) {
            return null;
        }
        try {
            $token = (new Parser())->parse($jwt);
        } catch (\Exception $e) {
            throw new InvalidTokenException('The token is invalid');
        }
        return $token;
    }

    /**
     * Get HTTP-Authorization header
     *
     * php-cgi under Apache does not pass HTTP Basic user/pass to PHP by default
     * For this workaround to work, add these lines to your .htaccess file:
     * RewriteCond %{HTTP:Authorization} ^(.+)$
     * RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]
     *
     * @return string|null
     */
    private function getAuthorizationHeader(): ?string
    {
        if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
            return trim($_SERVER["HTTP_AUTHORIZATION"]);
        } elseif (isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
            return trim($_SERVER['REDIRECT_HTTP_AUTHORIZATION']);
        }
        return null;
    }

    /**
     * Get the Request data
     */
    public function getGraphQLRequestData(string $inputFile = 'php://input'): array
    {
        if (isset($_SERVER['CONTENT_TYPE']) && strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false) {
            $raw = file_get_contents($inputFile) ? : '';
            $data = json_decode($raw, true) ? : [];
        } else {
            $data = $_REQUEST;
        }

        $data += [
            'query'         => null,
            'variables'     => null,
            'operationName' => null
        ];

        return $data;
    }
}
