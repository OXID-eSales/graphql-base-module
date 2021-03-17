<?php

declare(strict_types=1);

namespace Full\Qualified\Namespace\Shared\Service;

use OxidEsales\GraphQL\Base\Framework\PermissionProviderInterface;

final class PermissionProvider implements PermissionProviderInterface
{
    public function getPermissions(): array
    {
        return [
            'oxidcustomer' => [
                '3RDPARTY_EXPRESS_APPROVAL'
            ],
            'oxidnotyetordered' => [
                '3RDPARTY_EXPRESS_APPROVAL'
            ],
            'oxidanonymous' => [
                'CREATE_BASKET',
                'ADD_PRODUCT_TO_BASKET',
                'REMOVE_BASKET_PRODUCT',
                'ADD_VOUCHER',
                'REMOVE_VOUCHER',
                'PLACE_ORDER',
                '3RDPARTY_EXPRESS_APPROVAL'
            ],
        ];
    }
}
