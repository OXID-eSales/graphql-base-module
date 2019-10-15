<?php declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\GraphQL\Exception;

class ErrorCategories
{
    const PERMISSIONERRORS = 'permissionerror';
    const TOKENERRORS = 'tokenerror';
    const CONFIGURATIONERROR = 'configurationerror';
    const REQUESTERROR = 'requesterror';
}
