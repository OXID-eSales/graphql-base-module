<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\GraphQl\Core;

use OxidEsales\EshopCommunity\Internal\Application\ContainerFactory;
use OxidEsales\GraphQl\Exception\NoAuthHeaderException;
use OxidEsales\GraphQl\Exception\NoSignatureKeyException;
use OxidEsales\GraphQl\Service\KeyRegistryInterface;
use OxidEsales\GraphQl\Service\TokenServiceInterface;

class GraphQlConfig extends GraphQlConfig_parent
{

    protected function calculateActiveShopId()
    {
        $container = ContainerFactory::getInstance()->getContainer();
        /** @var \OxidEsales\GraphQl\Service\TokenServiceInterface $tokenService */
        $tokenService = $container->get(TokenServiceInterface::class);
        /** @var KeyRegistryInterface $keyRegistry */
        $keyRegistry = $container->get(KeyRegistryInterface::class);
        try {
            $token = $tokenService->getToken($keyRegistry->getSignatureKey());
            return $token->getShopid();
        }
        catch (NoAuthHeaderException $e) {
            // No graph QL request
        } catch (NoSignatureKeyException $e) {
            // Not yet initialized
        }
        return parent::calculateActiveShopId();
    }

}