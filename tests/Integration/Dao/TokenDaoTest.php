<?php declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\GraphQl\Tests\Integration\Dao;

use OxidEsales\EshopCommunity\Tests\Integration\Internal\TestContainerFactory;
use OxidEsales\GraphQl\Dao\TokenDao;
use OxidEsales\GraphQl\Dao\TokenDaoInterface;
use OxidEsales\GraphQl\DataObject\Token;
use OxidEsales\GraphQl\Service\KeyRegistryInterface;
use OxidEsales\TestingLibrary\UnitTestCase;

class TokenDaoTest extends UnitTestCase
{

    /** @var  TokenDao */
    private $tokenDao;

    /** @var  KeyRegistryInterface */
    private $keyRegistry;

    public function setUp()
    {
        parent::setUp();

        $containerFactory = new TestContainerFactory();
        $container = $containerFactory->create();
        $container->compile();
        $this->tokenDao = $container->get(TokenDaoInterface::class);
        $this->keyRegistry = $container->get(KeyRegistryInterface::class);
    }

    public function testSaveToken()
    {
        $token = new Token($this->keyRegistry->getSignatureKey());
        $token->setSubject('someuser');
        $token->setLang('de');
        $token->setShopid(1);
        $token->setShopUrl("https://localhost");
        $token->setUserGroup('customers');

        $this->tokenDao->saveOrUpdateToken($token);

        $loadedToken = $this->tokenDao->loadToken('someuser', 1);

        $this->assertEquals($token->getJwt(), $loadedToken->getJwt());

    }

    public function testUpdateToken()
    {
        $token = new Token($this->keyRegistry->getSignatureKey());
        $token->setSubject('someuser');
        $token->setLang('de');
        $token->setShopid(1);
        $token->setShopUrl("https://localhost");
        $token->setUserGroup('customers');

        $this->tokenDao->saveOrUpdateToken($token);

        $token->setLang('en');

        $this->tokenDao->saveOrUpdateToken($token);

        $loadedToken = $this->tokenDao->loadToken('someuser', 1);

        $this->assertEquals('en', $loadedToken->getLang());

    }
}
