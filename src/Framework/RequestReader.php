<?php declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\GraphQL\Framework;

use OxidEsales\GraphQL\DataObject\Token;

class RequestReader implements RequestReaderInterface
{
    /**
     * Returns the encoded token from the authorization header
     */
    public function getAuthToken(): ?string
    {
        $authHeader = $this->getAuthorizationHeader();
        if ($authHeader === null) {
            return null;
        }
        list($jwt) = sscanf($authHeader, 'Bearer %s');
        if (!$jwt) {
            return null;
        }
        return $jwt;
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
