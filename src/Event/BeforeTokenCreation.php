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

    /**
     * Handles Builder immutability internally (lcobucci/jwt v5+).
     *
     * @param non-empty-string $name
     */
    public function withClaim(string $name, mixed $value): self
    {
        $this->builder = $this->builder->withClaim($name, $value);
        return $this;
    }

    /**
     * Handles Builder immutability internally (lcobucci/jwt v5+).
     *
     * @param non-empty-string $name
     */
    public function withHeader(string $name, mixed $value): self
    {
        $this->builder = $this->builder->withHeader($name, $value);
        return $this;
    }

    /**
     * @internal For internal use only. Prefer withClaim()/withHeader() in event subscribers.
     */
    public function getBuilder(): Builder
    {
        return $this->builder;
    }

    public function getUser(): UserInterface
    {
        return $this->user;
    }
}
