<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Tests\Unit\Framework;

use Exception;
use Lcobucci\JWT\Token;
use OxidEsales\GraphQL\Base\Exception\InvalidToken;
use OxidEsales\GraphQL\Base\Framework\RequestReader;
use OxidEsales\GraphQL\Base\Infrastructure\Legacy;
use OxidEsales\GraphQL\Base\Service\JwtConfigurationBuilder;
use OxidEsales\GraphQL\Base\Tests\Unit\BaseTestCase;

class TokenTest extends BaseTestCase
{
    // TODO: write tests for token validation in the service
}
