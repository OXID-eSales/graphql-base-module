<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\GraphQl\Component\Widget;

use OxidEsales\EshopCommunity\Internal\Application\ContainerFactory;
use OxidEsales\GraphQl\Framework\GraphQlQueryHandlerInterface;

/**
 * Class GraphQL
 *
 * @package OxidEsales\GraphQl\Component\Widget
 */
class GraphQL extends \OxidEsales\Eshop\Application\Component\Widget\WidgetController
{
    /**
     * Init function
     *
     * @return void
     */
    public function init()
    {

        /** @var GraphQlQueryHandlerInterface $queryHandler */
        $queryHandler = ContainerFactory::getInstance()->getContainer()->get(GraphQlQueryHandlerInterface::class);

        $queryHandler->executeGraphQlQuery();

    }

}