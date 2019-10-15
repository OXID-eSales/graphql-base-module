<?php declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\GraphQl\Dao;

use Doctrine\DBAL\Query\QueryBuilder;
use OxidEsales\EshopCommunity\Internal\Domain\Authentication\Bridge\PasswordServiceBridgeInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Database\QueryBuilderFactoryInterface;
use OxidEsales\GraphQl\DataObject\Address;
use OxidEsales\GraphQl\DataObject\User;
use OxidEsales\GraphQl\Utility\AuthConstants;

class UserDao implements UserDaoInterface
{

    /** @var QueryBuilderFactoryInterface $queryBuilderFactory */
    private $queryBuilderFactory;
    /** @var PasswordServiceBridgeInterface $passwordService */
    private $passwordService;

    public function __construct(
        QueryBuilderFactoryInterface $queryBuilderFactory,
        PasswordServiceBridgeInterface $passwordService
    ) {
        $this->queryBuilderFactory = $queryBuilderFactory;
        $this->passwordService = $passwordService;
    }

    public function getUserById(string $userid): User
    {
        $queryBuilder = $this->queryBuilderFactory->create();
        $this->configureBaseQuery($queryBuilder);
        $queryBuilder->where($queryBuilder->expr()->eq('u.OXID', ':id'))
            ->setParameter('id', $userid);

        return $this->executeQuery($queryBuilder);
    }

    public function getUserByName(string $username, int $shopid): User
    {
        $queryBuilder = $this->queryBuilderFactory->create();
        $this->configureBaseQuery($queryBuilder);
        $queryBuilder->where(
            $queryBuilder->expr()->andX(
                $queryBuilder->expr()->eq('u.OXUSERNAME', ':username'),
                $this->getShopIdExpression($queryBuilder)
            )
        )
            ->setParameter('username', $username)
            ->setParameter('shopid', $shopid);

        return $this->executeQuery($queryBuilder);
    }

    private function getShopIdExpression(QueryBuilder $queryBuilder)
    {
        $queryBuilder->setParameter('mallright', 'mall%');

        return $queryBuilder->expr()->orX(
            $queryBuilder->expr()->eq('u.OXSHOPID', ':shopid'),
            $queryBuilder->expr()->like('u.OXRIGHTS', ':mallright')
        );
    }

    public function updateUser(User $user): void
    {
        $queryBuilder = $this->queryBuilderFactory->create();
        $queryBuilder
            ->update('oxuser', 'u')
            ->set('u.OXUSERNAME', ':username')
            ->set('u.OXPASSWORD', ':passwordhash')
            ->set('u.OXFNAME', ':firstname')
            ->set('u.OXLNAME', ':lastname')
            ->set('u.OXRIGHTS', ':rights')
            ->set('u.OXSTREET', ':street')
            ->set('u.OXSTREETNR', ':streetnr')
            ->set('u.OXADDINFO', ':additionalinfo')
            ->set('u.OXCITY', ':city')
            ->set('u.OXZIP', ':zip')
            ->set('u.OXCOUNTRYID', ':countryid')
            ->setParameters($this->mapUserToArray($user))
            ->where($queryBuilder->expr()->eq('u.OXID', ':id'))
            ->setParameter('id', $user->getId())
            ->execute();
    }

    public function saveUser(User $user): void
    {
        $queryBuilder = $this->queryBuilderFactory->create();
        $values = [
            'OXID'        => ':id',
            'OXUSERNAME'  => ':username',
            'OXPASSWORD'  => ':passwordhash',
            'OXFNAME'     => ':firstname',
            'OXLNAME'     => ':lastname',
            'OXRIGHTS'    => ':rights',
            'OXSTREET'    => ':street',
            'OXSTREETNR'  => ':streetnr',
            'OXADDINFO'   => ':additionalinfo',
            'OXCITY'      => ':city',
            'OXZIP'       => ':zip',
            'OXCOUNTRYID' => ':countryid',
            'OXSHOPID'    => ':shopid'
        ];
        $parameters = $this->mapUserToArray($user);
        $parameters['id'] = $user->getId();
        $queryBuilder->insert('oxuser')
                     ->values($values)
                     ->setParameters($parameters)
                     ->execute();
    }

