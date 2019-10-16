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
     *  Get header Authorization
     *
     *  @return string|null
     */
    private function getAuthorizationHeader(): ?string
    {
        // should work in most cases
        if (isset($_SERVER['AUTHORIZATION'])) {
            return trim($_SERVER["AUTHORIZATION"]);
        }
        // FastCGI
        if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
            return trim($_SERVER["HTTP_AUTHORIZATION"]);
        }
        if (\function_exists('apache_request_headers')) {
            $requestHeaders = \apache_request_headers();
            // Server-side fix
            //(a nice side-effect of this fix means we don't care about capitalization for Authorization)
            $requestHeaders = array_combine(
                array_map(
                    'ucwords',
                    array_keys($requestHeaders)
                ),
                array_values($requestHeaders)
            );

            if (isset($requestHeaders['Authorization'])) {
                return trim($requestHeaders['Authorization']);
            }
        }
        return null;
    }

    /**
     * Get the Request data
     */
    public function getGraphQLRequestData(): array
    {
        if (isset($_SERVER['CONTENT_TYPE']) && strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false) {
            $raw = file_get_contents('php://input') ? : '';
            $data = json_decode($raw, true) ? : [];
        } else {
            $data = $_REQUEST;
        }

        $data += [
            'query'         => null,
            'variables'     => null,
            'operationName' => null
        ];

        if (null === $data['query']) {
            $data['query'] = '{welcome}';
        }

        return $data;
    }
}
