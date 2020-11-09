<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Service;

use OxidEsales\GraphQL\Base\Exception\MissingSignatureKey;
use OxidEsales\GraphQL\Base\Infrastructure\Legacy as LegacyService;

use function bin2hex;
use function is_string;
use function random_bytes;
use function strlen;

/**
 * Class KeyRegistry
 *
 * The current implementation stores the signature key in
 * the config table. This should be changed eventually.
 */
class KeyRegistry
{
    public const SIGNATUREKEYNAME = 'sJsonWebTokenSignature';

    /** @var LegacyService */
    private $legacyService;

    public function __construct(
        LegacyService $legacyService
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
