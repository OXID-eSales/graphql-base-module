<?php declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\GraphQl\Service;

use OxidEsales\GraphQl\DataObject\TokenRequest;

interface AuthenticationServiceInterface
{
    /**
     * @param TokenRequest $tokenRequest
     *
     * @return string
     */
    public function getToken(TokenRequest $tokenRequest);

}