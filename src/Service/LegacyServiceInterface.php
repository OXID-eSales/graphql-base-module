<?php

namespace OxidEsales\GraphQL\Service;

use OxidEsales\GraphQL\Exception\InvalidLoginException;

interface LegacyServiceInterface
{
    const GROUP_ADMIN = 'admin';
    const GROUP_CUSTOMERS = 'customer';

    /**
     * @param string $username
     * @param string $password
     * @throws InvalidLoginException
     */
    public function checkCredentials(string $username, string $password);

    /**
     * @param string $username
     * @return string
     * @throws InvalidLoginException
     */
    public function getUserGroup(string $username): string;

    /**
     * @return string
     */
    public function getShopUrl(): string;

    /**
     * @return int
     */
    public function getShopId(): int;

}