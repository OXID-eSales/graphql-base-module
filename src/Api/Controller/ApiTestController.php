<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Api\Controller;

use OxidEsales\Eshop\Application\Model\Article;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Dao\ModuleConfigurationDaoInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;

readonly class ApiTestController
{
    public function __construct(private ModuleConfigurationDaoInterface $moduleConfigurationDao)
    {
    }

    #[Route('/api/{name}/{id}/', requirements: ['id' => Requirement::DIGITS], methods: ['GET'])]
    public function fly(string $name, int $id): Response
    {
        Registry::getConfig()->saveShopConfVar('string', 'testControllers', 'hello test');
        return new JsonResponse(
            [
                'name' => $name,
                'id' => $id,
                'configParameter' => Registry::getConfig()->getShopConfVar('testControllers'),
            ]
        );
    }
}
