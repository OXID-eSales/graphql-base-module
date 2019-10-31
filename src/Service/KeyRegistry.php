<?php declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\GraphQL\Service;

use OxidEsales\EshopCommunity\Core\Registry;
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
    public const signatureKeyName = 'sJsonWebTokenSignature';

    public function generateSignatureKey(): string
    {
        return bin2hex(openssl_random_pseudo_bytes(64));
    }

    /**
     * @throws NoSignatureKeyException
     */
    public function getSignatureKey(): string
    {
        // TODO: legacy wrapper
        $config = Registry::getConfig();
        $signature = $config->getConfigParam(self::signatureKeyName);
        if (!is_string($signature) || strlen($signature) < 64) {
            throw new NoSignatureKeyException();
        }
        return $signature;
    }
}
