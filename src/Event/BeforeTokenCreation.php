<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Event;

use Lcobucci\JWT\Builder;
use OxidEsales\GraphQL\Base\DataType\UserInterface;
use Symfony\Contracts\EventDispatcher\Event;

class BeforeTokenCreation extends Event
{
    public function __construct(
        private readonly Builder $builder,
        private readonly UserInterface $user
    ) {
    }

    public function getBuilder(): Builder
    {
        return $this->builder;
    }

    public function getUser(): UserInterface
    {
        return $this->user;
    }
}
