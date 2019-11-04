<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Exception;

class ErrorCategories
{
    public const PERMISSIONERRORS   = 'permissionerror';
    public const TOKENERRORS        = 'tokenerror';
    public const CONFIGURATIONERROR = 'configurationerror';
    public const REQUESTERROR       = 'requesterror';
}
