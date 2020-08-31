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
     * @deprecated since v3.1.3 (2020-06-26);
     *
     * @var string
     */
    private $userGroup;

    /**
     * @var array<string,string>
     */
    private $userGroupIds;

    /**
     * @param array<string,string> $userGroupIds
     */
    public function __construct(string $userId, string $userGroup, array $userGroupIds)
    {
        $this->userId       = $userId;
        $this->userGroup    = $userGroup;
        $this->userGroupIds = $userGroupIds;
    }

    public function getUserId(): string
    {
        return $this->userId;
    }

    /**
     * @deprecated since v3.1.3 (2020-06-26);
     */
    public function getUserGroup(): string
    {
        return $this->userGroup;
    }

    /**
     * @return array<string,string>
     */
    public function getUserGroupIds(): array
    {
        return $this->userGroupIds;
    }
}
