<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Framework;

use DateTimeInterface;
use Lcobucci\JWT\Token\DataSet;
use Lcobucci\JWT\Token\RegisteredClaims;
use Lcobucci\JWT\Token\Signature;
use Lcobucci\JWT\UnencryptedToken;

class NullToken implements UnencryptedToken
{
    private $headers;

    private $claims;

    private $signature;

    public function __construct()
    {
        $this->headers   = new DataSet([], '');
        $this->claims    = new DataSet([], '');
        $this->signature = Signature::fromEmptyData();
    }

    public function headers(): DataSet
    {
        return $this->headers;
    }

    public function claims(): DataSet
    {
        return $this->claims;
    }

    public function signature(): Signature
    {
        return $this->signature;
    }

    public function payload(): string
    {
        return $this->headers->toString() . '.' . $this->claims->toString();
    }

    public function isPermittedFor(string $audience): bool
    {
        return in_array($audience, $this->claims->get(RegisteredClaims::AUDIENCE, []), true);
    }

    public function isIdentifiedBy(string $id): bool
    {
        return $this->claims->get(RegisteredClaims::ID) === $id;
    }

    public function isRelatedTo(string $subject): bool
    {
        return $this->claims->get(RegisteredClaims::SUBJECT) === $subject;
    }

    public function hasBeenIssuedBy(string ...$issuers): bool
    {
        return in_array($this->claims->get(RegisteredClaims::ISSUER), $issuers, true);
    }

    public function hasBeenIssuedBefore(DateTimeInterface $time): bool
    {
        return $this->claims->get(RegisteredClaims::ISSUED_AT) <= $time;
    }

    public function isMinimumTimeBefore(DateTimeInterface $time): bool
    {
        return $this->claims->get(RegisteredClaims::NOT_BEFORE) < $time;
    }

    public function isExpired(DateTimeInterface $now): bool
    {
        return false;
    }

    public function toString(): string
    {
        return $this->headers->toString() . '.'
            . $this->claims->toString() . '.'
            . $this->signature->toString();
    }
}
