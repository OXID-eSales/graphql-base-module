<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\GraphQl\Core;

use OxidEsales\EshopCommunity\Internal\Application\ContainerFactory;
use OxidEsales\GraphQl\Exception\NoAuthHeaderException;
use OxidEsales\GraphQl\Service\KeyRegistry;
use OxidEsales\GraphQl\Service\TokenServiceInterface;

class GraphQlConfig extends GraphQlConfig_parent
{

    protected function calculateActiveShopId()
    {
        $container = ContainerFactory::getInstance()->getContainer();
        /** @var \OxidEsales\GraphQl\Service\TokenServiceInterface $tokenService */
        $tokenService = $container->get(TokenServiceInterface::class);
        try {
            $token = $tokenService->getToken($this->getSignatureKey());
            return $token->getShopid();
        }
        catch (NoAuthHeaderException $e) {
            // No graph QL request
        }
        return parent::calculateActiveShopId();
    }

    private function getSignatureKey()
    {
        $this->_loadVarsFromDb(1);
        if (array_key_exists(KeyRegistry::SIGNATUREKEY_KEY, $this->_aConfigParams)) {
            return $this->_aConfigParams[KeyRegistry::SIGNATUREKEY_KEY];
        }
        throw new NoAuthHeaderException();

    }


}