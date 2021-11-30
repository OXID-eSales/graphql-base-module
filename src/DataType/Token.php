<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\DataType;

use DateTimeInterface;
use OxidEsales\GraphQL\Base\Infrastructure\Model\Token as GraphQLTokenModel;
use TheCodingMachine\GraphQLite\Annotations\Field;
use TheCodingMachine\GraphQLite\Annotations\Type;
use TheCodingMachine\GraphQLite\Types\ID;

/**
 * @Type()
 */
final class Token implements ShopModelAwareInterface
{
    /** @var GraphQLTokenModel */
    private $tokenModel;

    public function __construct(GraphQLTokenModel $tokenModel)
    {
        $this->tokenModel = $tokenModel;
    }

    public function getEshopModel(): GraphQLTokenModel
    {
        return $this->tokenModel;
    }

    /**
     * @Field()
     */
    public function id(): ID
    {
        return new ID((string) $this->tokenModel->getId());
    }

    /**
     * @Field()
     */
    public function token(): String
    {
        return (string) $this->tokenModel->getRawFieldData('token');
    }

    /**
     * @Field()
     */
    public function createdAt(): ?DateTimeInterface
    {
        return DateTimeImmutableFactory::fromString(
            (string) $this->tokenModel->getRawFieldData('issued_at')
        );
    }

    /**
     * @Field()
     */
    public function expiresAt(): ?DateTimeInterface
    {
        return DateTimeImmutableFactory::fromString(
            (string) $this->tokenModel->getRawFieldData('expires_at')
        );
    }

    /**
     * @Field()
     */
    public function userAgent(): String
    {
        return $this->tokenModel->getRawFieldData('useragent');
    }

    public function customerId(): ID
    {
        return new ID((string) $this->tokenModel->getRawFieldData('oxuserid'));
    }

    public function shopId(): ID
    {
        return new ID((string) $this->tokenModel->getRawFieldData('oxshopid'));
    }

    public static function getModelClass(): string
    {
        return GraphQLTokenModel::class;
    }
}
