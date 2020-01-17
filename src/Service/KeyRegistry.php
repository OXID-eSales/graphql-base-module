<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Service;

use OxidEsales\GraphQL\Base\Exception\MissingSignatureKey;

use function bin2hex;
use function is_string;
use function random_bytes;
use function strlen;

/**
 * Class KeyRegistry
 *
 * The current implementation stores the signature key in
 * the config table. This should be changed eventually.
 *
 * @package OxidEsales\GraphQL\Base\Service
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
        return bin2hex(random_bytes(64));
    }

    /**
     * @throws MissingSignatureKey
     */
    public function getSignatureKey(): string
    {
        // TODO: legacy wrapper
        $signature = $this->legacyService->getConfigParam(static::SIGNATUREKEYNAME);
        if (!is_string($signature) || strlen($signature) < 64) {
            throw new MissingSignatureKey();
        }
        return $signature;
    }
}
