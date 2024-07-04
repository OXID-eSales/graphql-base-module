<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Service;

use OxidEsales\GraphQL\Base\Exception\FingerprintHashNotValidException;

class FingerprintService implements FingerprintServiceInterface
{
    public function getFingerprint(): string
    {
        return bin2hex(random_bytes(64));
    }

    public function hashFingerprint(string $fingerprint): string
    {
        return hash('sha512', $fingerprint);
    }

    public function validateFingerprintHash(string $fingerprint, string $hash): void
    {
        if ($hash !== $this->hashFingerprint($fingerprint)) {
            throw new FingerprintHashNotValidException();
        }
    }
}
