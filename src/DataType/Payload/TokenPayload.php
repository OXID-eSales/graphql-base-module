<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\DataType\Payload;

use OxidEsales\GraphQL\Base\DataType\Error\AbstractPayload;
use OxidEsales\GraphQL\Base\DataType\Error\ErrorInterface;
use TheCodingMachine\GraphQLite\Annotations\Field;
use TheCodingMachine\GraphQLite\Annotations\Type;

#[Type]
final class TokenPayload extends AbstractPayload implements TokenPayloadInterface
{
    /**
     * @param ErrorInterface[] $userErrors
     */
    public function __construct(
        private readonly ?string $token,
        array $userErrors = []
    ) {
        parent::__construct($userErrors);
    }

    #[Field]
    public function token(): ?string
    {
        return $this->token;
    }
}
