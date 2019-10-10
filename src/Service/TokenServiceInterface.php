<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\GraphQl\Service;

use OxidEsales\GraphQl\DataObject\Token;
use OxidEsales\GraphQl\Exception\InvalidTokenException;

interface TokenServiceInterface
{
    /**
     * @return Token
     * @throws InvalidTokenException, NoAuthHeaderException
     * @throws \OxidEsales\GraphQl\Exception\NoAuthHeaderException
     */
    public function getToken(string $signatureKey): Token;
}
