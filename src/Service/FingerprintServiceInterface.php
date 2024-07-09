<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\GraphQL\Base\Service;

use OxidEsales\GraphQL\Base\Exception\FingerprintMissingException;
use OxidEsales\GraphQL\Base\Exception\FingerprintValidationException;

interface FingerprintServiceInterface
{
    public const COOKIE_KEY = 'fingerprint';
    public const TOKEN_KEY = 'fingerprintHash';

    public function getFingerprint(): string;

    public function hashFingerprint(string $fingerprint): string;

    /**
     * @throws FingerprintMissingException
     * @throws FingerprintValidationException
     */
    public function validateFingerprintHashToCookie(string $hashedFingerprint): void;
}
