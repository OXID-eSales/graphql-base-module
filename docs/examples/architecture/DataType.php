<?php

declare(strict_types=1);

namespace Full\Qualified\Namespace\Context\DataType;

use DateTimeImmutable;
use DateTimeInterface;
use Exception;
use OxidEsales\Eshop\Application\Model\Category as CategoryModel;
use OxidEsales\GraphQL\Storefront\Shared\DataType\DataType;
use TheCodingMachine\GraphQLite\Annotations\Field;
use TheCodingMachine\GraphQLite\Annotations\Type;
use TheCodingMachine\GraphQLite\Types\ID;

/**
 * @Type()
 */
final class Category implements DataType
{
    /** @var CategoryModel */
    private $category;

    public function __construct(CategoryModel $category)
    {
        $this->category = $category;
    }

    public function getEshopModel(): CategoryModel
    {
        return $this->category;
    }

    /**
     * @Field()
     */
    public function getId(): ID
    {
        return new ID(
            $this->category->getId()
        );
    }

    /**
     * @Field()
     */
    public function isActive(?DateTimeInterface $now = null): bool
    {
        $active = (bool) $this->category->getFieldData('oxactive');

        if ($active) {
            return true;
        }

        $from = new DateTimeImmutable(
            (string) $this->category->getFieldData('oxactivefrom')
        );
        $to = new DateTimeImmutable(
            (string) $this->category->getFieldData('oxactiveto')
        );
        $now = $now ?? new DateTimeImmutable('now');

        if ($from <= $now && $to >= $now) {
            return true;
        }

        return false;
    }
}
