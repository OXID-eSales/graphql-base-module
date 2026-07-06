<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\DataType\Payload;

use OxidEsales\GraphQL\Base\DataType\Error\AbstractPayload;
use OxidEsales\GraphQL\Base\DataType\Error\ErrorInterface;
use OxidEsales\GraphQL\Base\DataType\LoginInterface;
use TheCodingMachine\GraphQLite\Annotations\Field;
use TheCodingMachine\GraphQLite\Annotations\Type;

/**
 * @Type()
 */
final class LoginPayload extends AbstractPayload implements LoginPayloadInterface
{
    /**
     * @param ErrorInterface[] $userErrors
     */
    public function __construct(
        private readonly ?LoginInterface $login,
        array $userErrors = []
    ) {
        parent::__construct($userErrors);
    }

    /**
     * @Field()
     */
    public function login(): ?LoginInterface
    {
        return $this->login;
    }
}
