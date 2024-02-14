<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Event;

use Lcobucci\JWT\Token;
use Symfony\Contracts\EventDispatcher\Event;

class BeforeAuthorization extends Event
{
    private ?bool $authorized = null;

    public function __construct(
        private readonly ?Token $token,
        private readonly string $right
    ) {
    }

    public function getToken(): ?Token
    {
        return $this->token;
    }

    public function getRight(): string
    {
        return $this->right;
    }

    public function setAuthorized(?bool $flag = null): void
    {
        $this->authorized = $flag;
    }

    public function getAuthorized(): ?bool
    {
        return $this->authorized;
    }
}
