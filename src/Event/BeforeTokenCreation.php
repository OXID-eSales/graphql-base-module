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
        private Builder $builder,
        private readonly UserInterface $user
    ) {
    }

    public function getBuilder(): Builder
    {
        return $this->builder;
    }

    /**
     * lcobucci/jwt v5 made Builder immutable — `withClaim()`/`withHeader()` return
     * a new instance. Subscribers that add claims must push the updated builder
     * back onto the event via this setter.
     */
    public function setBuilder(Builder $builder): void
    {
        $this->builder = $builder;
    }

    public function getUser(): UserInterface
    {
        return $this->user;
    }
}
