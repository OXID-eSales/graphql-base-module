<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Service;

use OxidEsales\GraphQL\Base\Exception\FingerprintMissingException;

interface CookieServiceInterface
{
    public function setFingerprintCookie(string $fingerprint): void;

    /**
     * @throws FingerprintMissingException
     */
    public function getFingerprintCookie(): string;
}
