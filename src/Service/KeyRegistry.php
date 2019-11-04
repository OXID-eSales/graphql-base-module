<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Service;

use OxidEsales\GraphQL\Exception\NoSignatureKeyException;

/**
 * Class KeyRegistry
 *
 * The current implementation stores the signature key in
 * the config table. This should be changed eventually.
 *
 * @package OxidEsales\GraphQL\Service
 */
class KeyRegistry implements KeyRegistryInterface
{
    /** @var LegacyServiceInterface */
    private $legacyService = null;

    public const SIGNATUREKEYNAME = 'sJsonWebTokenSignature';

    public function __construct(
        LegacyServiceInterface $legacyService
    ) {
        $this->legacyService = $legacyService;
    }

    public function generateSignatureKey(): string
    {
        return \bin2hex(\random_bytes(64));
    }

    /**
     * @throws NoSignatureKeyException
     */
    public function getSignatureKey(): string
    {
        // TODO: legacy wrapper
        $signature = $this->legacyService->getConfigParam(static::SIGNATUREKEYNAME);
        if (!is_string($signature) || strlen($signature) < 64) {
            throw new NoSignatureKeyException();
        }
        return $signature;
    }
}
