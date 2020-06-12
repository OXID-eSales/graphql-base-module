<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Framework;

class UserData
{
    /** @var string */
    private $userId;

    /** @var string */
    private $userGroup;

    public function __construct(string $userId, string $userGroup)
    {
        $this->userId    = $userId;
        $this->userGroup = $userGroup;
    }

    public function getUserId(): string
    {
        return $this->userId;
    }

    public function getUserGroup(): string
    {
        return $this->userGroup;
    }
}
