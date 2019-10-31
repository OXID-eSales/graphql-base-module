<?php declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

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

    public const signatureKeyName = 'sJsonWebTokenSignature';

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
        $signature = $this->legacyService->getConfigParam(static::signatureKeyName);
        if (!is_string($signature) || strlen($signature) < 64) {
            throw new NoSignatureKeyException();
        }
        return $signature;
    }
}
