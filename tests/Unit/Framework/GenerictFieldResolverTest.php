<?php declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\GraphQl\Tests\Unit\Framework;

use OxidEsales\GraphQl\DataObject\User;
use OxidEsales\GraphQl\Exception\NoSuchGetterException;
use OxidEsales\GraphQl\Framework\GenericFieldResolver;
use PHPUnit\Framework\TestCase;

class GenerictFieldResolverTest extends TestCase
{
    /** @var  GenericFieldResolver */
    private $genericFieldResolver;

    public function setUp()
    {
        $this->genericFieldResolver = new GenericFieldResolver();
    }

    public function testGetterWorking() {

        $dataObject = new User();

        $dataObject->setUsername("testuser");

        $this->assertEquals('testuser', $this->genericFieldResolver->getField('username', $dataObject));

    }

    public function testUnknownGetter() {

        $this->expectException(NoSuchGetterException::class);
        $this->expectExceptionMessage('Can\'t resolve field with name "nonexistingfield".');

        $dataObject = new User();

        $this->assertEquals('testuser', $this->genericFieldResolver->getField('nonexistingfield', $dataObject));

    }

}
