<?php declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\GraphQl\Service;

use OxidEsales\Eshop\Core\Registry;

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

    const SIGNATUREKEY_KEY = 'strAuthTokenSignatureKey';

    public function createSignatureKey()
    {
        $config = Registry::getConfig();
        // Never overwrite the signature key because it will be
        // impossible to decode the existing tokens
        if ($config->getConfigParam($this::SIGNATUREKEY_KEY) === null) {
            $key = base64_encode(openssl_random_pseudo_bytes(64));
            $config = Registry::getConfig();
            $config->setConfigParam($this::SIGNATUREKEY_KEY, $key);
            $config->saveShopConfVar('str', $this::SIGNATUREKEY_KEY, $key);
        }
    }


    public function getSignatureKey()
    {
        $config = Registry::getConfig();
        return $config->getConfigParam('strAuthTokenSignatureKey');

    }

}
