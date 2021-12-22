<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Infrastructure\Model;

use OxidEsales\Eshop\Core\Model\BaseModel as EshopModelBase;

class Token extends EshopModelBase
{
    /**
     * Name of current class.
     *
     * @var string
     */
    protected $_sClassName = 'oegraphqltoken';

    /**
     * Core database table name. $sCoreTable could be only original data table name and not view name.
     *
     * @var string
     */
    protected $_sCoreTable = 'oegraphqltoken';
}
