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

    public function verifyPassword(string $username, string $password): string
    {
        $queryBuilder = $this->queryBuilderFactory->create();
        $queryBuilder->select('oxid', 'oxpassword', 'oxpasssalt')
            ->from('oxuser')
            ->where($queryBuilder->expr()->eq('oxusername', ':name'))
            ->setParameter('name', $username);
        $result = $queryBuilder->execute()->fetch();
        if (! $result) {
            throw new UserNotFoundException();
        }
        $storedHashedPassword = $result['oxpassword'];
        $providedHashedPassword = $this->passwordEncoder->encodePassword($password, $result['oxpasssalt']);
        if ($storedHashedPassword !== $providedHashedPassword) {
            throw new PasswordMismatchException();
        }
        return $result['oxid'];
    }
}
