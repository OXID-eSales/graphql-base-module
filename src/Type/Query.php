<?php declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\GraphQl\Type;

/**
 * Class Query
 *
 * @package OxidEsales\GraphQl\Type
 */
class Query extends RootGraphQlType
{

    /**
     * Query constructor.
     *
     * @param array $fields
     * @param array $fieldHandlers
     */
    public function __construct(array $fields, array $fieldHandlers)
    {
        $this->name = 'query';
        $this->description = 'The root type for queries';
        parent::__construct($fields, $fieldHandlers);
    }
}
