<?php declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\GraphQl\Tests\Unit\Type;

use GraphQL\GraphQL;
use GraphQL\Type\Schema;
use OxidEsales\GraphQl\DataObject\Token;
use OxidEsales\GraphQl\Framework\AppContext;
use OxidEsales\GraphQl\Service\PermissionsProvider;
use OxidEsales\GraphQl\Service\PermissionsService;
use PHPUnit\Framework\TestCase;

class GraphQlTypeTestCase extends \PHPUnit_Framework_TestCase
{

    const DEFAULTGROUP = 'somegroup';

    /** @var  Schema */
    protected $schema;

    /** @var  PermissionsService */
    protected $permissionsService;

    /** @var  Token */
    protected $token;

    public function setUp()
    {
        $this->permissionsService = new PermissionsService();
        $this->token = $this->createDefaultToken();

    }

    protected function addPermission(string $group, string $permission)
    {
        $permissionsProvider = new PermissionsProvider();
        $permissionsProvider->addPermission($group, $permission);
        $this->permissionsService->addPermissionsProvider($permissionsProvider);

    }

    protected function executeQuery($query, $context=null)
    {
        $graphQl = new GraphQL();
        $context = $context ? $context : $this->createDefaultContext();
        return $graphQl->executeQuery(
            $this->schema,
            $query,
            null,
            $context
        );

    }

    protected function createDefaultContext()
    {
        $context = new AppContext();
        $context->setDefaultShopLanguage('de');
        $context->setDefaultShopId(1);
        $context->setAuthToken($this->createDefaultToken());
        return $context;
    }

    protected function createDefaultToken()
    {
        $token = new Token();
        $token->setSubject('someid');
        $token->setUsername('someuser');
        $token->setUserGroup($this::DEFAULTGROUP);
        $token->setKey('initialtokenkey');
        $token->setLang('de');
        $token->setShopid(1);
        $token->setShopUrl('http://somethingorother.com');

        return $token;
    }

}
