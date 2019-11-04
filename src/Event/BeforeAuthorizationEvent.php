<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Event;

use Lcobucci\JWT\Token;
use Symfony\Component\EventDispatcher\Event;

class BeforeAuthorizationEvent extends Event
{
    public const NAME = self::class;

    /** @var Token */
    private $token = null;

    /** @var string */
    private $right = null;

    /** @var ?bool */
    private $authorized = null;

    public function __construct(
        Token $token,
        string $right
    ) {
        $this->token = $token;
        $this->right = $right;
    }

    public function getToken(): Token
    {
        return $this->token;
    }

    public function getRight(): string
    {
        return $this->right;
    }

    public function setAuthorized(bool $flag = null)
    {
        $this->authorized = $flag;
    }

    public function getAuthorized(): ?bool
    {
        return $this->authorized;
    }
}
