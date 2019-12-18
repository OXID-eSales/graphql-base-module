<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\GraphQL\Base\DataObject;

use Doctrine\DBAL\Query\QueryBuilder;

interface FilterInterface
{
    public function addToQuery(QueryBuilder $builder, string $field): void;
}
