<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Tests\Unit\DataType\Error;

use InvalidArgumentException;
use OxidEsales\GraphQL\Base\DataType\Error\IdAlreadyExistsError;
use OxidEsales\GraphQL\Base\DataType\Error\ErrorInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(IdAlreadyExistsError::class)]
final class IdAlreadyExistsErrorTest extends TestCase
{
    #[Test]
    public function idAlreadyExistsError(): void
    {
        $code = 'id_already_exists.code';
        $identifier = uniqid();
        $sut = new class ($code, $identifier) extends IdAlreadyExistsError {
            protected function messages(): array
            {
                return ['id_already_exists.code' => 'The error message.'];
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
        $code = uniqid();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('There is no message for the error code "' . $code . '".');

        new IdAlreadyExistsError($code, uniqid());
    }
}
