<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Service;

use OxidEsales\GraphQL\Base\Exception\FingerprintMissingException;

class CookieService implements CookieServiceInterface
{
    public function setFingerprintCookie(string $fingerprint): void
    {
        setcookie(
            name: FingerprintServiceInterface::COOKIE_KEY,
            value: $fingerprint,
            httponly: true
        );
    }

    public function getFingerprintCookie(): string
    {
        if (!key_exists(FingerprintServiceInterface::COOKIE_KEY, $_COOKIE)) {
            throw new FingerprintMissingException();
        }

        return $_COOKIE[FingerprintServiceInterface::COOKIE_KEY];
    }
}
