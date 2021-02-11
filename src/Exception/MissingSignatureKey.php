<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Exception;

class MissingSignatureKey extends Error
{
    protected const WRONG_TYPE_MESSAGE = 'Signature key needs to be a string';

    protected const WRONG_SIZE_MESSAGE = 'Signature key is too short';

    public static function wrongType(): self
    {
        return new self(self::WRONG_TYPE_MESSAGE);
    }

    public static function wrongSize(): self
    {
        return new self(self::WRONG_SIZE_MESSAGE);
    }
}
