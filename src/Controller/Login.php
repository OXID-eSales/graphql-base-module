<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Controller;

use DateTimeInterface;
use OxidEsales\GraphQL\Base\Service\Authentication;
use TheCodingMachine\GraphQLite\Annotations\Query;
use TheCodingMachine\GraphQLite\Annotations\Security;
use TheCodingMachine\GraphQLite\Types\ID;

class Login
{
    /** @var Authentication */
    protected $authentication;

    public function __construct(
        Authentication $authentication
    ) {
        $this->authentication = $authentication;
    }

    /**
     * retrieve a JWT for authentication of further requests
     *
     * @Query
     */
    public function token(?string $username = null, ?string $password = null): string
    {
        return (string) $this->authentication->createToken(
            $username,
            $password
        );
    }

    /**
     * @Query
     * @Security("is_granted('PRINT_STRING', stringToPrint)")
     */
    public function printString(string $stringToPrint): string
    {
        return $stringToPrint;
    }

    /**
     * @Query()
     * @Security("is_granted('PRINT_DATE', basketID)")
     */
    public function printDate(ID $basketID): string
    {
        return 'asd';
    }
}
