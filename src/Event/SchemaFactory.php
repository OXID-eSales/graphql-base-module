<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Event;

use Symfony\Contracts\EventDispatcher\Event;
use TheCodingMachine\GraphQLite\SchemaFactory as GraphQLiteSchemaFactory;

class SchemaFactory extends Event
{
    public const NAME = self::class;

    /** @var GraphQLiteSchemaFactory */
    private $factory;

    public function __construct(
        GraphQLiteSchemaFactory $factory
    ) {
        $this->factory = $factory;
    }

    public function getSchemaFactory(): GraphQLiteSchemaFactory
    {
        return $this->factory;
    }
}
