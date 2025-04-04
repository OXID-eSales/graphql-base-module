<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Api\Controller;

use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Dao\ModuleConfigurationDaoInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;

readonly class OxapiController
{
    public function __construct(private ModuleConfigurationDaoInterface $moduleConfigurationDao)
    {
    }

    #[Route('/oxapi/{name}/{shp}/', requirements: ['shp' => Requirement::DIGITS], methods: ['GET'])]
    public function foo(string $name, int $shp): Response
    {
        Registry::getConfig()->saveShopConfVar('string', 'testControllers', 'hello');
        return new JsonResponse(
            [
                'name' => $name,
                'shp' => $shp,
                'configParameter' => Registry::getConfig()->getShopConfVar('testControllers'),
            ]
        );
    }
}
