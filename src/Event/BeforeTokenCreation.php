<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Event;

use Lcobucci\JWT\Builder;
use OxidEsales\GraphQL\Base\DataType\User;
use Symfony\Component\EventDispatcher\Event;

class BeforeTokenCreation extends Event
{
    public const NAME = self::class;

    /** @var Builder */
    private $builder;

    /** @var User */
    private $user;

    public function __construct(
        Builder $builder,
        User $userData
    ) {
        $this->builder = $builder;
        $this->user = $userData;
    }

    public function getBuilder(): Builder
    {
        return $this->builder;
    }

    public function getUser(): User
    {
        return $this->user;
    }
}
