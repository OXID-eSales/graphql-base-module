<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\GraphQl\Service;

use OxidEsales\GraphQl\DataObject\Token;
use OxidEsales\GraphQl\Exception\InvalidTokenException;
use OxidEsales\GraphQl\Framework\RequestReaderInterface;

class TokenService implements TokenServiceInterface
{
    /** @var RequestReaderInterface  */
    private $requestReader;

    public function __construct(
        RequestReaderInterface $requestReader)
    {
        $this->requestReader = $requestReader;
    }

    /**
     * @return Token
     * @throws InvalidTokenException, NoAuthHeaderException
     * @throws \OxidEsales\GraphQl\Exception\NoAuthHeaderException
     */
    public function getToken(string $signatureKey): Token
    {
        $jwt = $this->requestReader->getAuthTokenString();
        $token = new Token();
        $token->setJwt($jwt, $signatureKey);
        return $token;
    }
}