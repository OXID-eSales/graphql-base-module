<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Component\Widget;

use OxidEsales\Eshop\Application\Component\Widget\WidgetController;
use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\GraphQL\Base\Framework\GraphQLQueryHandler;

/**
 * Class GraphQL
 *
 * Implements the GraphQL widget for the OXID eShop to make all
 * of this callable via a SEO Url or via widget.php?cl=graphql
 */
class GraphQL extends WidgetController
{
    /**
     * Init function
     */
    public function init(): void
    {
        // handle preflight CORS request
        // https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS#Preflighted_requests
        if (
            strtoupper($_SERVER['REQUEST_METHOD']) === 'OPTIONS' &&
            isset(
                $_SERVER['HTTP_ORIGIN'],
                $_SERVER['HTTP_ACCESS-CONTROL-REQUEST-METHOD'],
                $_SERVER['HTTP_ACCESS-CONTROL-REQUEST-HEADERS']
            )
        ) {
            $header = oxNew(\OxidEsales\Eshop\Core\Header::class);
            $header->setHeader('HTTP/1.1 204 No Content');
            $header->setHeader('Access-Control-Allow-Methods: POST, GET, OPTIONS');
            $header->setHeader('Access-Control-Allow-Headers: Content-Type, Authorization');
            $header->setHeader('Access-Control-Allow-Origin: *');
            $header->sendHeader();

            exit;
        }
        ContainerFactory::getInstance()
            ->getContainer()
            ->get(GraphQLQueryHandler::class)
            ->executeGraphQLQuery();
    }
}
