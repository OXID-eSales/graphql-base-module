<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\DataType;

use OxidEsales\GraphQL\Base\DataType\Filter\BoolFilter;
use OxidEsales\GraphQL\Base\DataType\Filter\DateFilter;
use OxidEsales\GraphQL\Base\DataType\Filter\FilterListInterface;
use OxidEsales\GraphQL\Base\DataType\Filter\IDFilter;
use TheCodingMachine\GraphQLite\Annotations\Factory;

final class AccessTokenFilterList implements FilterListInterface
{
    /** @var ?IDFilter */
    private $customerId;

    /** @var ?IDFilter */
    private $shopId;

    /** @var ?DateFilter */
    private $expiresAt;

    public function __construct(
        ?IDFilter $customerId = null,
        ?IDFilter $shopId = null,
        ?DateFilter $expiresAt = null
    ) {
        $this->customerId = $customerId;
        $this->shopId = $shopId;
        $this->expiresAt = $expiresAt;
    }

    public function withActiveFilter(?BoolFilter $active): self
    {
        return $this;
    }

    public function getActive(): ?BoolFilter
    {
        return null;
    }

    public function getUserFilter(): ?IDFilter
    {
        return $this->customerId;
    }

    public function withUserFilter(IDFilter $user): self
    {
        $filter = clone $this;
        $filter->customerId = $user;

        return $filter;
    }

    /**
     * @return array{
     *                oxuserid: ?IDFilter,
     *                oxshopid: ?IDFilter,
     *                expires_at: ?DateFilter
     *                }
     */
    public function getFilters(): array
    {
        return [
            'oxuserid' => $this->customerId,
            'oxshopid' => $this->shopId,
            'expires_at' => $this->expiresAt,
        ];
    }

    /**
     * @Factory(name="TokenFilterList",default=true)
     */
    public static function fromUserInput(
        ?IDFilter $customerId,
        ?IDFilter $shopId = null,
        ?DateFilter $expiresAt = null
    ): self {
        return new self($customerId, $shopId, $expiresAt);
    }
}
