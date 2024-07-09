<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Service;

use OxidEsales\GraphQL\Base\Exception\FingerprintValidationException;

class FingerprintService implements FingerprintServiceInterface
{
    public function __construct(
        private CookieServiceInterface $cookieService,
    ) {
    }

    public function getFingerprint(): string
    {
        return bin2hex(random_bytes(64));
    }

    public function hashFingerprint(string $fingerprint): string
    {
        return hash('sha512', $fingerprint);
    }

    public function validateFingerprintHashToCookie(string $hashedFingerprint): void
    {
        $cookieValue = $this->cookieService->getFingerprintCookie();
        if ($this->hashFingerprint($cookieValue) !== $hashedFingerprint) {
            throw new FingerprintValidationException("Fingerprint validation failed");
        }
    }
}
