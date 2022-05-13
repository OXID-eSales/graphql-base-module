<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\DataType;

use OxidEsales\Eshop\Application\Model\User as EshopUserModel;
use TheCodingMachine\GraphQLite\Annotations\Field;
use TheCodingMachine\GraphQLite\Annotations\Type;
use TheCodingMachine\GraphQLite\Types\ID;

/**
 * @Type()
 */
final class User implements ShopModelAwareInterface
{
    /** @var EshopUserModel */
    private $userModel;

    private bool $isAnonymous;

    public function __construct(EshopUserModel $userModel, bool $isAnonymous = false)
    {
        $this->userModel = $userModel;
        $this->isAnonymous = $isAnonymous;
    }

    public function getEshopModel(): EshopUserModel
    {
        return $this->userModel;
    }

    /**
     * @Field()
     */
    public function id(): ID
    {
        return new ID((string)$this->userModel->getId());
    }

    /**
     * @Field()
     */
    public function email(): string
    {
        return (string)$this->userModel->getRawFieldData('oxusername');
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
