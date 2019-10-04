<?php declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\GraphQl\Service;

use OxidEsales\GraphQl\Exception\NoSignatureKeyException;
use OxidEsales\EshopCommunity\Core\Registry;

/**
 * Class KeyRegistry
 *
 * The current implementation stores the signature key in
 * the config table. This should be changed eventually.
 *
 * @package OxidEsales\GraphQl\Service
 */
class KeyRegistry implements KeyRegistryInterface
{

    /*
    public function createSignatureKey()
    {
        $this->createTableIfNecessary();
        try {
            $this->getSignatureKey();
        } catch (NoSignatureKeyException $e) {
            $key = base64_encode(openssl_random_pseudo_bytes(64));
            $this->queryBuilderFactory
                ->create()
                ->insert($this->tableName)
                ->values([$this->columnName => '?'])
                ->setParameter(0, $key)
                ->execute();
        }
    }
     */

    /**
     * @throws NoSignatureKeyException
     */
    public function getSignatureKey(): string
    {
        $config = Registry::getConfig();
        $signature = $config->getConfigParam('sJsonWebTokenSignature');
        if (!is_string($signature) || !strlen($signature) >= 64) {
            throw new NoSignatureKeyException();
        }
        return $signature;
    }
}
