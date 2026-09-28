<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Tests\Unit\DataType\Error;

use InvalidArgumentException;
use OxidEsales\GraphQL\Base\DataType\Error\AbstractError;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(AbstractError::class)]
final class AbstractErrorTest extends TestCase
{
    #[Test]
    public function unknownCodeThrowsException(): void
    {
        $code = uniqid();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('There is no message for the error code "' . $code . '".');

        new class ($code) extends AbstractError {
            protected function messages(): array
            {
                return ['oegqlb.known_code' => 'The known message.'];
            }
        };
    }
}
