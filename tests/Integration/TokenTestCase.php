<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Tests\Integration;

abstract class TokenTestCase extends TestCase
{
    /** @var string */
    protected const ADMIN_USER = 'admin';

    /** @var string */
    protected const ADMIN_PASS = 'admin';

    /** @var array */
    private static $token = [];

    public function prepareToken(?string $username = null, ?string $password = null): void
    {
        $username = $username ?: self::ADMIN_USER;
        $password = $password ?: self::ADMIN_PASS;
        $key      = $username . $password . $_GET['shp'];

        if (!isset(self::$token[$key])) {
            self::$token[$key] = $this->query('query {
                token (
                    username: "' . $username . '",
                    password: "' . $password . '"
                )
            }')['body']['data']['token'];
        }
        $this->setAuthToken(self::$token[$key]);
    }
}
