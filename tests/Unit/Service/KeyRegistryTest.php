<?php declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\GraphQL\Tests\Unit\Service;

use PHPUnit\Framework\TestCase;
use OxidEsales\EshopCommunity\Tests\Integration\Internal\TestContainerFactory;
use OxidEsales\GraphQL\Exception\NoSignatureKeyException;
use OxidEsales\GraphQL\Service\KeyRegistryInterface;
use OxidEsales\GraphQL\Service\KeyRegistry;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\Config;

class KeyRegistryTest extends TestCase
{

    protected static $container = null;

    protected static $keyRegistry = null;

    /**
     * this empty methods prevents phpunit from resetting
     * invocation mocker and therefore we can use the same
     * mocks for all tests and do not need to reinitialize
     * the container for every test in this file which
     * makes the whole thing pretty fast :-)
     */
    protected function verifyMockObjects()
    {
    }

    public function setUp(): void
    {
        if (self::$container !== null) {
            return;
        }

        $containerFactory = new TestContainerFactory();
        self::$container = $containerFactory->create();

        self::$container->compile();

        self::$keyRegistry = self::$container->get(KeyRegistryInterface::class);
    }

    public function tearDown(): void
    {
        Registry::set(Config::class, null);
    }

    public function testGenerateSignatureKeyCreatesRandom64BytesKeys()
    {
        $iterations = 5;
        $keys = [];
        for ($i = 0; $i < $iterations; $i ++){
            $key = self::$keyRegistry->generateSignatureKey();
            $this->assertGreaterThanOrEqual(
                64,
                strlen($key),
                'Signature key needs to be at least 64 chars, '.strlen($key).' chars given'
            );
            $this->assertTrue(is_string($key), 'Signature key needs to be a string');
            $keys[] = $key;
        }
        array_unique($keys);
        $this->assertEquals(
            $iterations,
            count($keys),
            'All signature keys need to be random'
        );
    }

    public function signatureKeyProvider(): array
    {
        return [
            [true, false],
            [null, false],
            [false, false],
            [new \stdClass(), false],
            ['', false],
            ['too short', false],
            [[], false],
            ['a8sohflkashdflkjashdfkjashdfkljahsdlkfjhaskldjfhakjlsdfhlkjasdhflkajshdflkajsdhflkjashdflkjashdlfkjahsldkfjhalkjdsfasdf', true]
        ];
    }
    
    /**
     * @dataProvider signatureKeyProvider
     */
    public function testGetSignatureKeyWithInvalidOrNoSignature($signature, bool $valid)
    {
        $oldConfig = Registry::getConfig();
        $config = $this->getMockBuilder(Config::class)->getMock();
        $config->method('getConfigParam')
               ->with(KeyRegistry::signatureKeyName)
               ->willReturn($signature);
        Registry::set(Config::class, $config);
        $e = null;
        $sig = null;
        try {
            $sig = self::$keyRegistry->getSignatureKey();
        } catch (NoSignatureKeyException $e) {
        }
        if ($valid) {
            $this->assertEquals(null, $e);
            $this->assertTrue(is_string($sig), 'Signature key needs to be a string');
        } else {
            $this->assertInstanceOf(
                NoSignatureKeyException::class,
                $e
            );
        }
        Registry::set(Config::class, $oldConfig);
    }

}
