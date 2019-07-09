<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\GraphQl\Tests\Acceptance\Provider;

use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use OxidEsales\EshopCommunity\Core\Registry;
use OxidEsales\GraphQl\Type\Provider\QueryProviderInterface;

class ConfigTestQueryProvider implements QueryProviderInterface
{
    public function getQueries()
    {
        return [
            'configtest'  => [
                'type'        => Type::string(),
                'description' => 'Returns a test parameter from the database.',
                'args'        => [],
            ]
        ];
    }

    public function getQueryResolvers()
    {
        return [
            'configtest' => function ($value, $args, $context, ResolveInfo $info) {
                $config = Registry::getConfig();
                // This value should depend on the shop id in the token
                $param = $config->getConfigParam('sTestParam');
                return $param;
            }
        ];
    }


}