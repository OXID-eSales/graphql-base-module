<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Tests\Unit\DataType\Error;

use InvalidArgumentException;
use OxidEsales\GraphQL\Base\DataType\Error\ErrorInterface;
use OxidEsales\GraphQL\Base\DataType\Error\NotDeletedError;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(NotDeletedError::class)]
final class NotDeletedErrorTest extends TestCase
{
    #[Test]
    public function notDeletedError(): void
    {
        $code = 'not_deleted.code';
        $identifier = uniqid();
        $sut = new class ($code, $identifier) extends NotDeletedError {
            protected function messages(): array
            {
                return ['not_deleted.code' => 'The error message.'];
            }
        };

        $this->assertInstanceOf(ErrorInterface::class, $sut);
        $this->assertSame($code, $sut->code());
        $this->assertSame('The error message.', $sut->message());
        $this->assertSame($identifier, $sut->identifier());
    }

    #[Test]
    public function unknownCodeThrowsException(): void
    {
        $notExistingCode = uniqid();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The error-code "' . $notExistingCode . '" is unknown.');

        new NotDeletedError($notExistingCode, uniqid());
    }
}
