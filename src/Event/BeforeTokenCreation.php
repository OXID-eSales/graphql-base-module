<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Event;

use Lcobucci\JWT\Builder;
use OxidEsales\GraphQL\Base\Framework\UserDataInterface;
use Symfony\Component\EventDispatcher\Event;

class BeforeTokenCreation extends Event
{
    public const NAME = self::class;

    /** @var Builder */
    private $builder;

    /** @var UserDataInterface */
    private $userData;

    public function __construct(
        Builder $builder,
        UserDataInterface $userData
    ) {
        $this->builder  = $builder;
        $this->userData = $userData;
    }

    public function getBuilder(): Builder
    {
        return $this->builder;
    }

    public function getUserData(): UserDataInterface
    {
        return $this->userData;
    }
}
