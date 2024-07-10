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
    public const LIFETIME_SECONDS = 31500000; // about 1 year

    public function setFingerprintCookie(string $fingerprint): void
    {
        setcookie(
            name: FingerprintServiceInterface::COOKIE_KEY,
            value: $fingerprint,
            expires_or_options: time() + self::LIFETIME_SECONDS,
            httponly: true
        );
    }

    public function getFingerprintCookie(): string
    {
        if (!key_exists(FingerprintServiceInterface::COOKIE_KEY, $_COOKIE)) {
            throw new FingerprintMissingException("Fingerprint missing");
        }

        return $_COOKIE[FingerprintServiceInterface::COOKIE_KEY];
    }
}
