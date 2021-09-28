<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Tests\Unit\Framework;

use DateTime;
use OxidEsales\GraphQL\Base\Framework\NullToken;
use PHPUnit\Framework\TestCase;

class NullTokenTest extends TestCase
{
    public function testNotExpired(): void
    {
        $now   = new DateTime();
        $token = new NullToken();

        $this->assertFalse(
            $token->isExpired($now),
            'Null token cannot be expired'
        );
    }

    public function testHeaders(): void
    {
        $token = new NullToken();

        $this->assertEmpty(
            $token->headers()->all(),
            'There should be no headers in null token'
        );
    }

    public function testClaims(): void
    {
        $token = new NullToken();

        $this->assertEmpty(
            $token->claims()->all(),
            'There should be no claims in null token'
        );
    }

    public function testSignature(): void
    {
        $token = new NullToken();

        $this->assertEmpty(
            $token->signature()->toString(),
            'For null token, the signature is empty string'
        );
    }

    public function testPayload(): void
    {
        $token = new NullToken();

        $this->assertSame(
            '.',
            $token->payload(),
            'Payload is empty, but still from two parts, so dot should be the result'
        );
    }

    public function testToString(): void
    {
        $token = new NullToken();

        $this->assertSame(
            '..',
            $token->toString(),
            'Its empty token, but still from three parts, so two dots should be the result'
        );
    }

    public function testIsPermittedFor(): void
    {
        $token = new NullToken();

        $this->assertFalse(
            $token->isPermittedFor('any'),
            'isPermittedFor should be still functional and give false'
        );
    }

    public function testIsRelatedTo(): void
    {
        $token = new NullToken();

        $this->assertFalse(
            $token->isRelatedTo('any'),
            'isRelatedTo should be still functional and give false'
        );
    }

    public function testIsIdentifiedBy(): void
    {
        $token = new NullToken();

        $this->assertFalse(
            $token->isIdentifiedBy('any'),
            'isIdentifiedBy should be still functional and give false'
        );
    }

    public function testHasBeenIssuedBy(): void
    {
        $token = new NullToken();

        $this->assertFalse(
            $token->hasBeenIssuedBy('any'),
            'hasBeenIssuedBy should be still functional and give false'
        );
    }

    public function testHasBeenIssuedBefore(): void
    {
        $time   = new DateTime('1900-01-01');
        $token  = new NullToken();

        $this->assertTrue(
            $token->hasBeenIssuedBefore($time),
            'hasBeenIssuedBefore should be still functional and give true on any old date'
        );
    }

    public function testIsMinimumTimeBefore(): void
    {
        $time   = new DateTime('1900-01-01');
        $token  = new NullToken();

        $this->assertTrue(
            $token->isMinimumTimeBefore($time),
            'isMinimumTimeBefore should be still functional and give true to any old time'
        );
    }
}
