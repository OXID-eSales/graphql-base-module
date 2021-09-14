<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Service;

use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Hmac\Sha512;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Validation\Constraint\IssuedBy;
use Lcobucci\JWT\Validation\Constraint\PermittedFor;
use Lcobucci\JWT\Validation\Constraint\SignedWith;
use OxidEsales\GraphQL\Base\Infrastructure\Legacy as LegacyService;

class JwtConfigurationBuilder
{
    /** @var KeyRegistry */
    private $keyRegistry;

    /** @var LegacyService */
    private $legacyService;

    public function __construct(
        KeyRegistry $keyRegistry,
        LegacyService $legacyService
    ) {
        $this->keyRegistry     = $keyRegistry;
        $this->legacyService   = $legacyService;
    }

    public function getConfiguration(): Configuration
    {
        $config = Configuration::forSymmetricSigner(
            new Sha512(),
            InMemory::plainText($this->keyRegistry->getSignatureKey())
        );

        $issuedBy     = new IssuedBy($this->legacyService->getShopUrl());
        $permittedFor = new PermittedFor($this->legacyService->getShopUrl());
        $signedWith   = new SignedWith($config->signer(), $config->verificationKey());
        $config->setValidationConstraints($issuedBy, $permittedFor, $signedWith);

        return $config;
    }
}
