<?php declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\GraphQl\Framework;

use OxidEsales\GraphQl\DataObject\Token;
use OxidEsales\GraphQl\Exception\InvalidTokenException;
use OxidEsales\GraphQl\Exception\NoAuthHeaderException;

class RequestReader implements RequestReaderInterface
{
    /**
     * Returns the encoded token from the authorization header
     *
     * @return string
     * @throws NoAuthHeaderException
     */
    public function getAuthTokenString(): string
    {
        $authHeader = $this->getAuthorizationHeader();
        if (! $authHeader) {
            throw new NoAuthHeaderException();
        }
        list($jwt) = sscanf( $authHeader, 'Bearer %s');
        return $jwt;
    }

    /**
     *  Get header Authorization
     *
     *  @return $aHeaders array
     */
    public function getAuthorizationHeader(){

        $authHeader = null;

        if (isset($_SERVER['Authorization'])) {
            $authHeader = trim($_SERVER["Authorization"]);
        }
        else if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
            //Nginx or fast CGI
            $authHeader = trim($_SERVER["HTTP_AUTHORIZATION"]);
        } elseif (function_exists('apache_request_headers')) {
            $requestHeaders = apache_request_headers();
            // Server-side fix
            //(a nice side-effect of this fix means we don't care about capitalization for Authorization)
            $requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));

            if (isset($requestHeaders['Authorization'])) {
                $authHeader = trim($requestHeaders['Authorization']);
            }
        }
        return $authHeader;
    }

    /**
     * Get the Request data
     *
     * @return array
     */
    public function getGraphQLRequestData()
    {
        if (isset($_SERVER['CONTENT_TYPE']) && strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false) {
            $raw = file_get_contents('php://input') ?: '';
            $data = json_decode($raw, true) ?: [];
        } else {
            $data = $_REQUEST;
        }

        $data += ['query' => null, 'variables' => null, 'operationName' => null];

        if (null === $data['query']) {
            $Data['query'] = '{welcome}';
        }

        return $data;
    }
}
