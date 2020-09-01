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

    /**
     * @var array<string,string>
     */
    private $userGroupIds;

    /**
     * @param array<string,string> $userGroupIds
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
     * @return array<string,string>
     */
    public function getUserGroupIds(): array
    {
        return $this->userGroupIds;
    }
}
