<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\DataType;

use OxidEsales\GraphQL\Base\DataType\Error\AbstractPayload;
use OxidEsales\GraphQL\Base\DataType\Error\ErrorInterface;
use TheCodingMachine\GraphQLite\Annotations\Field;
use TheCodingMachine\GraphQLite\Annotations\Type;

/**
 * @Type()
 */
final class CreationPayload extends AbstractPayload implements CreationPayloadInterface
{
    /**
     * @param ErrorInterface[] $userErrors
     */
    public function __construct(
        private readonly ?bool $success,
        array $userErrors = []
    ) {
        parent::__construct($userErrors);
    }

    /**
     * @Field()
     */
    public function success(): ?bool
    {
        return $this->success;
    }
}
