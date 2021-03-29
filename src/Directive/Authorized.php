<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Directive;

use GraphQL\Language\DirectiveLocation;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Definition\Directive;
use GraphQL\Type\Definition\FieldArgument;

class Authorized extends Directive
{
    public function __construct()
    {
        parent::__construct([
            'name' => 'authorized',
            'description' => 'In order to use this resource you need correct authorization rights!',
            'locations' => [DirectiveLocation::FIELD_DEFINITION],
            'args' => [
                new FieldArgument([
                    'name' => 'warning',
                    'type' => Type::string(),
                    'description' => 'Warns the API consumer that this resource needs specific access rights!',
                    'defaultValue' => 'Authorisation is required!'
                ])
            ]
        ]);
    }
}
