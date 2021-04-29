<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Exception;

class InvalidToken extends Error
{
    protected const INVALID_TOKEN_MESSAGE = 'The token is invalid';

    protected const UNABLE_TO_PARSE_MESSAGE = 'Unable to parse token';

    protected const USER_BLOCKED_MESSAGE = 'User is blocked';

    public function getCategory(): string
    {
        return ErrorCategories::PERMISSIONERRORS;
    }

    public static function invalidToken(): self
    {
        return new self(self::INVALID_TOKEN_MESSAGE);
    }

    public static function unableToParse(): self
    {
        return new self(self::UNABLE_TO_PARSE_MESSAGE);
    }

    public static function userBlocked(): self
    {
        return new self(self::USER_BLOCKED_MESSAGE);
    }
}
