<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Tests\Unit\Exception;

use OxidEsales\GraphQL\Base\Exception\ErrorCategories;
use OxidEsales\GraphQL\Base\Exception\NotFound;
use PHPUnit\Framework\TestCase;

final class NotFoundTest extends TestCase
{
    public function testExceptionCategory(): void
    {
        $notFountException = NotFound::notFound();

        $this->assertSame(ErrorCategories::REQUESTERROR, $notFountException->getCategory());
    }

    public function testIsClientSafe(): void
    {
        $notFountException = NotFound::notFound();

        $this->assertTrue($notFountException->isClientSafe());
    }

    public function testNotFound(): void
    {
        $notFountException = NotFound::notFound();

        $this->assertSame('Queried data was not found', $notFountException->getMessage());
    }
}
