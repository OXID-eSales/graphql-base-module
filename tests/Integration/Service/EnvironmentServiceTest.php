<?php declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\GraphQl\Tests\Integration\Service;

use OxidEsales\GraphQl\Service\EnvironmentService;
use PHPUnit\Framework\TestCase;

class EnvironmentServiceTest extends TestCase
{
    /** @var  EnvironmentService $environmentService */
    private $environmentService;

    public function setUp()
    {
        $this->environmentService = new EnvironmentService();
        parent::setUp();

    }

    public function testGetShopUrl()
    {
        $shopUrl = $this->environmentService->getShopUrl();
        $this->assertEquals(1, preg_match('/^https?:\/\/.+/', $shopUrl));
    }

    public function testGetDefaultLanguage()
    {
        $defaultLanguage = $this->environmentService->getDefaultLanguage();
        $this->assertEquals(1, preg_match('/^[a-z]{2,2}$/', $defaultLanguage));
    }

    public function testGetDefaultShopId()
    {
        $defaultShopId = $this->environmentService->getDefaultShopId();
        $this->assertEquals(1, $defaultShopId);
    }
}
