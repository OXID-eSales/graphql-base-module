<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\DataType;

use OxidEsales\Eshop\Application\Model\User as EshopUserModel;
use TheCodingMachine\GraphQLite\Annotations\Type;

/**
 * @Type()
 */
final class User implements DataTypeInterface
{
    /** @var EshopUserModel */
    private $userModel;

    private bool $isAnonymous;

    public function __construct(EshopUserModel $userModel, bool $isAnonymous = false)
    {
        $this->userModel   = $userModel;
        $this->isAnonymous = $isAnonymous;
    }

    public function getEshopModel(): EshopUserModel
    {
        return $this->userModel;
    }

    public function getUserId(): ?string
    {
        return $this->userModel->getId();
    }

    public function getUserName(): string
    {
        return (string) $this->userModel->getFieldData('oxusername');
    }

    public function isAnonymous(): bool
    {
        return $this->isAnonymous;
    }

    public static function getModelClass(): string
    {
        return EshopUserModel::class;
    }
}
