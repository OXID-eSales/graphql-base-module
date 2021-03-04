<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Framework;

class UserData implements UserDataInterface
{
    /** @var string */
    private $userId;

    /**
     * @var string[]
     */
    private $userGroupIds;

    /**
     * @param string[] $userGroupIds
     */
    public function __construct(string $userId, array $userGroupIds)
    {
        $this->userId       = $userId;
        $this->userGroupIds = $userGroupIds;
    }

    public function getUserId(): string
    {
        return $this->userId;
    }

    /**
     * @return string[]
     */
    public function getUserGroupIds(): array
    {
        return $this->userGroupIds;
    }
}
