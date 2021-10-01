<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\DataType;

use OxidEsales\Eshop\Application\Model\User as EshopUserModel;
use OxidEsales\GraphQL\Base\Framework\UserDataInterface;
use OxidEsales\GraphQL\Base\Infrastructure\ShopModelAwareInterface as ShopModelAwareInterfaceAlias;

final class User implements ShopModelAwareInterfaceAlias, UserDataInterface
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

    public function isAnonymous(): bool
    {
        return $this->isAnonymous;
    }
}
