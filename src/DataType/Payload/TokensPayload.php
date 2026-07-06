<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\DataType\Payload;

use OxidEsales\GraphQL\Base\DataType\Error\AbstractPayload;
use OxidEsales\GraphQL\Base\DataType\Error\ErrorInterface;
use OxidEsales\GraphQL\Base\DataType\Token;
use TheCodingMachine\GraphQLite\Annotations\Field;
use TheCodingMachine\GraphQLite\Annotations\Type;

/**
 * @Type()
 */
final class TokensPayload extends AbstractPayload implements TokensPayloadInterface
{
    /**
     * @param Token[]|null $tokens
     * @param ErrorInterface[] $userErrors
     */
    public function __construct(
        private readonly ?array $tokens,
        array $userErrors = []
    ) {
        parent::__construct($userErrors);
    }

    /**
     * @Field()
     * @return Token[]|null
     */
    public function tokens(): ?array
    {
        return $this->tokens;
    }
}