    private function fetchCountryIdFromShortcut(string $countryShortcut)
    {
        $queryBuilder = $this->queryBuilderFactory->create();
        $queryBuilder->select('c.OXID')
            ->from('oxcountry', 'c')
            ->where($queryBuilder->expr()->eq('c.OXISOALPHA2', ':shortcut'))
            ->setParameter('shortcut', strtoupper($countryShortcut));
        $result = $queryBuilder->execute()->fetch();

        if (!$result) {
            return null;
        }

        return $result['OXID'];
    }

    private function executeQuery(QueryBuilder $queryBuilder)
    {
        $result = $queryBuilder->execute()->fetch();

        if (!$result) {
            return null;
        }

        return $this->mapUserFromDatabaseResult($result);
    }

    private function configureBaseQuery(QueryBuilder $queryBuilder)
    {
        $queryBuilder->select('u.*, c.OXISOALPHA2 as OXCOUNTRYSHORTCUT')
            ->from('oxuser', 'u')
            ->leftJoin('u', 'oxcountry', 'c', 'u.OXCOUNTRYID=c.OXID');
    }

    private function mapUserFromDatabaseResult($result)
    {
        $address = new Address(
            $result['OXSTREET'],
            $result['OXSTREETNR'],
            $result['OXADDINFO'],
            $result['OXCITY'],
            $result['OXZIP'],
            $result['OXCOUNTRYSHORTCUT']
        );
        # $address->setCountryshortcut($result['OXCOUNTRYSHORTCUT']);
 
        $user = new User(
            $result['OXID'],
            (int)$result['OXSHOPID'],
            $result['OXUSERNAME'],
            $result['OXPASSWORD'],
            $result['OXFNAME'],
            $result['OXLNAME'],
            $this->mapGroup($result['OXRIGHTS']),
            $address
        );

        return $user;
    }

    private function mapUserToArray(User $user): array
    {
        $address = $user->getAddress();
        if ($address === null) {
            $address = new Address;
        }

        $ret = [
            'username'       => $user->getUsername(),
            'passwordhash'   => $user->getPasswordhash(),
            'firstname'      => $user->getFirstname(),
            'lastname'       => $user->getLastname(),
            'rights'         => $this->unmapGroup($user),
            'street'         => $address->getStreet(),
            'streetnr'       => $address->getStreetnr(),
            'additionalinfo' => $address->getAdditionalinfo(),
            'city'           => $address->getCity(),
            'zip'            => $address->getZip(),
            'countryid'      => $address->getCountryshortcut() ? $this->fetchCountryIdFromShortcut($address->getCountryshortcut()) : '',
            'shopid'         => $user->getShopid()
        ];

        return $ret;
    }

    private function mapGroup(string $oxrights): string
    {
        if ($oxrights === 'user') {
            return AuthConstants::USER_GROUP_CUSTOMER;
        }
        if ($oxrights === 'malladmin') {
            return AuthConstants::USER_GROUP_ADMIN;
        }
        if (preg_match("/^\d+$/", $oxrights)) {
            return AuthConstants::USER_GROUP_SHOPADMIN;
        }
        throw new \Exception("Can't map oxrights \"$oxrights\" to any known usergroup.");
    }

    private function unmapGroup(User $user): string
    {
        $usergroup = $user->getUsergroup();
        if ($usergroup === AuthConstants::USER_GROUP_CUSTOMER) {
            return 'user';
        }
        if ($usergroup === AuthConstants::USER_GROUP_ADMIN) {
            return 'malladmin';
        }
        if ($usergroup === AuthConstants::USER_GROUP_SHOPADMIN) {
            return "" . $user->getShopid();
        }
        throw new \Exception("Can't map usergroup \"$usergroup\" to any known oxright.");
    }
}
