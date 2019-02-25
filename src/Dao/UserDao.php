<?php declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\GraphQl\Dao;

use OxidEsales\EshopCommunity\Internal\Common\Database\QueryBuilderFactoryInterface;
use OxidEsales\GraphQl\Exception\PasswordMismatchException;
use OxidEsales\GraphQl\Exception\UserNotFoundException;
use OxidEsales\GraphQl\Service\PasswordEncoderInterface;

class UserDao implements UserDaoInterface
{
    /** @var QueryBuilderFactoryInterface $queryBuilderFactory */
    private $queryBuilderFactory;
    /** @var PasswordEncoderInterface $passwordEncoder */
    private $passwordEncoder;

    public function __construct(
        QueryBuilderFactoryInterface $queryBuilderFactory,
        PasswordEncoderInterface $passwordEncoder)
    {
        $this->queryBuilderFactory = $queryBuilderFactory;
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * Checks if there is a working user/password/shopid combination
     * and determines the user group.
     *
     * If not, an exception is thrown. If yes, the group the user
     * belongs to is returned.
     *
     * TODO: Improve the user group mechanism
     *
     * @param string $username
     * @param string $password
     * @param int    $shopid
     *
     * @return string
     * @throws PasswordMismatchException
     * @throws UserNotFoundException
     */
    public function fetchUserGroup(string $username, string $password, int $shopid): string
    {
        $queryBuilder = $this->queryBuilderFactory->create();
        $queryBuilder->select('oxrights', 'oxpassword', 'oxpasssalt')
            ->from('oxuser')
            ->where(
                $queryBuilder->expr()->andX(
                    $queryBuilder->expr()->eq('oxusername', ':name'),
                    $queryBuilder->expr()->eq('oxshopid', ':shopid')
                )
            )
            ->setParameter('name', $username)
            ->setParameter('shopid', $shopid);
        $result = $queryBuilder->execute()->fetch();
        if (! $result) {
            throw new UserNotFoundException();
        }
        $storedHashedPassword = $result['oxpassword'];
        $providedHashedPassword = $this->passwordEncoder->encodePassword($password, $result['oxpasssalt']);
        if ($storedHashedPassword !== $providedHashedPassword) {
            throw new PasswordMismatchException();
        }
        if ($result['oxrights'] == 'malladmin') {
            return 'admin';
        }
        return 'customer';
    }
}
