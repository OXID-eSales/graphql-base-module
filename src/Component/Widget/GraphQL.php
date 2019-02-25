<?php
/**
 * This Software is the property of OXID eSales and is protected
 * by copyright law - it is NOT Freeware.
 *
 * Any unauthorized use of this software without a valid license key
 * is a violation of the license agreement and will be prosecuted by
 * civil and criminal law.
 *
 * @category    module
 *
 * @package     GraphQL
 * @link        http://www.oxid-esales.com
 * @copyright   (C) OXID eSales AG 2003-2018
 * @version     OXID eSales GraphQL
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