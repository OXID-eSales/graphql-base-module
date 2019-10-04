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
use OxidEsales\GraphQl\DataObject\TokenRequest;
use OxidEsales\GraphQl\DataObject\User;
use OxidEsales\GraphQl\Exception\ObjectNotFoundException;
use OxidEsales\GraphQl\Exception\PasswordMismatchException;
use OxidEsales\GraphQl\Utility\AuthConstants;
use OxidEsales\GraphQl\Utility\LegacyWrapperInterface;

class UserDao implements UserDaoInterface
{

    /** @var QueryBuilderFactoryInterface $queryBuilderFactory */
    private $queryBuilderFactory;
    /** @var PasswordServiceBridgeInterface $passwordService */
    private $passwordService;
    /** @var LegacyWrapperInterface $legacyWrapper */
    private $legacyWrapper;

    public function __construct(
        QueryBuilderFactoryInterface $queryBuilderFactory,
        PasswordServiceBridgeInterface $passwordService,
        LegacyWrapperInterface $legacyWrapper)
    {
        $this->queryBuilderFactory = $queryBuilderFactory;
        $this->passwordService = $passwordService;
        $this->legacyWrapper = $legacyWrapper;
    }

    /**
     * Checks if there is a working user/password/shopid combination, otherwise
     * throw an exception. Then determine
     * the user id and group and add them to the token request.
     *
     * This does not exactly honors the Single-Responsibility-Principle, but
     * we want can do this in one database request, so we mingle some responsibilities.
     *
     * If not, an exception is thrown. If yes, the group the user
     * belongs to is returned.
     *
     * TODO: Improve the user group mechanism
     *
     * @param TokenRequest $tokenRequest
     *
     * @return TokenRequest
     * @throws PasswordMismatchException
     * @throws ObjectNotFoundException
     */
    public function addIdAndUserGroupToTokenRequest(TokenRequest $tokenRequest): TokenRequest
    {
        $queryBuilder = $this->queryBuilderFactory->create();
        $queryBuilder->select('OXID', 'OXRIGHTS', 'OXPASSWORD')
            ->from('oxuser')
            ->where(
                $queryBuilder->expr()->andX(
                    $queryBuilder->expr()->eq('OXUSERNAME', ':name'),
                    $queryBuilder->expr()->eq('OXSHOPID', ':shopid')
                )
            )
            ->setParameter('name', $tokenRequest->getUsername())
            ->setParameter('shopid', $tokenRequest->getShopid());


        $result = $queryBuilder->execute()->fetch();
        if (!$result) {
            throw new PasswordMismatchException('User/password combination is not valid.');
        }
        if (! $this->passwordService->verifyPassword($tokenRequest->getPassword(), $result['OXPASSWORD'])) {
            throw new PasswordMismatchException('User/password combination is not valid.');
        };

        $tokenRequest->setUserid($result['OXID']);
        $tokenRequest->setGroup($this->mapGroup($result['OXRIGHTS']));
        return $tokenRequest;
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

    public function saveOrUpdateUser(User $user)
    {
        if ($user->getId()) {
            $this->updateUser($user);
        } else {
            $this->saveUser($user);
        }
    }

    private function updateUser(User $user)
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

    private function saveUser(User $user)
    {
        $queryBuilder = $this->queryBuilderFactory->create();
        $values = [
            'OXID' => ':id',
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
            'OXSHOPID'    => ':shopid'];
        $parameters = $this->mapUserToArray($user);
        $parameters['id'] = $this->legacyWrapper->createUid();
        $queryBuilder->insert('oxuser')->values($values)
            ->setParameters($parameters)->execute();
    }

    private function fetchCountryIdFromShortcut($countryShortcut)
    {
        $queryBuilder = $this->queryBuilderFactory->create();
        $queryBuilder->select('c.OXID')
            ->from('oxcountry', 'c')
            ->where($queryBuilder->expr()->eq('c.OXISOALPHA2', ':shortcut'))
            ->setParameter('shortcut', strtoupper($countryShortcut));
        $result = $queryBuilder->execute()->fetch();

        if (!$result) {
            throw new ObjectNotFoundException("Did not find country with shortcut \"$countryShortcut\"");
        }

        return $result['OXID'];
    }

    private function executeQuery(QueryBuilder $queryBuilder)
    {
        $result = $queryBuilder->execute()->fetch();

        if (!$result) {
            throw new ObjectNotFoundException('Could not find requested user.');
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
        $user = new User();
        $user->setId($result['OXID']);
        $user->setShopid($result['OXSHOPID']);
        $user->setUsername($result['OXUSERNAME']);
        $user->setPasswordhash($result['OXPASSWORD']);
        $user->setFirstname($result['OXFNAME']);
        $user->setLastname($result['OXLNAME']);
        $user->setUsergroup($this->mapGroup($result['OXRIGHTS']));
        $address = new Address();
        $address->setStreet($result['OXSTREET']);
        $address->setStreetnr($result['OXSTREETNR']);
        $address->setAdditionalinfo($result['OXADDINFO']);
        $address->setCity($result['OXCITY']);
        $address->setZip($result['OXZIP']);
        $address->setCountryshortcut($result['OXCOUNTRYSHORTCUT']);
        $user->setAddress($address);

        return $user;
    }

    private function mapUserToArray(User $user)
    {
        $address = $user->getAddress();

        return [
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
            'countryid'      => $this->fetchCountryIdFromShortcut($address->getCountryshortcut()),
            'shopid'         => $user->getShopid()];
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
