<?php declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\GraphQl\Tests\Unit\Type;

use OxidEsales\GraphQl\Dao\UserDaoInterface;
use OxidEsales\GraphQl\DataObject\Address;
use OxidEsales\GraphQl\DataObject\User;
use OxidEsales\GraphQl\Framework\SchemaFactory;
use OxidEsales\GraphQl\Service\UserService;
use OxidEsales\GraphQl\Service\UserServiceInterface;
use OxidEsales\GraphQl\Type\Provider\UserMutationProvider;
use OxidEsales\GraphQl\Utility\AuthConstants;
use OxidEsales\GraphQl\Utility\LegacyWrapperInterface;
use PHPUnit\Framework\MockObject\MockObject;

class UserTypeTest extends BaseTestType
{

    /** @var  UserDaoInterface|MockObject */
    private $userDao;

    /** @var  UserServiceInterface $userService */
    private $userService;

    /** @var  User $user */
    private $user;

    /** @var  int $shopId */
    private $shopId;

    public function setUp()
    {
        parent::setUp();

        /** @var LegacyWrapperInterface|MockObject $legacyWrapper */
        $legacyWrapper = $this->getMockBuilder(LegacyWrapperInterface::class)->getMock();
        $legacyWrapper->method('encodePassword')->willReturn('somepasswordhash');
        $legacyWrapper->method('createSalt')->willReturn('somesalt');
        $this->userDao = $this->getMockBuilder(UserDaoInterface::class)->getMock();
        $this->userService = new UserService($this->userDao, $legacyWrapper);
        $userMutationProvider = new UserMutationProvider($this->userService, $this->permissionsService);

        $schemaFactory = new SchemaFactory();
        $schemaFactory->addMutationProvider($userMutationProvider);

        $this->schema = $schemaFactory->getSchema();
    }

    public function saveUser(User $user)
    {
        $this->user = $user;
        $this->user->setId('some_new_id');
        $this->shopId = $user->getShopid();
    }

    public function updateUser(User $user)
    {
        $this->user = $user;
        $this->shopId = $user->getShopid();
    }

    public function testCreateUser()
    {
        $group = AuthConstants::USER_GROUP_CUSTOMER;
        $query = <<<EOQ
mutation CreateUser {
    createUser (user: {username: "someuser", 
                       firstname: "firstname",
                       password: "password",
                       usergroup: "$group",
                       address: {
                                 street: "somestreet"
                                }
                      }) {
        id,
        address {
            street
        }
    }
}
EOQ;

        $this->addPermission('somegroup', 'maycreatecustomer');

        $this->userDao->method('saveOrUpdateUser')->willReturnCallback([$this, 'saveUser']);
        $this->userDao->method('getUserByName')->willReturnCallback(function() { return $this->user;});

        $result = $this->executeQuery($query);

        $userid = $result->data['createUser']['id'];

        $this->assertEquals('some_new_id', $userid, $result->errors[0]);
        $this->assertEquals('somestreet', $result->data['createUser']['address']['street'], $result->errors[0]);
        $this->assertEquals('somepasswordhash', $this->user->getPasswordhash());
        $this->assertEquals('somesalt', $this->user->getPasswordsalt());

    }

    public function testCreateUserMissingPermission()
    {
        $query = <<< EOQ
mutation CreateUser {
    createUser (user: {username: "someuser", 
                       firstname: "firstname",
                       password: "password",
                       usergroup: "admin",
                       address: {
                                 street: "somestreet"
                                }
                      }) {
        id,
        address {
            street
        }
    }
}
EOQ;

        $this->addPermission('somegroup', 'maycreatecustomer');

        $result = $this->executeQuery($query);
        $this->assertEquals('Missing Permission: User someuser does not have permission "maycreateanyuser"', $result->errors[0]->message);
    }

    public function testUpdateSameUser()
    {
        $query = <<< EOQ
mutation UpdateUser {
    updateUser (user: {id: "someid", 
                       firstname: "Paula",
                       address: {
                                 street: "otherstreet"
                                }
                      }) {
        id,
        address {
            street
        }
    }
}
EOQ;
        $this->user = new User();
        $address = new Address();
        $address->setStreet('somestreet');
        $this->user->setAddress($address);
        $this->user->setId("someid");

        $this->userDao->method('getUserById')->willReturn($this->user);
        $this->userDao->method('saveOrUpdateUser')->willReturnCallback([$this, 'updateUser']);

        $this->addPermission('somegroup', 'mayupdateself');

        $result = $this->executeQuery($query);

        $this->assertEquals(0, sizeof($result->errors));
        $this->assertEquals('otherstreet', $this->user->getAddress()->getStreet());
        $this->assertEquals('Paula', $this->user->getFirstname());

    }

    public function testUpdateOtherUser()
    {
        $query = <<< EOQ
mutation UpdateUser {
    updateUser (user: {id: "otherid", 
                       firstname: "Paula",
                       address: {
                                 street: "otherstreet"
                                }
                      }) {
        id,
        address {
            street
        }
    }
}
EOQ;
        $this->user = new User();
        $address = new Address();
        $address->setStreet('somestreet');
        $this->user->setAddress($address);
        $this->user->setId("otherid");

        $this->userDao->method('getUserById')->willReturn($this->user);
        $this->userDao->method('saveOrUpdateUser')->willReturnCallback([$this, 'updateUser']);

        $this->addPermission('somegroup', 'mayupdateanyuser');

        $result = $this->executeQuery($query);

        $this->assertEquals(0, sizeof($result->errors));
        $this->assertEquals('otherstreet', $this->user->getAddress()->getStreet());
        $this->assertEquals('Paula', $this->user->getFirstname());

    }

    public function testUpdateOtherUserMissingPermission()
    {
        $query = <<< EOQ
mutation UpdateUser {
    updateUser (user: {id: "otherid", 
                       firstname: "Paula",
                       address: {
                                 street: "otherstreet"
                                }
                      }) {
        id,
        address {
            street
        }
    }
}
EOQ;
        $this->user = new User();
        $address = new Address();
        $address->setStreet('somestreet');
        $this->user->setAddress($address);
        $this->user->setId("otherid");

        $this->userDao->method('getUserById')->willReturn($this->user);
        $this->userDao->method('saveOrUpdateUser')->willReturnCallback([$this, 'updateUser']);

        $this->addPermission('somegroup', 'mayupdateself');

        $result = $this->executeQuery($query);

        $this->assertEquals(1, sizeof($result->errors));
        $this->assertEquals('Missing Permission: User someuser does not have permission "mayupdateanyuser"',
            $result->errors[0]->message);

    }

    public function testUpdatePassword()
    {
        $query = <<< EOQ
mutation UpdateUser {
    updateUser (user: {id: "someid", 
                       password: "mynewpassword",
                      }) {
        id
    }
}
EOQ;
        $this->user = new User();
        $this->user->setId("someid");

        $this->userDao->method('getUserById')->willReturn($this->user);
        $this->userDao->method('saveOrUpdateUser')->willReturnCallback([$this, 'updateUser']);

        $this->addPermission('somegroup', 'mayupdateself');

        $this->executeQuery($query);

        $this->assertEquals('somepasswordhash', $this->user->getPasswordhash());
        $this->assertEquals('somesalt', $this->user->getPasswordsalt());
    }
}
