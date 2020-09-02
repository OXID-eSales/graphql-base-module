<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Tests\Unit\DataType;

use DateTimeInterface;
use OxidEsales\GraphQL\Base\DataType\DateTimeImmutableFactory;

class DateTimeImmutableFactoryTest extends DataTypeTestCase
{
    public function testGetNullOnIncorrectString(): void
    {
        $this->assertNull(DateTimeImmutableFactory::fromString(''));
        $this->assertNull(DateTimeImmutableFactory::fromString('-'));
        $this->assertNull(DateTimeImmutableFactory::fromString('0000-00-00'));
        $this->assertNull(DateTimeImmutableFactory::fromString('0000-00-00 00:00:00'));
    }

    public function testGetNullOnIncorrectInt(): void
    {
        $this->assertNull(DateTimeImmutableFactory::fromTimeStamp(0));
        $this->assertNull(DateTimeImmutableFactory::fromTimeStamp(-128));
    }

    public function testGetDateTimeInterfaceFromString(): void
    {
        $this->assertInstanceOf(
            DateTimeInterface::class,
            DateTimeImmutableFactory::fromString('2020-09-01 15:14:35')
        );
    }

    public function testGetDateTimeInterfaceFromTimeStamp(): void
    {
        $this->assertInstanceOf(
            DateTimeInterface::class,
            DateTimeImmutableFactory::fromTimeStamp(1598966135)
        );
    }
}
